<?php

namespace App\Enums;

enum LegalTextType: string
{
    case PRIVACY_POLICY = 'privacy_policy';
    case TERMS_AND_CONDITIONS = 'terms_and_conditions';

    public function label(): string
    {
        return match ($this) {
            self::PRIVACY_POLICY => __('resources.legal_texts.types.privacy_policy'),
            self::TERMS_AND_CONDITIONS => __('resources.legal_texts.types.terms_and_conditions'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
