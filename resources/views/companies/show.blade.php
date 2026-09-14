<x-layout>
    <x-page-heading>{{ $company->name }}</x-page-heading>

    <div class="space-y-8">
        <div class="space-y-3 border-b border-white/10 pb-6">
            @if($company->location)<p class="text-gray-400">{{ $company->location }}</p>@endif
            @if($company->description)<p class="leading-7 text-gray-300">{{ $company->description }}</p>@endif
            @if($company->website)
                <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="font-bold text-blue-300 hover:text-blue-200">Visit website</a>
            @endif
        </div>

        <div class="space-y-4">
            <h2 class="text-2xl font-bold">Open positions</h2>
            @forelse($company->jobs as $job)
                <x-panel class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-xl font-bold"><a href="{{ route('careers.show', $job) }}" class="hover:text-blue-300">{{ $job->title }}</a></h3>
                        <p class="mt-2 text-sm text-gray-400">{{ $job->location }} · {{ $job->schedule }}
                            @if(auth()->guest() || auth()->user()->canViewApplicationCount($job))
                                · {{ $job->applications_count }} {{ $job->applications_count === 1 ? 'application' : 'applications' }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('careers.show', $job) }}" class="font-bold text-blue-300 hover:text-blue-200">View job</a>
                </x-panel>
            @empty
                <p class="text-gray-400">This company has no open positions.</p>
            @endforelse
        </div>
    </div>
</x-layout>