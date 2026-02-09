<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;

final class UserObserver
{
    public function created()
    {
        /** @var Tenant $tenant */
        $tenant = tenant();
        $doctorsCount = User::role('doctor')->where('tenant_id', $tenant->id)->count();
        $secretariesCount = User::role('secretary')->where('tenant_id', $tenant->id)->count();

        $tenant->update([
            'number_of_doctors' => $doctorsCount,
            'number_of_secretaries' => $secretariesCount,
        ]);
    }
}
