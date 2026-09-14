<x-layout>
    <x-page-heading>Register</x-page-heading>

    <x-forms.form method="POST" action="/register" enctype="multipart/form-data">
        <x-forms.input label="Name" name="name" />
        <x-forms.input label="Email" name="email" type="email" />
        <x-forms.input label="Password" name="password" type="password" />
        <x-forms.input label="Password Confirmation" name="password_confirmation" type="password" />

        <x-forms.divider />

        <x-forms.select label="Sign up as" name="role" id="role">
            <option value="job_seeker" @selected(old('role', 'job_seeker') === 'job_seeker')>Job Seeker</option>
            <option value="company" @selected(old('role') === 'company')>Company</option>
            <option value="employer" @selected(old('role') === 'employer')>Employer</option>
        </x-forms.select>

        <div id="company-fields" @class(['space-y-6', 'hidden' => old('role') !== 'company'])>
            <x-forms.input label="Company Name" name="company_name" />
            <x-forms.input label="Company Logo" name="logo" type="file" />
        </div>

        <div id="employer-fields" @class(['space-y-6', 'hidden' => old('role') !== 'employer'])>
            <x-forms.select label="Company" name="company_id">
                <option value="">Select a company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </x-forms.select>
        </div>

        <x-forms.button>Create Account</x-forms.button>
    </x-forms.form>
</x-layout>

<script>
    const roleSelect = document.getElementById('role');
    const companyFields = document.getElementById('company-fields');
    const employerFields = document.getElementById('employer-fields');

    roleSelect.addEventListener('change', () => {
        companyFields.classList.toggle('hidden', roleSelect.value !== 'company');
        employerFields.classList.toggle('hidden', roleSelect.value !== 'employer');
    });
</script>
