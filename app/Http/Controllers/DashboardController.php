<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        // Angka singkat sesuai pekerjaan masing-masing role
        $stats = match ($user->role) {
            UserRole::Pelapor => [
                'Laporan saya yang masih aktif' => $user->reports()->active()->count(),
                'Laporan saya yang perlu diperbaiki' => $user->reports()->where('status', ReportStatus::PerluPerbaikan)->count(),
            ],
            UserRole::Relawan => [
                'Laporan baru menunggu ditinjau' => Report::query()->where('status', ReportStatus::Baru)->count(),
                'Sedang saya tinjau' => $user->reviewedReports()->where('status', ReportStatus::Ditinjau)->count(),
            ],
            UserRole::Koordinator => [
                'Menunggu diteruskan' => Report::query()->where('status', ReportStatus::PerluDiteruskan)->count(),
                'Sudah diteruskan, belum ditutup' => Report::query()->where('status', ReportStatus::Diteruskan)->count(),
            ],
        };

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}
