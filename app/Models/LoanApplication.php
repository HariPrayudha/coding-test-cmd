<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Enums\LoanType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'loan_type',
        'loan_amount',
        'tenor_months',
        'monthly_income',
        'monthly_installment',
        'interest_rate_monthly',
        'status',
        'notes',
        'rejection_reason',
        'actioned_at',
        'actioned_by',
    ];

    protected function casts(): array
    {
        return [
            'loan_type' => LoanType::class,
            'status' => LoanStatus::class,
            'loan_amount' => 'decimal:2',
            'tenor_months' => 'integer',
            'monthly_income' => 'decimal:2',
            'monthly_installment' => 'decimal:2',
            'interest_rate_monthly' => 'decimal:4',
            'actioned_at' => 'datetime',
        ];
    }

    /**
     * Scope query to search by customer name.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where('customer_name', 'like', '%' . trim($term) . '%');
    }

    /**
     * Scope query by status.
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope query by loan type.
     */
    public function scopeType(Builder $query, ?string $type): Builder
    {
        if (blank($type) || $type === 'all') {
            return $query;
        }

        return $query->where('loan_type', $type);
    }

    /**
     * Format currency helper.
     */
    public static function formatRupiah(float|int|string $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    public function getFormattedLoanAmountAttribute(): string
    {
        return self::formatRupiah($this->loan_amount);
    }

    public function getFormattedMonthlyIncomeAttribute(): string
    {
        return self::formatRupiah($this->monthly_income);
    }

    public function getFormattedMonthlyInstallmentAttribute(): string
    {
        return self::formatRupiah($this->monthly_installment);
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return Carbon::parse($this->created_at)->translatedFormat('d M Y, H:i');
    }

    /**
     * Calculate Debt-to-Income (DTI) ratio percentage.
     */
    public function getDebtToIncomeRatioAttribute(): float
    {
        if ((float) $this->monthly_income <= 0) {
            return 0.0;
        }

        return round(((float) $this->monthly_installment / (float) $this->monthly_income) * 100, 1);
    }

    /**
     * Approve the loan application.
     */
    public function approve(?string $actionedBy = 'Internal Staff'): void
    {
        $this->update([
            'status' => LoanStatus::APPROVED,
            'actioned_at' => now(),
            'actioned_by' => $actionedBy,
        ]);
    }

    /**
     * Reject the loan application.
     */
    public function reject(?string $reason = null, ?string $actionedBy = 'Internal Staff'): void
    {
        $this->update([
            'status' => LoanStatus::REJECTED,
            'rejection_reason' => $reason ?: 'Tidak memenuhi kualifikasi kredit',
            'actioned_at' => now(),
            'actioned_by' => $actionedBy,
        ]);
    }
}
