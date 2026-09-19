<?php

namespace App\Services;

class LoanCalculationService
{
    /**
     * Default monthly interest/margin rate (0.9% flat per month).
     */
    public const DEFAULT_MONTHLY_INTEREST_RATE = 0.0090;

    /**
     * Calculate monthly loan installment and financing breakdown.
     *
     * @param float $loanAmount
     * @param int $tenorMonths
     * @param float $interestRateMonthly
     * @return array<string, mixed>
     */
    public function calculate(
        float $loanAmount,
        int $tenorMonths,
        float $interestRateMonthly = self::DEFAULT_MONTHLY_INTEREST_RATE
    ): array {
        if ($tenorMonths <= 0) {
            $tenorMonths = 1;
        }

        $principalPerMonth = round($loanAmount / $tenorMonths, 2);
        $interestPerMonth = round($loanAmount * $interestRateMonthly, 2);
        $monthlyInstallment = round($principalPerMonth + $interestPerMonth, 2);

        $totalRepayment = round($monthlyInstallment * $tenorMonths, 2);
        $totalInterest = round($interestPerMonth * $tenorMonths, 2);

        return [
            'loan_amount' => $loanAmount,
            'tenor_months' => $tenorMonths,
            'interest_rate_monthly' => $interestRateMonthly,
            'interest_percentage_monthly' => $interestRateMonthly * 100,
            'principal_per_month' => $principalPerMonth,
            'interest_per_month' => $interestPerMonth,
            'monthly_installment' => $monthlyInstallment,
            'total_repayment' => $totalRepayment,
            'total_interest' => $totalInterest,
            'formatted_loan_amount' => 'Rp ' . number_format($loanAmount, 0, ',', '.'),
            'formatted_principal_per_month' => 'Rp ' . number_format($principalPerMonth, 0, ',', '.'),
            'formatted_interest_per_month' => 'Rp ' . number_format($interestPerMonth, 0, ',', '.'),
            'formatted_monthly_installment' => 'Rp ' . number_format($monthlyInstallment, 0, ',', '.'),
            'formatted_total_repayment' => 'Rp ' . number_format($totalRepayment, 0, ',', '.'),
            'formatted_total_interest' => 'Rp ' . number_format($totalInterest, 0, ',', '.'),
        ];
    }

    /**
     * Generate detailed monthly amortization schedule.
     *
     * @param float $loanAmount
     * @param int $tenorMonths
     * @param float $interestRateMonthly
     * @return array<int, array<string, mixed>>
     */
    public function generateSchedule(
        float $loanAmount,
        int $tenorMonths,
        float $interestRateMonthly = self::DEFAULT_MONTHLY_INTEREST_RATE
    ): array {
        $calculation = $this->calculate($loanAmount, $tenorMonths, $interestRateMonthly);
        $schedule = [];
        $remainingBalance = $loanAmount;

        for ($month = 1; $month <= $tenorMonths; $month++) {
            $principal = ($month === $tenorMonths) ? $remainingBalance : $calculation['principal_per_month'];
            $interest = $calculation['interest_per_month'];
            $installment = round($principal + $interest, 2);
            $remainingBalance = max(0, round($remainingBalance - $principal, 2));

            $schedule[] = [
                'month' => $month,
                'principal' => $principal,
                'interest' => $interest,
                'installment' => $installment,
                'remaining_balance' => $remainingBalance,
                'formatted_principal' => 'Rp ' . number_format($principal, 0, ',', '.'),
                'formatted_interest' => 'Rp ' . number_format($interest, 0, ',', '.'),
                'formatted_installment' => 'Rp ' . number_format($installment, 0, ',', '.'),
                'formatted_remaining_balance' => 'Rp ' . number_format($remainingBalance, 0, ',', '.'),
            ];
        }

        return $schedule;
    }
}
