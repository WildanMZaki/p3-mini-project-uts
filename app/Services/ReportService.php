<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(
        private EvidenceService $evidence,
        private ReportWorkflowService $workflow,
    ) {}

    public function create(User $reporter, array $data, ?UploadedFile $evidence = null): Report
    {
        return DB::transaction(function () use ($reporter, $data, $evidence) {
            $report = new Report($data);
            $report->code = $this->generateCode();
            $report->status = ReportStatus::Baru;
            $report->reporter()->associate($reporter);
            $report->save();

            if ($evidence !== null) {
                $this->evidence->attach($report, $evidence);
            }

            return $report;
        });
    }

    public function update(Report $report, array $data, ?UploadedFile $evidence = null): Report
    {
        return DB::transaction(function () use ($report, $data, $evidence) {
            $report->fill($data);

            if ($report->status === ReportStatus::PerluPerbaikan) {
                $this->workflow->resubmit($report);
            }

            $report->save();

            if ($evidence !== null) {
                $this->evidence->attach($report, $evidence);
            }

            return $report;
        });
    }

    public function delete(Report $report): void
    {
        DB::transaction(fn () => $report->delete());

        $this->evidence->remove($report);
    }

    private function generateCode(): string
    {
        $prefix = 'KHL-' . now()->format('Ym') . '-';

        $lastCode = Report::query()
            ->where('code', 'like', $prefix . '%')
            ->lockForUpdate()
            ->max('code');

        $next = $lastCode ? ((int) substr($lastCode, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
