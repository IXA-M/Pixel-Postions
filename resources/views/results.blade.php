<x-layout>
    <x-page-heading>Results</x-page-heading>

    <div class="space-y-6">
        @forelse($jobs as $job)
            <x-job-card-wide :$job />
        @empty
            <p class="text-gray-400">No jobs matched your search.</p>
        @endforelse
    </div>
</x-layout>
