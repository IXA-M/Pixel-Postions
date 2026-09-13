@props(['employer', 'width' => 90])

@if($employer->logo)
	@php
		$logoUrl = str_starts_with($employer->logo, 'http://') || str_starts_with($employer->logo, 'https://')
			? $employer->logo
			: \Illuminate\Support\Facades\Storage::disk('public')->url($employer->logo);
	@endphp

	<img src="{{ $logoUrl }}" alt="{{ $employer->name }} logo" class="rounded-xl object-cover" width="{{ $width }}" height="{{ $width }}">
@else
	<div class="flex items-center justify-center rounded-xl bg-white/10 p-2" style="width: {{ $width }}px; height: {{ $width }}px;">
		<img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="{{ $employer->name }} logo" class="max-h-full max-w-full object-contain">
	</div>
@endif
