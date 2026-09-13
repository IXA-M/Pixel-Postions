<?php

use App\Models\Employer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an employer can publish a featured job with tags', function () {
    $employer = Employer::factory()->create();

    $response = $this->actingAs($employer->user)->post('/jobs', [
        'title' => 'Credit Analyst',
        'salary' => 85000,
        'location' => 'Remote',
        'schedule' => 'Full Time',
        'url' => 'https://example.com/jobs/credit-analyst',
        'featured' => '1',
        'tags' => 'Finance, Analysis',
    ]);

    $response->assertRedirect('/');
    $this->assertDatabaseHas('jobs', [
        'employer_id' => $employer->id,
        'title' => 'Credit Analyst',
        'featured' => true,
    ]);
    $this->assertDatabaseHas('tags', ['name' => 'Finance']);
    $this->assertDatabaseHas('tags', ['name' => 'Analysis']);

    expect($employer->jobs()->latest()->first()->tags)->toHaveCount(2);
});

test('a normal user cannot access job creation', function () {
    $user = User::factory()->create(['role' => 'job_seeker']);

    $this->actingAs($user)->get('/jobs/create')->assertForbidden();

    $this->actingAs($user)->post('/jobs', [
        'title' => 'Unauthorized Job',
        'salary' => '$90,000 USD',
        'location' => 'Remote',
        'schedule' => 'Full Time',
        'url' => 'https://example.com/jobs/unauthorized',
    ])->assertForbidden();

    $this->assertDatabaseMissing('jobs', ['title' => 'Unauthorized Job']);
});
