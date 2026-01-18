<?php

namespace App\Enums;

enum OrderFulfillmentType: string
{
    case DELIVERY = 'delivery';
    case PICKUP = 'pickup';
    case DINE_IN = 'dine_in';

    public function label(): string
    {
        return __('resources.orders.fulfillment_types.' . $this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::DELIVERY => 'primary',
            self::PICKUP => 'info',
            self::DINE_IN => 'warning',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public static function tryFromMixed(self|string|null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (! is_string($value) || $value === '') {
            return null;
        }

        return self::tryFrom($value);
    }
}
