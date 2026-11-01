<?php

namespace App\Models;

use App\Enums\AgencyType;
use Database\Factories\AgencyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ReportForwarding;

#[Fillable(['name', 'type', 'contact', 'is_active'])]
class Agency extends Model
{
    /** @use HasFactory<AgencyFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => AgencyType::class,
            'is_active' => 'boolean',
        ];
    }

    public function forwardings(): HasMany
    {
        return $this->hasMany(ReportForwarding::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
