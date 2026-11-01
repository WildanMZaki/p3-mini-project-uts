<?php

namespace App\Enums;

enum ForwardingChannel: string
{
    case Telepon = 'telepon';
    case WhatsApp = 'whatsapp';
    case Surat = 'surat';
    case Langsung = 'langsung';

    public function label(): string
    {
        return match ($this) {
            self::Telepon => 'Telepon',
            self::WhatsApp => 'WhatsApp',
            self::Surat => 'Surat',
            self::Langsung => 'Datang langsung',
        };
    }
}
