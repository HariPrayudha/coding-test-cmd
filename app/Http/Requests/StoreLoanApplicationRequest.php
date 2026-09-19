<?php

namespace App\Http\Requests;

use App\Enums\LoanType;
use App\Models\LoanApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs for validation (clean currency formatting like Rp and separators).
     */
    protected function prepareForValidation(): void
    {
        $cleanAmount = $this->input('loan_amount');
        if (is_string($cleanAmount)) {
            $cleanAmount = preg_replace('/[^\d]/', '', $cleanAmount);
        }

        $cleanIncome = $this->input('monthly_income');
        if (is_string($cleanIncome)) {
            $cleanIncome = preg_replace('/[^\d]/', '', $cleanIncome);
        }

        $this->merge([
            'customer_name' => trim((string) $this->input('customer_name')),
            'loan_amount' => $cleanAmount !== '' ? (float) $cleanAmount : null,
            'monthly_income' => $cleanIncome !== '' ? (float) $cleanIncome : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => [
                'required',
                'string',
                'min:2',
                'max:150',
                function ($attribute, $value, $fail) {
                    // Behaviour 4: Maksimal pengajuan nasabah adalah sebanyak 3 kali
                    $existingCount = LoanApplication::whereRaw('LOWER(TRIM(customer_name)) = ?', [strtolower(trim($value))])
                        ->count();

                    if ($existingCount >= 3) {
                        $fail('Maksimal pengajuan nasabah adalah sebanyak 3 kali. Nasabah ini sudah memiliki 3 pengajuan.');
                    }
                },
            ],
            'loan_type' => ['required', Rule::enum(LoanType::class)],
            'loan_amount' => [
                'required',
                'numeric',
                'min:100000',
                // Behaviour 2: Nominal maksimal pinjaman yang dapat disetujui adalah 200 juta
                'max:200000000',
            ],
            'tenor_months' => [
                'required',
                'integer',
                'min:1',
                // Behaviour 3: Tenor pinjaman tertinggi adalah 24 bulan
                'max:24',
            ],
            'monthly_income' => [
                'required',
                'numeric',
                // Behaviour 1: Pendapatan bulanan nasabah < 1 juta, sistem menampilkan pesan khusus
                'min:1000000',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages matching test criteria.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama lengkap nasabah wajib diisi.',
            'loan_type.required' => 'Tipe pengajuan wajib dipilih.',
            'loan_type.enum' => 'Tipe pengajuan yang dipilih tidak valid.',
            'loan_amount.required' => 'Nominal pengajuan wajib diisi.',
            'loan_amount.numeric' => 'Nominal pengajuan harus berupa angka valid.',
            'loan_amount.min' => 'Nominal pengajuan minimal adalah Rp 100.000.',
            'loan_amount.max' => 'Nominal maksimal pinjaman yang dapat disetujui adalah 200 juta.',
            'tenor_months.required' => 'Tenor pinjaman wajib diisi.',
            'tenor_months.integer' => 'Tenor pinjaman harus berupa bilangan bulat bulan.',
            'tenor_months.min' => 'Tenor pinjaman minimal adalah 1 bulan.',
            'tenor_months.max' => 'Tenor pinjaman tertinggi adalah 24 bulan.',
            'monthly_income.required' => 'Pendapatan bulanan nasabah wajib diisi.',
            'monthly_income.numeric' => 'Pendapatan bulanan harus berupa angka valid.',
            // Exact required error message:
            'monthly_income.min' => 'Nasabah belum dapat mengajukan pinjaman',
            'notes.max' => 'Catatan maksimal 1.000 karakter.',
        ];
    }
}
