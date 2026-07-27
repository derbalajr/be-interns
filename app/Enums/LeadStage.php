<?php

namespace App\Enums;

enum LeadStage: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Unqualified = 'unqualified';

    /**
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::New => [
                self::Contacted,
            ],

            self::Contacted => [
                self::Qualified,
                self::Unqualified,
            ],

            self::Qualified => [
                self::Unqualified,
            ],

            self::Unqualified => [],
        };
    }

    public function canTransitionTo(self $stage): bool
    {
        return in_array(
            $stage,
            $this->allowedTransitions(),
            true,
        );
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
