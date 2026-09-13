<x-layout>
    <x-page-heading>Careers</x-page-heading>

    <div class="space-y-8">
        <div class="border-b border-white/10 pb-6">
            <p class="text-gray-400">Find your next opportunity from the latest roles.</p>
        </div>

        <div class="space-y-4">
            @forelse($jobs as $job)
                <x-panel class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm text-gray-400">{{ $job->employer->name }}</p>
                        <h2 class="mt-1 text-xl font-bold">{{ $job->title }}</h2>
                        <p class="mt-2 text-sm text-gray-400">{{ $job->location }} · {{ $job->schedule }}</p>
                    </div>

                    <div class="flex items-center gap-5">
                        <span class="text-sm text-gray-300">{{ $job->salary }}</span>
                        <a href="{{ $job->url }}" target="_blank" rel="noopener noreferrer" class="font-bold text-blue-300 hover:text-blue-200">
                            View role
                        </a>
                    </div>
                </x-panel>
            @empty
                <p class="text-gray-400">No career opportunities are available yet.</p>
            @endforelse
        </div>
    </div>
</x-layout>
