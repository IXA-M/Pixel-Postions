<x-layout>
    <x-page-heading>Profile</x-page-heading>

    @if(session('status'))
        <p class="mx-auto mb-6 max-w-2xl text-center text-sm text-green-300">{{ session('status') }}</p>
    @endif

    @php
        $company = $user->company;
    @endphp

    <x-forms.form method="PUT" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        <x-section-heading>Account</x-section-heading>

        <x-forms.input label="Name" name="name" :value="old('name', $user->name)" />
        <x-forms.input label="Email" name="email" type="email" :value="old('email', $user->email)" />

        @if($isEmployer)
            <x-forms.divider />
            <x-section-heading>Company Profile</x-section-heading>

            <x-forms.input label="Company Name" name="company_name" :value="old('company_name', $company?->name ?? $user->employer?->name)" />
            <x-forms.input label="Website" name="website" type="url" :value="old('website', $company?->website)" />
            <x-forms.input label="Location" name="company_location" :value="old('company_location', $company?->location)" />
            <x-forms.input label="Company Logo" name="logo" type="file" />

            <x-forms.field label="Description" name="description">
                <textarea name="description" id="description" rows="5" class="w-full rounded-xl border border-white/10 bg-white/10 px-5 py-4 text-white">{{ old('description', $company?->description) }}</textarea>
            </x-forms.field>
        @else
            <x-forms.divider />
            <x-section-heading> Profile</x-section-heading>

            <x-forms.input label="Headline" name="headline" :value="old('headline', $user->jobSeeker?->headline)" placeholder="Frontend Developer" />
            <x-forms.input label="Location" name="location" :value="old('location', $user->jobSeeker?->location)" />

            <x-forms.field label="Bio" name="bio">
                <textarea name="bio" id="bio" rows="5" class="w-full rounded-xl border border-white/10 bg-white/10 px-5 py-4 text-white">{{ old('bio', $user->jobSeeker?->bio) }}</textarea>
            </x-forms.field>
        @endif

        <x-forms.button>Save Profile</x-forms.button>
    </x-forms.form>
</x-layout>
