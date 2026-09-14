<?php

use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the companies page lists registered companies', function () {
    Company::factory()->create(['name' => 'Pixel Labs']);

    $response = $this->get('/companies');

    $response->assertOk();
    $response->assertSee('Pixel Labs');
});

test('a company user sees a link to their company profile', function () {
    $user = User::factory()->create(['role' => 'company']);
    $company = Company::factory()->for($user)->create([
        'name' => 'My Company',
        'description' => 'Company information.',
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertSee(route('companies.show', $company), false);
    $response->assertSee('My Company');

    $companyResponse = $this->actingAs($user)->get(route('companies.show', $company));
    $companyResponse->assertOk();
    $companyResponse->assertSee('Company information.');
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
