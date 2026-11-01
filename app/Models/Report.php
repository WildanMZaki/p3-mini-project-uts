<?php

namespace App\Models;

use App\Enums\ConditionLevel;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'area_id',
    'title',
    'description',
    'location_detail',
    'latitude',
    'longitude',
    'occurred_at',
    'condition_level',
])]
class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'occurred_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'condition_level' => ConditionLevel::class,
            'status' => ReportStatus::class,
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function forwardings(): HasMany
    {
        return $this->hasMany(ReportForwarding::class);
    }

    /**
     * @return Collection<int, Report>
     */
    public function relatedReports(int $hours = 48): Collection
    {
        return static::query()
            ->with('reporter')
            ->where('area_id', $this->area_id)
            ->whereKeyNot($this->getKey())
            ->whereBetween('occurred_at', [
                $this->occurred_at->copy()->subHours($hours),
                $this->occurred_at->copy()->addHours($hours),
            ])
            ->orderBy('occurred_at')
            ->get();
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->reporter()->is($user);
    }

    public function hasEvidence(): bool
    {
        return $this->evidence_path !== null;
    }

    public function evidenceIsImage(): bool
    {
        return str_starts_with((string) $this->evidence_mime, 'image/');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereIn('status', ReportStatus::activeCases());
    }

    #[Scope]
    protected function visibleTo(Builder $query, User $user): void
    {
        if ($user->role === UserRole::Pelapor) {
            $query->where('reporter_id', $user->id);
        }
    }
}
