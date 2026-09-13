<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = $request->input('role');

        if (! $role && $request->filled('employer')) {
            $role = 'employer';
        }

        $request->merge([
            'role' => $role ?: 'job_seeker',
            'company_name' => $request->input('company_name', $request->input('employer')),
        ]);

        $attributes = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => ['required', 'in:employer,job_seeker'],
            'company_name' => ['required_if:role,employer'],
            'logo' => ['nullable', File::types(['png', 'jpg', 'webp'])],
        ]);

        $user = DB::transaction(function () use ($request, $attributes) {
            $user = User::create(Arr::only($attributes, [
                'name',
                'email',
                'password',
                'role',
            ]));

            if ($attributes['role'] === 'employer') {
                $logoPath = $request->file('logo')?->store('logos', 'public');
                $companyName = $attributes['company_name'];

                $user->company()->create([
                    'name' => $companyName,
                    'logo' => $logoPath,
                ]);

                $user->employer()->create([
                    'name' => $companyName,
                    'logo' => $logoPath,
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
