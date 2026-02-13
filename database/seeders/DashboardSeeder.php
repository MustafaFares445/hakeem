<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SubscriptionOrderStatusEnum;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Carbon\CarbonImmutable;
use Database\Factories\TenantFactory;
use Illuminate\Database\Seeder;

final class DashboardSeeder extends Seeder
{
    private const MinTenantsForDashboard = 3;

    private const TrialsEndingSoonCount = 1;

    private const PendingOrdersCount = 2;

    private const ActiveSubscriptionsCount = 2;

    private const CancelledThisMonthCount = 1;

    public function run(): void
    {
        $tenants = $this->ensureTenants();
        $plans = SubscriptionPlan::query()->where('is_active', true)->get();
        if ($plans->isEmpty()) {
            return;
        }

        $monthlyPlan = $plans->first(fn ($p) => ! $p->is_lifetime && $p->duration_unit?->value === 'month');
        $yearlyPlan = $plans->first(fn ($p) => ! $p->is_lifetime && $p->duration_unit?->value === 'year');
        $lifetimePlan = $plans->first(fn ($p) => $p->is_lifetime);
        $timedPlan = $monthlyPlan ?? $yearlyPlan ?? $plans->first();
        $now = CarbonImmutable::now();

        $created = 0;
        foreach ($tenants as $tenant) {
            if ($created >= self::PendingOrdersCount) {
                break;
            }
            SubscriptionOrder::create($this->orderAttributes($tenant->id, $timedPlan ?? $plans->first(), SubscriptionOrderStatusEnum::Pending->value));
            $created++;
        }

        $created = 0;
        foreach ($tenants as $tenant) {
            if ($created >= self::ActiveSubscriptionsCount) {
                break;
            }
            $plan = $created === 0 && $lifetimePlan ? $lifetimePlan : $timedPlan;
            $startsAt = $now->subDays(10);
            $endsAt = $plan->is_lifetime ? null : $startsAt->addMonth();
            SubscriptionOrder::create(array_merge(
                $this->orderAttributes($tenant->id, $plan, SubscriptionOrderStatusEnum::Confirmed->value),
                [
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'confirmed_at' => $now,
                ]
            ));
            $created++;
        }

        $tenantForCancelled = $tenants->first();
        if ($tenantForCancelled && $timedPlan) {
            SubscriptionOrder::create(array_merge(
                $this->orderAttributes($tenantForCancelled->id, $timedPlan, SubscriptionOrderStatusEnum::Cancelled->value),
                [
                    'starts_at' => $now->subDays(20),
                    'ends_at' => $now->subDays(5),
                    'confirmed_at' => $now->subDays(20),
                    'cancelled_at' => $now->startOfMonth()->addDays(2),
                ]
            ));
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, Tenant>
     */
    private function ensureTenants(): \Illuminate\Support\Collection
    {
        $existing = Tenant::query()->get();
        $need = self::MinTenantsForDashboard - $existing->count();
        $trialsEndingSoon = self::TrialsEndingSoonCount;
        $now = CarbonImmutable::now();
        $endingSoonAt = $now->addDays(5);

        if ($need <= 0) {
            return $existing;
        }

        $factory = TenantFactory::new();
        $defaultDomain = config('tenancy.default_domain', 'localhost');

        for ($i = 0; $i < $need; $i++) {
            $trialEndsAt = $trialsEndingSoon > 0
                ? $endingSoonAt
                : $now->addMonth();
            if ($trialsEndingSoon > 0) {
                $trialsEndingSoon--;
            }
            $tenant = $factory->create([
                'name' => 'Demo Clinic '.($existing->count() + $i + 1),
                'domain_name' => 'demo-clinic-'.($existing->count() + $i + 1).'-'.uniqid(),
                'trial_starts_at' => $now->subDays(14),
                'trial_ends_at' => $trialEndsAt,
            ]);
            $tenant->domains()->firstOrCreate(
                ['domain' => $tenant->domain_name.'.'.$defaultDomain],
                ['domain' => $tenant->domain_name.'.'.$defaultDomain]
            );
            $existing->push($tenant);
        }

        return $existing;
    }

    /**
     * @return array<string, mixed>
     */
    private function orderAttributes(string $tenantId, SubscriptionPlan $plan, string $status): array
    {
        return [
            'tenant_id' => $tenantId,
            'subscription_plan_id' => $plan->id,
            'created_by_user_id' => null,
            'status' => $status,
            'plan_name' => $plan->name,
            'plan_description' => $plan->description,
            'original_price' => $plan->original_price,
            'price' => $plan->price,
            'currency_code' => $plan->currency_code,
            'duration_value' => $plan->duration_value,
            'duration_unit' => $plan->duration_unit?->value,
            'is_lifetime' => $plan->is_lifetime,
            'starts_at' => null,
            'ends_at' => null,
            'confirmed_at' => null,
            'confirmed_by_user_id' => null,
            'cancelled_at' => null,
            'cancelled_by_user_id' => null,
            'cancellation_reason' => null,
        ];
    }
}
