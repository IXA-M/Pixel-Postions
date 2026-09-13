<?php

use App\Models\Company;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the companies page lists registered companies', function () {
    Company::factory()->create(['name' => 'Pixel Labs']);

    $response = $this->get('/companies');

    $response->assertOk();
    $response->assertSee('Pixel Labs');
});

test('the careers page lists available jobs', function () {
    Job::factory()->create(['title' => 'Senior Laravel Developer']);

    $response = $this->get('/careers');

    $response->assertOk();
    $response->assertSee('Senior Laravel Developer');
});

test('the salaries page lists salary bands', function () {
    Job::factory()->create(['salary' => '$120,000 USD']);

    $response = $this->get('/salaries');

    $response->assertOk();
    $response->assertSee('$120,000 USD');
});
