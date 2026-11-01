<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EvidenceService
{
    public const DISK = 'local';
    public const PATH = 'evidence';

    public function generatePath(?Report $report = null): string
    {
        return $report ? self::PATH . '/' . $report->id : self::PATH;
    }

    public function attach(Report $report, UploadedFile $file): void
    {
        $oldPath = $report->evidence_path;

        $report->evidence_path = $file->store($this->generatePath($report), self::DISK);
        $report->evidence_original_name = mb_substr($file->getClientOriginalName(), 0, 255);
        $report->evidence_mime = $file->getMimeType();
        $report->save();

        if ($oldPath !== null && $oldPath !== $report->evidence_path) {
            Storage::disk(self::DISK)->delete($oldPath);
        }
    }

    public function remove(Report $report): void
    {
        if ($report->evidence_path !== null) {
            Storage::disk(self::DISK)->delete($report->evidence_path);
        }
    }

    public function exists(Report $report): bool
    {
        return $report->evidence_path !== null
            && Storage::disk(self::DISK)->exists($report->evidence_path);
    }
}
