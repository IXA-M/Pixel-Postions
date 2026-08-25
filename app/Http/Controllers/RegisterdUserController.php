<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegisterdUserController extends Controller
{
    //
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'employer' => 'required',
            'logo' => 'required|image|max:2048',
        ]);

        $logo = $request->file('logo');

        if (! $logo || ! $logo->isValid()) {
            return back()->withErrors(['logo' => 'The logo upload was not received correctly.'])->withInput();
        }

        $filename = Str::uuid().'.'.($logo->extension() ?: 'jpg');
        $logoPath = 'logos/'.$filename;
        $temporaryPath = $logo->getPathname();

        if (! $temporaryPath || ! is_file($temporaryPath)) {
            return back()->withErrors(['logo' => 'The uploaded logo is no longer available. Please choose it again.'])->withInput();
        }

        $contents = file_get_contents($temporaryPath);

        if ($contents === false || ! Storage::disk('public')->put($logoPath, $contents)) {
            return back()->withErrors(['logo' => 'The logo could not be stored.'])->withInput();
        }

        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
        ]);
        $user->employer()->create([
            'name' => $attributes['employer'],
            'logo' => $logoPath,
        ]);
        Auth::login($user);

        return redirect('/');
    }
}
