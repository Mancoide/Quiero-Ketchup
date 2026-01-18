<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return __('resources.orders.statuses.' . $this->value);
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

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PREPARING => 'info',
            self::READY => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
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
