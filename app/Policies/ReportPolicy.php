<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    public function view(User $user, Report $report): bool
    {
        return $user->hasRole(UserRole::Relawan, UserRole::Koordinator)
            || $report->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Pelapor;
    }

    public function update(User $user, Report $report): Response
    {
        return $this->reporterCanModify($user, $report);
    }

    public function delete(User $user, Report $report): Response
    {
        return $this->reporterCanModify($user, $report);
    }

    public function review(User $user, Report $report): bool
    {
        return $user->role === UserRole::Relawan;
    }

    public function forward(User $user, Report $report): bool
    {
        return $user->role === UserRole::Koordinator;
    }

    public function close(User $user, Report $report): bool
    {
        return $user->role === UserRole::Koordinator;
    }

    private function reporterCanModify(User $user, Report $report): Response
    {
        if (! $report->isOwnedBy($user)) {
            return Response::deny('Anda hanya dapat mengubah laporan milik sendiri.');
        }

        if (! $report->status->isEditableByReporter()) {
            return Response::deny('Laporan sudah diproses sehingga tidak dapat diubah atau dihapus.');
        }

        return Response::allow();
    }
}
