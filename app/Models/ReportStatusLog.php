<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jejak perubahan status laporan. Hanya ditulis oleh ReportObserver.
 */
#[Fillable(['from_status', 'to_status', 'changed_by'])]
class ReportStatusLog extends Model
{
    // Tabel hanya punya created_at (log tidak pernah di-update)
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'from_status' => ReportStatus::class,
            'to_status' => ReportStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
