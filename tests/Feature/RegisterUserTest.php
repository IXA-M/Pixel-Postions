<?php

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('a company can register with a company logo', function () {
    Storage::fake('public');

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'company',
        'company_name' => 'Test Company',
        'logo' => UploadedFile::fake()->createWithContent(
            'logo.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
        ),
    ]);

    $user = User::where('email', 'test@example.com')->first();

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'role' => 'company',
    ]);
    $this->assertDatabaseHas('companies', [
        'user_id' => $user->id,
        'name' => 'Test Company',
    ]);
    expect($user->company)->toBeInstanceOf(Company::class);
    Storage::disk('public')->assertExists($user->company->logo);
});

test('an employer can register and be assigned to a company', function () {
    $company = Company::factory()->create(['name' => 'Existing Company']);

    $response = $this->post('/register', [
        'name' => 'Company Employee',
        'email' => 'employee@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'employer',
        'company_id' => $company->id,
    ]);

    $user = User::where('email', 'employee@example.com')->first();

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'role' => 'employer',
    ]);
    $this->assertDatabaseHas('employers', [
        'user_id' => $user->id,
        'company_id' => $company->id,
    ]);
});

test('a user can register as a job seeker', function () {
    $response = $this->post('/register', [
        'name' => 'Job Seeker',
        'email' => 'job-seeker@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'job_seeker',
    ]);

    $user = User::where('email', 'job-seeker@example.com')->first();

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'role' => 'job_seeker',
    ]);
    $this->assertDatabaseHas('job_seekers', [
        'user_id' => $user->id,
    ]);
});
