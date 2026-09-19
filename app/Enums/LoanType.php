<?php

namespace App\Enums;

enum LoanType: string
{
    case MOTOR = 'motor';
    case MOBIL = 'mobil';
    case MULTIGUNA = 'multiguna';

    /**
     * Get human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::MOTOR => 'Sepeda Motor',
            self::MOBIL => 'Mobil',
            self::MULTIGUNA => 'Multiguna',
        };
    }

    /**
     * Get Tailwind color classes for badges.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::MOTOR => 'bg-blue-50 text-blue-700 border-blue-200 ring-blue-600/20',
            self::MOBIL => 'bg-indigo-50 text-indigo-700 border-indigo-200 ring-indigo-600/20',
            self::MULTIGUNA => 'bg-amber-50 text-amber-800 border-amber-200 ring-amber-600/20',
        };
    }
}
