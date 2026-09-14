<x-layout>
	<div class="space-y-8">
		<div class="border-b border-white/10 pb-8">
			<p class="text-gray-400">{{ $job->employer->name }}</p>
			<h1 class="mt-2 text-4xl font-bold">{{ $job->title }}</h1>
			<p class="mt-4 text-gray-300">{{ $job->location }} · {{ $job->schedule }} · {{ $job->salary }}
				@if(auth()->guest() || auth()->user()->isJobSeeker())
					· {{ $job->applications_count }} {{ $job->applications_count === 1 ? 'application' : 'applications' }}
				@endif
			</p>
		</div>

		@if(session('success'))
			<div class="rounded-lg border border-green-400/30 bg-green-400/10 p-4 text-green-200">{{ session('success') }}</div>
		@endif

		<div class="flex flex-wrap gap-2">
			@foreach($job->tags as $tag)<x-tag :$tag />@endforeach
		</div>

		<section class="space-y-4">
			<h2 class="text-2xl font-bold">Job location</h2>
			<p class="text-gray-300">{{ $job->location }}</p>
			@php
				$mapQuery = $job->latitude !== null && $job->longitude !== null
					? $job->latitude.','.$job->longitude
					: $job->location;
			@endphp
			<div class="overflow-hidden rounded-xl border border-white/10">
				<iframe
					src="https://www.google.com/maps?q={{ urlencode($mapQuery) }}&output=embed"
					class="h-72 w-full border-0"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					title="Map showing {{ $job->location }}"
				></iframe>
			</div>
			<a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapQuery) }}" target="_blank" rel="noopener noreferrer" class="text-sm font-bold text-gray-300 hover:text-white">
				Open in Google Maps
			</a>
		</section>

		<div class="flex flex-wrap items-center gap-4">
			@auth
				@if(auth()->user()->isJobSeeker())
					@if($hasApplied)
						<span class="rounded-lg bg-white/10 px-5 py-3 font-bold text-gray-300">Application submitted</span>
					@else
						<form method="POST" action="{{ route('applications.store', $job) }}">
							@csrf
							<button class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-black transition-colors duration-300 hover:bg-gray-200">Apply for Job</button>
						</form>
					@endif
				@endif
			@else
				<a href="{{ url('/login') }}" class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-black transition-colors duration-300 hover:bg-gray-200">Log in to apply</a>
			@endauth

			<a href="{{ $job->url }}" target="_blank" rel="noopener noreferrer" class="font-bold text-blue-300 hover:text-blue-200">View external listing</a>
		</div>
	</div>
</x-layout>
