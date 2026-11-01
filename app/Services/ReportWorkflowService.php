<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportWorkflowService
{
    public function claim(Report $report, User $volunteer): Report
    {
        return DB::transaction(function () use ($report, $volunteer) {
            $locked = $this->lock($report);

            if ($locked->reviewer_id !== null && ! $locked->reviewer()->is($volunteer)) {
                throw ValidationException::withMessages([
                    'status' => 'Laporan ini sedang ditangani oleh relawan lain.',
                ]);
            }

            $this->transition($locked, ReportStatus::Ditinjau);
            $locked->reviewer()->associate($volunteer);
            $locked->save();

            return $locked;
        });
    }

    public function decide(Report $report, User $volunteer, ReportStatus $decision, string $note): Report
    {
        if (! in_array($decision, ReportStatus::reviewDecisions(), true)) {
            throw ValidationException::withMessages([
                'decision' => 'Keputusan tinjauan tidak dikenal.',
            ]);
        }

        return DB::transaction(function () use ($report, $volunteer, $decision, $note) {
            $locked = $this->lock($report);

            if (! $locked->reviewer()->is($volunteer)) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya relawan yang sedang meninjau laporan ini yang dapat memberi keputusan.',
                ]);
            }

            $this->transition($locked, $decision);
            $locked->review_note = $note;
            $locked->reviewed_at = Carbon::now();
            $locked->save();

            return $locked;
        });
    }

    public function resubmit(Report $report): void
    {
        $this->transition($report, ReportStatus::Baru);
    }

    public function markForwarded(Report $report): void
    {
        $this->transition($report, ReportStatus::Diteruskan);
        $report->save();
    }

    public function close(Report $report): Report
    {
        return DB::transaction(function () use ($report) {
            $locked = $this->lock($report);

            $this->transition($locked, ReportStatus::Selesai);
            $locked->save();

            return $locked;
        });
    }

    private function transition(Report $report, ReportStatus $target): void
    {
        if (! $report->status->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "Status laporan tidak dapat diubah dari \"{$report->status->label()}\" ke \"{$target->label()}\".",
            ]);
        }

        $report->status = $target;
    }

    private function lock(Report $report): Report
    {
        return Report::query()->lockForUpdate()->findOrFail($report->getKey());
    }
}
