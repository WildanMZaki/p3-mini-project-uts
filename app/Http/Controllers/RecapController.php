<?php

namespace App\Http\Controllers;

use App\Enums\ConditionLevel;
use App\Enums\ReportStatus;
use App\Models\Area;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Rekap sederhana untuk relawan & koordinator.
 */
class RecapController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        // Query dasar yang sudah difilter, dipakai ulang untuk ringkasan & tabel
        $base = Report::query()
            ->when($filters['area_id'] ?? null, fn ($query, $areaId) => $query->where('area_id', $areaId))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('occurred_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('occurred_at', '<=', $to));

        $summary = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->active()->count(),
            'awaiting_forward' => (clone $base)->where('status', ReportStatus::PerluDiteruskan)->count(),
            'closed' => (clone $base)->whereIn('status', [ReportStatus::Selesai, ReportStatus::Ditolak])->count(),
        ];

        // Tabel rekap: satu baris per area, satu kolom per status
        $rowsQuery = (clone $base)
            ->select('area_id')
            ->selectRaw('COUNT(*) as total');

        foreach (ReportStatus::cases() as $status) {
            $rowsQuery->selectRaw(
                "SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as {$status->value}",
                [$status->value],
            );
        }

        [$fire, $spreading] = ConditionLevel::criticalCases();
        $rowsQuery->selectRaw(
            'SUM(CASE WHEN condition_level IN (?, ?) THEN 1 ELSE 0 END) as critical',
            [$fire->value, $spreading->value],
        );

        $rows = $rowsQuery
            ->with('area')
            ->groupBy('area_id')
            ->orderByDesc('total')
            ->get();

        return view('recap', [
            'filters' => $filters,
            'summary' => $summary,
            'rows' => $rows,
            'statuses' => ReportStatus::cases(),
            'areas' => Area::query()->orderBy('name')->get(),
        ]);
    }
}
