<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForwardingRequest;
use App\Models\Report;
use App\Services\ForwardingService;
use App\Services\ReportWorkflowService;
use Illuminate\Http\RedirectResponse;

class FollowUpController extends Controller
{
    public function forward(StoreForwardingRequest $request, Report $report, ForwardingService $forwarding): RedirectResponse
    {
        $record = $forwarding->record($report, $request->user(), $request->validated());

        return redirect()
            ->route('reports.show', $report)
            ->with('status', "Penerusan ke {$record->agency->name} berhasil dicatat.");
    }

    public function close(Report $report, ReportWorkflowService $workflow): RedirectResponse
    {
        $workflow->close($report);

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Proses laporan pada aplikasi komunitas ditandai selesai.');
    }
}
