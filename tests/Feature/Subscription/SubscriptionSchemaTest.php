<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('creates subscription related tables and tenant trial columns', function (): void {
    expect(Schema::hasTable('tenant_types'))->toBeTrue()
        ->and(Schema::hasTable('subscription_plans'))->toBeTrue()
        ->and(Schema::hasTable('subscription_orders'))->toBeTrue();

    expect(Schema::hasColumn('tenants', 'tenant_type_id'))->toBeTrue()
        ->and(Schema::hasColumn('tenants', 'trial_starts_at'))->toBeTrue()
        ->and(Schema::hasColumn('tenants', 'trial_ends_at'))->toBeTrue()
        ->and(Schema::hasColumn('tenants', 'type'))->toBeFalse();
});
