<?php

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Enums\LoanType;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $analyst;
    protected User $marketing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->analyst = User::factory()->create([
            'name' => 'Hendra Analyst',
            'email' => 'analyst@cmd.co.id',
            'role' => 'analyst',
        ]);

        $this->marketing = User::factory()->create([
            'name' => 'Rahmat Marketing',
            'email' => 'marketing@cmd.co.id',
            'role' => 'marketing',
        ]);

        $this->actingAs($this->marketing);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        auth()->logout();

        $response = $this->get(route('loans.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_can_render_loans_dashboard_page(): void
    {
        LoanApplication::create([
            'customer_name' => 'Budi Pratama',
            'loan_type' => LoanType::MOTOR,
            'loan_amount' => 20000000,
            'tenor_months' => 12,
            'monthly_income' => 7000000,
            'monthly_installment' => 1846666.67,
            'interest_rate_monthly' => 0.0090,
            'status' => LoanStatus::PENDING,
        ]);

        $response = $this->get(route('loans.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Pengajuan Pembiayaan');
        $response->assertSee('Budi Pratama');
        $response->assertSee('Sepeda Motor');
    }

    public function test_analyst_user_sees_action_column(): void
    {
        $this->actingAs($this->analyst);

        $response = $this->get(route('loans.index'));
        $response->assertSee('<th scope="col" class="py-3.5 px-4 text-center">Aksi</th>', false);
    }

    public function test_marketing_user_does_not_see_action_column(): void
    {
        $this->actingAs($this->marketing);

        $response = $this->get(route('loans.index'));
        $response->assertDontSee('<th scope="col" class="py-3.5 px-4 text-center">Aksi</th>', false);
    }

    public function test_can_create_loan_application_with_valid_data(): void
    {
        $payload = [
            'customer_name' => 'Ahmad Dahlan',
            'loan_type' => 'mobil',
            'loan_amount' => '100.000.000',
            'tenor_months' => 24,
            'monthly_income' => '15.000.000',
            'notes' => 'Pembelian mobil keluarga',
        ];

        $response = $this->post(route('loans.store'), $payload);

        $response->assertRedirect(route('loans.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loan_applications', [
            'customer_name' => 'Ahmad Dahlan',
            'loan_type' => 'mobil',
            'loan_amount' => 100000000,
            'tenor_months' => 24,
            'status' => 'pending',
        ]);

        $loan = LoanApplication::where('customer_name', 'Ahmad Dahlan')->first();
        $this->assertNotNull($loan);
        $this->assertEquals(5066666.67, (float) $loan->monthly_installment);
    }

    /**
     * Behaviour 1: Ketika menambahkan data pengajuan baru dan pendapatan bulanan nasabah < 1 juta,
     * maka sistem akan menampilkan pesan error berikut: "Nasabah belum dapat mengajukan pinjaman"
     */
    public function test_behaviour_1_income_below_1_million_fails_with_exact_error_message(): void
    {
        $payload = [
            'customer_name' => 'Rian Kurniawan',
            'loan_type' => 'motor',
            'loan_amount' => '15.000.000',
            'tenor_months' => 12,
            'monthly_income' => '800.000', // Kurang dari 1 juta
        ];

        $response = $this->post(route('loans.store'), $payload);

        $response->assertSessionHasErrors([
            'monthly_income' => 'Nasabah belum dapat mengajukan pinjaman',
        ]);

        $this->assertDatabaseMissing('loan_applications', [
            'customer_name' => 'Rian Kurniawan',
        ]);
    }

    /**
     * Behaviour 2: Nominal maksimal pinjaman yang dapat disetujui adalah 200 juta.
     */
    public function test_behaviour_2_loan_amount_exceeding_200_million_fails(): void
    {
        $payload = [
            'customer_name' => 'Sultan Iskandar',
            'loan_type' => 'mobil',
            'loan_amount' => '250.000.000', // Melebihi 200 juta
            'tenor_months' => 24,
            'monthly_income' => '30.000.000',
        ];

        $response = $this->post(route('loans.store'), $payload);

        $response->assertSessionHasErrors(['loan_amount']);

        $this->assertDatabaseMissing('loan_applications', [
            'customer_name' => 'Sultan Iskandar',
        ]);
    }

    /**
     * Behaviour 3: Tenor pinjaman tertinggi adalah 24 bulan.
     */
    public function test_behaviour_3_tenor_exceeding_24_months_fails(): void
    {
        $payload = [
            'customer_name' => 'Ferry Santika',
            'loan_type' => 'multiguna',
            'loan_amount' => '50.000.000',
            'tenor_months' => 36, // Melebihi 24 bulan
            'monthly_income' => '10.000.000',
        ];

        $response = $this->post(route('loans.store'), $payload);

        $response->assertSessionHasErrors(['tenor_months']);

        $this->assertDatabaseMissing('loan_applications', [
            'customer_name' => 'Ferry Santika',
        ]);
    }

    /**
     * Behaviour 4: Maksimal pengajuan nasabah adalah sebanyak 3 kali.
     */
    public function test_behaviour_4_customer_cannot_apply_more_than_3_times(): void
    {
        $customerName = 'Bambang Trihatmodjo';

        for ($i = 1; $i <= 3; $i++) {
            LoanApplication::create([
                'customer_name' => $customerName,
                'loan_type' => LoanType::MOTOR,
                'loan_amount' => 15000000,
                'tenor_months' => 12,
                'monthly_income' => 5000000,
                'monthly_installment' => 1385000,
                'status' => LoanStatus::PENDING,
            ]);
        }

        $this->assertEquals(3, LoanApplication::where('customer_name', $customerName)->count());

        $payload = [
            'customer_name' => strtolower($customerName),
            'loan_type' => 'multiguna',
            'loan_amount' => '20.000.000',
            'tenor_months' => 12,
            'monthly_income' => '5.000.000',
        ];

        $response = $this->post(route('loans.store'), $payload);

        $response->assertSessionHasErrors(['customer_name']);
        $this->assertEquals(3, LoanApplication::where('customer_name', $customerName)->count());
    }

    public function test_can_approve_pending_loan(): void
    {
        $this->actingAs($this->analyst);

        $loan = LoanApplication::create([
            'customer_name' => 'Citra Kirana',
            'loan_type' => LoanType::MOBIL,
            'loan_amount' => 80000000,
            'tenor_months' => 24,
            'monthly_income' => 12000000,
            'monthly_installment' => 4053333.33,
            'status' => LoanStatus::PENDING,
        ]);

        $response = $this->post(route('loans.approve', $loan));

        $response->assertRedirect(route('loans.index'));
        $response->assertSessionHas('success');

        $loan->refresh();
        $this->assertTrue($loan->status->isApproved());
        $this->assertNotNull($loan->actioned_at);
    }

    public function test_can_reject_pending_loan_with_reason(): void
    {
        $this->actingAs($this->analyst);

        $loan = LoanApplication::create([
            'customer_name' => 'Doni Salman',
            'loan_type' => LoanType::MULTIGUNA,
            'loan_amount' => 70000000,
            'tenor_months' => 18,
            'monthly_income' => 4000000,
            'monthly_installment' => 4518888.89,
            'status' => LoanStatus::PENDING,
        ]);

        $response = $this->post(route('loans.reject', $loan), [
            'rejection_reason' => 'Rasio beban angsuran melebihi 100% dari penghasilan.',
        ]);

        $response->assertRedirect(route('loans.index'));
        $response->assertSessionHas('success');

        $loan->refresh();
        $this->assertTrue($loan->status->isRejected());
        $this->assertEquals('Rasio beban angsuran melebihi 100% dari penghasilan.', $loan->rejection_reason);
    }

    public function test_cannot_reapprove_already_processed_loan(): void
    {
        $this->actingAs($this->analyst);

        $loan = LoanApplication::create([
            'customer_name' => 'Eko Patrio',
            'loan_type' => LoanType::MOTOR,
            'loan_amount' => 15000000,
            'tenor_months' => 12,
            'monthly_income' => 6000000,
            'monthly_installment' => 1385000,
            'status' => LoanStatus::APPROVED,
        ]);

        $response = $this->post(route('loans.approve', $loan));
        $response->assertSessionHas('error');
    }

    public function test_credit_analyst_cannot_create_loan_application(): void
    {
        $this->actingAs($this->analyst);

        $payload = [
            'customer_name' => 'Analyst Attempt',
            'loan_type' => 'motor',
            'loan_amount' => '10.000.000',
            'tenor_months' => 12,
            'monthly_income' => '5.000.000',
        ];

        $response = $this->post(route('loans.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_credit_analyst_does_not_see_create_button(): void
    {
        $this->actingAs($this->analyst);

        $response = $this->get(route('loans.index'));
        $response->assertDontSee('Catat Pengajuan Baru');
    }

    public function test_marketing_officer_sees_create_button(): void
    {
        $this->actingAs($this->marketing);

        $response = $this->get(route('loans.index'));
        $response->assertSee('Catat Pengajuan Baru');
    }

    public function test_can_get_loan_detail_json_with_schedule(): void
    {
        $loan = LoanApplication::create([
            'customer_name' => 'Gilang Dirga',
            'loan_type' => LoanType::MOTOR,
            'loan_amount' => 12000000,
            'tenor_months' => 12,
            'monthly_income' => 6000000,
            'monthly_installment' => 1108000,
            'status' => LoanStatus::PENDING,
        ]);

        $response = $this->getJson(route('loans.show', $loan));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'customer_name',
            'loan_amount',
            'formatted_loan_amount',
            'monthly_installment',
            'calculation' => [
                'principal_per_month',
                'interest_per_month',
                'monthly_installment',
                'total_repayment',
            ],
            'schedule' => [
                '*' => [
                    'month',
                    'principal',
                    'interest',
                    'installment',
                    'remaining_balance',
                ]
            ]
        ]);
    }
}
