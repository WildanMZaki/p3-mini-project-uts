<?php

namespace App\Enums;

enum ConditionLevel: string
{
    case AsapTipis = 'asap_tipis';
    case AsapTebal = 'asap_tebal';
    case ApiTerlihat = 'api_terlihat';
    case ApiMeluas = 'api_meluas';

    public function label(): string
    {
        return match ($this) {
            self::AsapTipis => 'Asap tipis',
            self::AsapTebal => 'Asap tebal',
            self::ApiTerlihat => 'Api terlihat',
            self::ApiMeluas => 'Api meluas',
        };
    }

    public function isCritical(): bool
    {
        return in_array($this, [self::ApiTerlihat, self::ApiMeluas], true);
    }

    public static function criticalCases(): array
    {
        return [self::ApiTerlihat, self::ApiMeluas];
    }
}
