<x-layout>
    <x-page-heading>Companies</x-page-heading>

    <div class="space-y-8">
        <div class="flex items-end justify-between gap-6 border-b border-white/10 pb-6">
            <div>
                <p class="text-gray-400">Explore companies hiring through Pixel Positions.</p>
            </div>
            <p class="text-sm text-gray-400">{{ $companies->count() }} registered</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            @forelse($companies as $company)
                <x-panel class="space-y-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold"><a href="{{ route('companies.show', $company) }}" class="hover:text-blue-300">{{ $company->name }}</a></h2>
                            @if($company->location)
                                <p class="mt-1 text-sm text-gray-400">{{ $company->location }}</p>
                            @endif
                        </div>
                        <span class="bg-white/10 px-3 py-1 text-xs text-gray-300">
                            {{ $company->jobs_count }} {{ $company->jobs_count === 1 ? 'job' : 'jobs' }}
                        </span>
                    </div>

                    @if($company->description)
                        <p class="text-sm leading-6 text-gray-300">{{ $company->description }}</p>
                    @endif

                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="text-sm font-bold text-blue-300 hover:text-blue-200">
                            Visit company website
                        </a>
                    @endif

                    <a href="{{ route('companies.show', $company) }}" class="text-sm font-bold text-blue-300 hover:text-blue-200">View all jobs</a>
                </x-panel>
            @empty
                <p class="text-gray-400">No companies have registered yet.</p>
            @endforelse
        </div>
    </div>
</x-layout>
