<?php

namespace App\Enums;

enum AgencyType: string
{
    case Bpbd = 'bpbd';
    case ManggalaAgni = 'manggala_agni';
    case Damkar = 'damkar';
    case PemerintahDesa = 'pemerintah_desa';
    case PengelolaKawasan = 'pengelola_kawasan';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Bpbd => 'BPBD',
            self::ManggalaAgni => 'Manggala Agni',
            self::Damkar => 'Pemadam Kebakaran',
            self::PemerintahDesa => 'Pemerintah Desa/Kelurahan',
            self::PengelolaKawasan => 'Pengelola Kawasan',
            self::Lainnya => 'Lainnya',
        };
    }
}
