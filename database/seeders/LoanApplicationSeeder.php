<?php

namespace Database\Seeders;

use App\Enums\LoanStatus;
use App\Enums\LoanType;
use App\Models\LoanApplication;
use App\Services\LoanCalculationService;
use Illuminate\Database\Seeder;

class LoanApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $calculator = new LoanCalculationService();

        $samples = [
            [
                'customer_name' => 'Budi Santoso',
                'loan_type' => LoanType::MOTOR,
                'loan_amount' => 25000000,
                'tenor_months' => 12,
                'monthly_income' => 7500000,
                'status' => LoanStatus::PENDING,
                'notes' => 'Pengajuan kredit motor Honda Vario 160 baru.',
                'created_at' => now()->subHours(3),
            ],
            [
                'customer_name' => 'Siti Rahmawati',
                'loan_type' => LoanType::MOBIL,
                'loan_amount' => 120000000,
                'tenor_months' => 24,
                'monthly_income' => 18000000,
                'status' => LoanStatus::APPROVED,
                'notes' => 'Pembelian mobil keluarga Toyota Avanza bekas tahun 2022. DP 30% lunas.',
                'rejection_reason' => null,
                'actioned_at' => now()->subDay(),
                'actioned_by' => 'Credit Analyst - Hendra',
                'created_at' => now()->subDays(2),
            ],
            [
                'customer_name' => 'Ahmad Fauzi',
                'loan_type' => LoanType::MULTIGUNA,
                'loan_amount' => 45000000,
                'tenor_months' => 18,
                'monthly_income' => 9500000,
                'status' => LoanStatus::PENDING,
                'notes' => 'Renovasi ruko dan penambahan modal kerja usaha sembako.',
                'created_at' => now()->subHours(6),
            ],
            [
                'customer_name' => 'Dewi Lestari',
                'loan_type' => LoanType::MOTOR,
                'loan_amount' => 35000000,
                'tenor_months' => 12,
                'monthly_income' => 3000000,
                'status' => LoanStatus::REJECTED,
                'notes' => 'Pengajuan motor Yamaha NMAX.',
                'rejection_reason' => 'Beban tagihan melebihi batas aman kemampuan bayar (DTI > 60%).',
                'actioned_at' => now()->subHours(12),
                'actioned_by' => 'Branch Manager - Juan',
                'created_at' => now()->subDays(1),
            ],
            [
                'customer_name' => 'Rudi Hermawan',
                'loan_type' => LoanType::MOBIL,
                'loan_amount' => 180000000,
                'tenor_months' => 24,
                'monthly_income' => 28000000,
                'status' => LoanStatus::APPROVED,
                'notes' => 'Kredit mobil operasional Mitsubishi Xpander. Dokumen NPWP & rekening koran lengkap.',
                'rejection_reason' => null,
                'actioned_at' => now()->subHours(20),
                'actioned_by' => 'Branch Manager - Juan',
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($samples as $data) {
            $calculation = $calculator->calculate($data['loan_amount'], $data['tenor_months']);
            $data['monthly_installment'] = $calculation['monthly_installment'];
            $data['interest_rate_monthly'] = $calculation['interest_rate_monthly'];

            LoanApplication::create($data);
        }
    }
}
