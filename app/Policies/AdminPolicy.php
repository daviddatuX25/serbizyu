<?php

namespace App\Policies;

use App\Domains\Users\Models\User;

class AdminPolicy
{
    /**
     * Check if user is admin
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Access admin dashboard
     */
    public function viewDashboard(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Manage admin settings
     */
    public function manageSettings(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * View activity logs
     */
    public function viewActivityLogs(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
