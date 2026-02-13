<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Failed Jobs</p>
            <p class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ $this->getFailedJobsCount() }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Queue Backlog</p>
            <p class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ $this->getQueueBacklogCount() }}</p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap items-center gap-3">
        <a
            href="{{ $this->getPulseUrl() }}"
            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-500"
            target="_blank"
            rel="noreferrer"
        >
            Open Pulse
        </a>

        <a
            href="{{ $this->getTelescopeUrl() }}"
            class="inline-flex items-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-600"
            target="_blank"
            rel="noreferrer"
        >
            Open Telescope
        </a>
    </div>
</x-filament-panels::page>
