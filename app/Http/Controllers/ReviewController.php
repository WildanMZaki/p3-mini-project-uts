<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Http\Requests\ReviewReportRequest;
use App\Models\Report;
use App\Services\ReportWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private ReportWorkflowService $workflow) {}

    public function claim(Request $request, Report $report): RedirectResponse
    {
        $this->workflow->claim($report, $request->user());

        return redirect()
            ->route('reports.show', $report)
            ->with('status', "Laporan {$report->code} sekarang Anda tinjau.");
    }

    public function decide(ReviewReportRequest $request, Report $report): RedirectResponse
    {
        $decision = ReportStatus::from($request->validated('decision'));

        $this->workflow->decide($report, $request->user(), $decision, $request->validated('review_note'));

        return redirect()
            ->route('reports.show', $report)
            ->with('status', "Status laporan diubah menjadi \"{$decision->label()}\".");
    }
}
