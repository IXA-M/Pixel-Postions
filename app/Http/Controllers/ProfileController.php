<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['company', 'employer', 'jobSeeker']);

        return view('profile.edit', [
            'user' => $user,
            'isEmployer' => $user->isEmployer() || $user->employer !== null,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isEmployer = $user->isEmployer() || $user->employer()->exists();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ];

        if ($isEmployer) {
            $rules += [
                'company_name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:5000'],
                'website' => ['nullable', 'url', 'max:255'],
                'company_location' => ['nullable', 'string', 'max:255'],
                'logo' => ['nullable', File::types(['png', 'jpg', 'webp'])],
            ];
        } else {
            $rules += [
                'headline' => ['nullable', 'string', 'max:255'],
                'bio' => ['nullable', 'string', 'max:5000'],
                'location' => ['nullable', 'string', 'max:255'],
            ];
        }

        $attributes = Validator::validate($request->all(), $rules);

        DB::transaction(function () use ($request, $attributes, $user, $isEmployer): void {
            $user->update([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
            ]);

            if ($isEmployer) {
                $company = $user->company()->firstOrCreate([
                    'user_id' => $user->id,
                ], [
                    'name' => $attributes['company_name'],
                ]);

                $companyAttributes = [
                    'name' => $attributes['company_name'],
                    'description' => $attributes['description'] ?? null,
                    'website' => $attributes['website'] ?? null,
                    'location' => $attributes['company_location'] ?? null,
                ];

                if ($request->hasFile('logo')) {
                    if ($company->logo) {
                        Storage::disk('public')->delete($company->logo);
                    }

                    $companyAttributes['logo'] = $request->file('logo')->store('logos', 'public');
                }

                $company->update($companyAttributes);

                $user->employer()->updateOrCreate([], [
                    'name' => $company->name,
                    'logo' => $company->logo ?? '',
                ]);
            } else {
                $user->jobSeeker()->updateOrCreate([], [
                    'headline' => $attributes['headline'] ?? null,
                    'bio' => $attributes['bio'] ?? null,
                    'location' => $attributes['location'] ?? null,
                ]);
            }
        });

        return redirect()->route('profile.edit')->with('status', 'Profile updated.');
    }
}
