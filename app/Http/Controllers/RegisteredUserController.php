<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth.register', [
            'companies' => Company::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = $request->input('role');

        $request->merge([
            'role' => $role ?: 'job_seeker',
        ]);

        $attributes = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => ['required', 'in:company,employer,job_seeker'],
            'company_name' => ['required_if:role,company', 'nullable', 'string', 'max:255'],
            'company_id' => ['required_if:role,employer', 'nullable', 'exists:companies,id'],
            'logo' => ['nullable', File::types(['png', 'jpg', 'webp'])],
        ]);

        $user = DB::transaction(function () use ($request, $attributes) {
            $user = User::create(Arr::only($attributes, [
                'name',
                'email',
                'password',
                'role',
            ]));

            if ($attributes['role'] === 'company') {
                $logoPath = $request->file('logo')?->store('logos', 'public');
                $companyName = $attributes['company_name'];

                $user->company()->create([
                    'name' => $companyName,
                    'logo' => $logoPath,
                ]);

            } elseif ($attributes['role'] === 'employer') {
                $company = Company::findOrFail($attributes['company_id']);

                $user->employer()->create([
                    'company_id' => $company->id,
                    'name' => $user->name,
                    'logo' => $company->logo ?? '',
                ]);
            } else {
                $user->jobSeeker()->create();
            }

            return $user;
        });

        Auth::login($user);

        return redirect('/');
    }
}
