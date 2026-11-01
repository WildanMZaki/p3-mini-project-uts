<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Membuat laporan baru dari akses pelapor.
     */
    public function create(User $reporter, array $data): Report
    {
        return DB::transaction(function () use ($reporter, $data) {
            $report = new Report($data);
            $report->code = $this->generateCode();
            $report->status = ReportStatus::Baru;
            $report->reporter()->associate($reporter);
            $report->save();

            return $report;
        });
    }

    public function update(Report $report, array $data): Report
    {
        $report->fill($data);
        $report->save();

        return $report;
    }

    public function delete(Report $report): void
    {
        $report->delete();
    }

    private function generateCode(): string
    {
        // Rencana format: KHL-YYYYMM-0001 (nomor urut per bulan)
        $prefix = 'KHL-' . now()->format('Ym') . '-';

        $lastCode = Report::query()
            ->where('code', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max('code');

        $next = $lastCode ? ((int) substr($lastCode, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
