<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Online = 'online';
    case Any = 'any';

    public function label(): string
    {
        return match($this) {
            self::Cash => 'Cash Payment',
            self::Online => 'Online Payment',
            self::Any => 'Any Method',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Cash => 'Pay in person or via local method',
            self::Online => 'Pay via online (card, e-wallet, bank)',
            self::Any => 'Buyer can choose payment method',
        };
    }

    public static function options(): array
    {
        return [
            self::Cash->value => self::Cash->label(),
            self::Online->value => self::Online->label(),
            self::Any->value => self::Any->label(),
        ];
    }
}
