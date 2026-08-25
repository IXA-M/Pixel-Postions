<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('a user can register with an employer logo', function () {
    Storage::fake('public');

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'employer' => 'Test Employer',
        'logo' => UploadedFile::fake()->createWithContent(
            'logo.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        ),
    ]);

    $user = User::where('email', 'test@example.com')->first();

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('employers', [
        'user_id' => $user->id,
        'name' => 'Test Employer',
    ]);
    Storage::disk('public')->assertExists($user->employer->logo);
});
