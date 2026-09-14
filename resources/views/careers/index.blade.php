<x-layout>
    <x-page-heading>Careers</x-page-heading>

    <div class="space-y-8">
        <div class="border-b border-white/10 pb-6">
            <p class="text-gray-400">Find your next opportunity from the latest roles.</p>

            <form method="GET" action="{{ route('careers.index') }}" class="relative mt-5 flex gap-3 rounded-xl border border-white/10 bg-white/5 p-4">
                <input name="q" value="{{ $search }}" placeholder="Search jobs, companies, locations, or tags..."
                       class="min-w-0 flex-1 rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-gray-500">
                <button class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-black transition-colors duration-300 hover:bg-gray-200">Search</button>

                <details class="relative">
                    <summary class="flex h-full cursor-pointer list-none items-center rounded-lg border border-white/10 px-3 text-gray-300 transition-colors duration-300 hover:bg-white/10 hover:text-white" title="Open filters" aria-label="Open filters">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M10 18h4" />
                        </svg>
                    </summary>

                    <div class="absolute right-0 z-20 mt-3 grid w-[min(20rem,calc(100vw-3rem))] gap-3 rounded-xl border border-white/10 bg-zinc-950 p-4 shadow-xl">
                        <input name="location" value="{{ $location }}" placeholder="Filter by location"
                               class="rounded-lg border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-gray-500">
                        <select name="company_id" class="rounded-lg border border-white/10 bg-black px-4 py-3 text-white">
                            <option value="">All companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected((string) $companyId === (string) $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <select name="schedule" class="rounded-lg border border-white/10 bg-black px-4 py-3 text-white">
                            <option value="">All schedules</option>
                            <option value="Full Time" @selected($schedule === 'Full Time')>Full Time</option>
                            <option value="Part Time" @selected($schedule === 'Part Time')>Part Time</option>
                        </select>
                        <select name="tag" class="rounded-lg border border-white/10 bg-black px-4 py-3 text-white">
                            <option value="">All tags</option>
                            @foreach($tags as $availableTag)
                                <option value="{{ $availableTag->id }}" @selected((string) $tag === (string) $availableTag->id)>{{ $availableTag->name }}</option>
                            @endforeach
                        </select>
                        <select name="salary" class="rounded-lg border border-white/10 bg-black px-4 py-3 text-white">
                            <option value="">All salaries</option>
                            @foreach($salaries as $availableSalary)
                                <option value="{{ $availableSalary }}" @selected($salary === $availableSalary)>{{ $availableSalary }}</option>
                            @endforeach
                        </select>
                        <div class="flex items-center justify-between gap-3 pt-1">
                            <a href="{{ route('careers.index') }}" class="text-sm text-gray-400 hover:text-white">Clear</a>
                            <button class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-black transition-colors duration-300 hover:bg-gray-200">Apply filters</button>
                        </div>
                    </div>
                </details>
            </form>
        </div>

        <div class="space-y-4">
            @forelse($jobs as $job)
                <x-panel class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm text-gray-400">{{ $job->employer->name }}</p>
                        <h2 class="mt-1 text-xl font-bold"><a href="{{ route('careers.show', $job) }}" class="hover:text-blue-300">{{ $job->title }}</a></h2>
                        <p class="mt-2 text-sm text-gray-400">{{ $job->location }} · {{ $job->schedule }}
                            @if(auth()->guest() || auth()->user()->isJobSeeker())
                                · {{ $job->applications_count }} {{ $job->applications_count === 1 ? 'application' : 'applications' }}
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-5">
                        <span class="text-sm text-gray-300">{{ $job->salary }}</span>
                        <a href="{{ route('careers.show', $job) }}" class="rounded-lg bg-white px-3 py-1.5 text-sm font-bold text-black transition-colors duration-300 hover:bg-gray-200">
                            View job
                        </a>
                    </div>
                </x-panel>
            @empty
                <p class="text-gray-400">No career opportunities are available yet.</p>
            @endforelse
        </div>

        {{ $jobs->links() }}
    </div>
</x-layout>
