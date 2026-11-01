<?php

namespace App\Http\Controllers;

use App\Enums\ConditionLevel;
use App\Http\Requests\ReportRequest;
use App\Models\Area;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(Request $request): View
    {
        $reports = Report::query()
            ->visibleTo($request->user())
            ->with(['area', 'reporter'])
            ->latest('occurred_at')
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create(): View
    {
        return view('reports.create', $this->formData());
    }

    public function store(ReportRequest $request): RedirectResponse
    {
        $report = $this->reports->create(
            $request->user(),
            $request->safe()->except('evidence'),
            $request->file('evidence'),
        );

        return redirect()
            ->route('reports.show', $report)
            ->with('status', "Laporan {$report->code} berhasil dikirim.");
    }

    public function show(Report $report): View
    {
        $report->load(['area', 'reporter']);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        return view('reports.edit', ['report' => $report, ...$this->formData()]);
    }

    public function update(ReportRequest $request, Report $report): RedirectResponse
    {
        $this->reports->update(
            $report,
            $request->safe()->except('evidence'),
            $request->file('evidence'),
        );

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $this->reports->delete($report);

        return redirect()
            ->route('reports.index')
            ->with('status', "Laporan {$report->code} dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'areas' => Area::query()->active()->orderBy('name')->get(),
            'conditions' => ConditionLevel::cases(),
        ];
    }
}
