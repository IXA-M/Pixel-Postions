<?php

use App\Models\Employer;
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
