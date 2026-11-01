<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\EvidenceService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportEvidenceController extends Controller
{
    public function __invoke(Report $report, EvidenceService $evidence): StreamedResponse
    {
        abort_unless($evidence->exists($report), 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(EvidenceService::DISK);

        return $disk->response(
            $report->evidence_path,
            $report->evidence_original_name,
        );
    }
}
