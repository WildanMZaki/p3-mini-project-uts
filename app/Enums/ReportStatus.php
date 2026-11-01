<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Baru = 'baru';
    case PerluPerbaikan = 'perlu_perbaikan';
    case Ditinjau = 'ditinjau';
    case PerluDiteruskan = 'perlu_diteruskan';
    case Diteruskan = 'diteruskan';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru',
            self::PerluPerbaikan => 'Perlu Perbaikan',
            self::Ditinjau => 'Sedang Ditinjau',
            self::PerluDiteruskan => 'Perlu Diteruskan',
            self::Diteruskan => 'Sudah Diteruskan',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Tidak Ditindaklanjuti',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Baru => [self::Ditinjau],
            self::Ditinjau => [self::PerluPerbaikan, self::PerluDiteruskan, self::Ditolak],
            self::PerluPerbaikan => [self::Baru],
            self::PerluDiteruskan => [self::Diteruskan],
            self::Diteruskan => [self::Selesai],
            self::Selesai, self::Ditolak => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function isActive(): bool
    {
        return ! $this->isFinal();
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Selesai, self::Ditolak], true);
    }

    public function isEditableByReporter(): bool
    {
        return in_array($this, [self::Baru, self::PerluPerbaikan], true);
    }

    public static function activeCases(): array
    {
        return array_values(array_filter(self::cases(), fn(self $status) => $status->isActive()));
    }

    public static function reviewDecisions(): array
    {
        return [self::PerluPerbaikan, self::PerluDiteruskan, self::Ditolak];
    }
}
