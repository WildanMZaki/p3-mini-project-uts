<?php

namespace App\Observers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;

/**
 * Mencatat setiap perubahan status laporan ke tabel report_status_logs.
 *
 * Observer hanya MERESPONS perubahan model; ia tidak menentukan boleh/tidaknya
 * perubahan (itu tugas ReportPolicy & ReportWorkflowService).
 * Data yang dicatat sengaja minimal: id laporan, status lama, status baru,
 * id pengguna pelaku, dan waktu. Tidak ada deskripsi, koordinat, atau file.
 */
class ReportObserver
{
    public function created(Report $report): void
    {
        $this->log($report, null);
    }

    public function updated(Report $report): void
    {
        // Simpan bukti/judul/lokasi tanpa ganti status tidak perlu dicatat
        if (! $report->wasChanged('status')) {
            return;
        }

        $this->log($report, $report->getRawOriginal('status'));
    }

    private function log(Report $report, ?string $fromStatus): void
    {
        $report->statusLogs()->create([
            'from_status' => $fromStatus,
            'to_status' => $report->status,
            'changed_by' => Auth::id(), // null jika dari seeder/console
        ]);
    }
}
