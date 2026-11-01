<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\ReportForwarding;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ForwardingService
{
    public function __construct(private ReportWorkflowService $workflow) {}

    /**
     * @param  array{agency_id: int, channel: string, forwarded_at: string, note?: string|null}  $data
     */
    public function record(Report $report, User $coordinator, array $data): ReportForwarding
    {
        return DB::transaction(function () use ($report, $coordinator, $data) {
            $locked = Report::query()->lockForUpdate()->findOrFail($report->getKey());

            if (! in_array($locked->status, [ReportStatus::PerluDiteruskan, ReportStatus::Diteruskan], true)) {
                throw ValidationException::withMessages([
                    'agency_id' => 'Penerusan hanya dapat dicatat untuk laporan berstatus "Perlu Diteruskan" atau "Sudah Diteruskan".',
                ]);
            }

            if ($locked->forwardings()->where('agency_id', $data['agency_id'])->exists()) {
                throw ValidationException::withMessages([
                    'agency_id' => 'Laporan ini sudah tercatat diteruskan ke pihak tersebut.',
                ]);
            }

            $forwarding = new ReportForwarding($data);
            $forwarding->coordinator()->associate($coordinator);
            $locked->forwardings()->save($forwarding);

            if ($locked->status === ReportStatus::PerluDiteruskan) {
                $this->workflow->markForwarded($locked);
            }

            return $forwarding;
        });
    }
}
