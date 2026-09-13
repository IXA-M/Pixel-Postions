<?php

use App\Models\Company;
use App\Models\Employer;
use App\Models\JobSeeker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('the profile form uses browser-compatible method spoofing', function () {
    $user = User::factory()->create(['role' => 'job_seeker']);
    JobSeeker::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    $response->assertSee('method="POST"', false);
    $response->assertSee('type="hidden" name="_method" value="PUT"', false);
});

test('a job seeker can update their profile', function () {
    $user = User::factory()->create(['role' => 'job_seeker']);
    JobSeeker::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put('/profile', [
        'name' => 'Updated Seeker',
        'email' => 'updated-seeker@example.com',
        'headline' => 'Senior Developer',
        'bio' => 'Builds thoughtful products.',
        'location' => 'Remote',
    ]);

    $response->assertRedirect('/profile');
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Seeker',
        'email' => 'updated-seeker@example.com',
    ]);
    $this->assertDatabaseHas('job_seekers', [
        'user_id' => $user->id,
        'headline' => 'Senior Developer',
        'location' => 'Remote',
    ]);
});

test('a company user can update their company profile', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'employer']);
    Company::factory()->create(['user_id' => $user->id]);
    Employer::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put('/profile', [
        'name' => 'Company Owner',
        'email' => 'owner@example.com',
        'company_name' => 'Updated Company',
        'description' => 'A better company profile.',
        'website' => 'https://example.com',
        'company_location' => 'Remote',
        'logo' => UploadedFile::fake()->image('updated-logo.png'),
    ]);

    $response->assertRedirect('/profile');
    $this->assertDatabaseHas('companies', [
        'user_id' => $user->id,
        'name' => 'Updated Company',
        'website' => 'https://example.com',
    ]);
    Storage::disk('public')->assertExists($user->fresh()->company->logo);
});
