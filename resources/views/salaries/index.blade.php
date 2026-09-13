<x-layout>
    <x-page-heading>Salaries</x-page-heading>

    <div class="space-y-8">
        <div class="border-b border-white/10 pb-6">
            <p class="text-gray-400">Compare salary ranges from jobs currently listed on Pixel Positions.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($salaryBands as $band)
                <x-panel class="space-y-6">
                    <div>
                        <p class="text-sm text-gray-400">Published jobs</p>
                        <h2 class="mt-2 text-2xl font-bold">{{ $band->salary }}</h2>
                    </div>

                    <div class="flex items-center justify-between border-t border-white/10 pt-4 text-sm">
                        <span class="text-gray-400">Open roles</span>
                        <span class="font-bold">{{ $band->jobs_count }}</span>
                    </div>
                </x-panel>
            @empty
                <p class="text-gray-400">Salary information is not available yet.</p>
            @endforelse
        </div>
    </div>
</x-layout>
