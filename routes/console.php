<?php

declare(strict_types=1);

use App\Services\AdminNotificationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('admin:dispatch-ending-soon-alerts', function (AdminNotificationService $notificationService): void {
    $notificationService->dispatchEndingSoonAlerts();

    $this->components->info('Admin ending-soon alerts dispatched.');
})->purpose('Dispatch trial/subscription ending soon alerts to system admins.');

Schedule::command('admin:dispatch-ending-soon-alerts')
    ->dailyAt('08:00');
