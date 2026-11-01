<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Endpoint JSON yang dipanggil dengan fetch() saat pengguna memilih area.
 * Server yang memutuskan data apa yang boleh dikirim sesuai role.
 */
class AreaSummaryController extends Controller
{
    public function __invoke(Request $request, Area $area): JsonResponse
    {
        /** @var Collection<string, int> $counts */
        $counts = $area->reports()
            ->active()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byStatus = [];
        foreach (ReportStatus::activeCases() as $status) {
            $byStatus[] = [
                'status' => $status->value,
                'label' => $status->label(),
                'total' => (int) ($counts[$status->value] ?? 0),
            ];
        }

        $payload = [
            'area' => [
                'id' => $area->id,
                'name' => $area->name,
                'regency' => $area->regency,
                'province' => $area->province,
                'is_active' => $area->is_active,
            ],
            'active_count' => (int) $counts->sum(),
            'by_status' => $byStatus,
        ];

        // Daftar laporan hanya untuk relawan & koordinator.
        // Pelapor hanya menerima angka agregat (privasi pelapor lain).
        if ($request->user()->hasRole(UserRole::Relawan, UserRole::Koordinator)) {
            $payload['latest'] = $area->reports()
                ->active()
                ->latest('occurred_at')
                ->limit(5)
                ->get()
                ->map(fn (Report $report) => [
                    'code' => $report->code,
                    'title' => $report->title,
                    'condition' => $report->condition_level->label(),
                    'status' => $report->status->label(),
                    'occurred_at' => $report->occurred_at->format('d M Y H:i'),
                    'url' => route('reports.show', $report),
                ]);
        }

        return response()->json($payload);
    }
}
