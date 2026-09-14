<?php

use App\Models\Application;
use App\Models\Company;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobSeeker;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the public careers page can search jobs by title and location', function () {
    Job::factory()->create([
        'title' => 'Laravel Engineer',
        'location' => 'New York',
    ]);
    Job::factory()->create([
        'title' => 'Product Designer',
        'location' => 'Remote',
    ]);

    $response = $this->get('/careers?q=Laravel');

    $response->assertOk();
    $response->assertSee('Laravel Engineer');
    $response->assertDontSee('Product Designer');
});

test('the public careers page can search jobs by tag', function () {
    $job = Job::factory()->create(['title' => 'Backend Engineer']);
    $job->tags()->attach(Tag::factory()->create(['name' => 'PHP']));
    Job::factory()->create(['title' => 'Frontend Engineer']);

    $response = $this->get('/careers?q=PHP');

    $response->assertOk();
    $response->assertSee('Backend Engineer');
    $response->assertDontSee('Frontend Engineer');
});

test('the careers search can filter jobs by schedule', function () {
    Job::factory()->create([
        'title' => 'Full Time Engineer',
        'schedule' => 'Full Time',
    ]);
    Job::factory()->create([
        'title' => 'Part Time Engineer',
        'schedule' => 'Part Time',
    ]);

    $response = $this->get('/careers?q=Engineer&schedule=Part+Time');

    $response->assertOk();
    $response->assertSee('Part Time Engineer');
    $response->assertDontSee('Full Time Engineer');
});

test('the careers search can combine company, location, tag, and salary filters', function () {
    $companyUser = User::factory()->create(['role' => 'company']);
    $company = Company::factory()->for($companyUser)->create(['name' => 'Pixel Labs']);
    $employer = Employer::factory()->create(['company_id' => $company->id]);
    $matchingJob = Job::factory()->for($employer)->create([
        'title' => 'Filtered Engineer',
        'location' => 'New York',
        'salary' => '$120,000 USD',
    ]);
    $matchingJob->tags()->attach(Tag::factory()->create(['name' => 'PHP']));
    Job::factory()->create(['title' => 'Other Engineer', 'location' => 'Remote', 'salary' => '$90,000 USD']);

    $response = $this->get('/careers?company_id='.$company->id.'&location=New+York&tag='.$matchingJob->tags->first()->id.'&salary='.urlencode('$120,000 USD'));

    $response->assertOk();
    $response->assertSee('Filtered Engineer');
    $response->assertDontSee('Other Engineer');
});

test('the job detail page embeds its real location in Google Maps', function () {
    $job = Job::factory()->create(['location' => 'Winter Park, Florida']);

    $response = $this->get(route('careers.show', $job));

    $response->assertOk();
    $response->assertSee('https://www.google.com/maps?q=Winter+Park%2C+Florida&output=embed', false);
    $response->assertSee('Winter Park, Florida');
});

test('a company page lists jobs belonging to that company', function () {
    $user = User::factory()->create(['role' => 'employer']);
    $company = Company::factory()->for($user)->create(['name' => 'Pixel Labs']);
    $employer = Employer::factory()->for($user)->create([
        'company_id' => $company->id,
        'name' => 'Pixel Labs',
    ]);
    Job::factory()->for($employer)->create(['title' => 'Laravel Developer']);

    $response = $this->get(route('companies.show', $company));

    $response->assertOk();
    $response->assertSee('Pixel Labs');
    $response->assertSee('Laravel Developer');
});

test('company users and associated employers only see application counts for their company', function () {
    $companyUser = User::factory()->create(['role' => 'company']);
    $company = Company::factory()->for($companyUser)->create();
    $employerUser = User::factory()->create(['role' => 'employer']);
    $employer = Employer::factory()->for($employerUser)->create(['company_id' => $company->id]);
    $otherEmployer = Employer::factory()->create();
    $companyJob = Job::factory()->for($employer)->create(['title' => 'Company Job']);
    $otherJob = Job::factory()->for($otherEmployer)->create(['title' => 'Other Job']);
    $jobSeeker = JobSeeker::factory()->create();
    Application::factory()->create(['job_id' => $companyJob->id, 'job_seeker_id' => $jobSeeker->id]);
    Application::factory()->create(['job_id' => $otherJob->id, 'job_seeker_id' => $jobSeeker->id]);
    Application::factory()->create(['job_id' => $otherJob->id, 'job_seeker_id' => JobSeeker::factory()]);

    $companyResponse = $this->actingAs($companyUser)->get('/careers');
    $companyResponse->assertDontSee('1 application');
    $companyResponse->assertDontSee('2 applications');

    $employerResponse = $this->actingAs($employerUser)->get('/careers');
    $employerResponse->assertDontSee('1 application');
    $employerResponse->assertDontSee('2 applications');

    $companyTabResponse = $this->actingAs($companyUser)->get(route('companies.show', $company));
    $companyTabResponse->assertSee('1 application');
    $companyTabResponse->assertDontSee('2 applications');

    $employerCompanyTabResponse = $this->actingAs($employerUser)->get(route('companies.show', $company));
    $employerCompanyTabResponse->assertSee('1 application');
    $employerCompanyTabResponse->assertDontSee('2 applications');
});

test('a logged in job seeker can apply for a job', function () {
    $user = User::factory()->create(['role' => 'job_seeker']);
    $jobSeeker = JobSeeker::factory()->for($user)->create();
    $job = Job::factory()->create(['title' => 'PHP Developer']);

    $response = $this->actingAs($user)->post(route('applications.store', $job));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Your application has been submitted.');
    $this->assertDatabaseHas('applications', [
        'job_id' => $job->id,
        'job_seeker_id' => $jobSeeker->id,
        'status' => 'pending',
    ]);
});

test('a job seeker cannot submit more than one application for a job', function () {
    $user = User::factory()->create(['role' => 'job_seeker']);
    $jobSeeker = JobSeeker::factory()->for($user)->create();
    $job = Job::factory()->create();

    $this->actingAs($user)->post(route('applications.store', $job));
    $this->actingAs($user)->post(route('applications.store', $job));

    expect(Application::query()
        ->where('job_id', $job->id)
        ->where('job_seeker_id', $jobSeeker->id)
        ->count())->toBe(1);
});

test('guests and employers cannot apply for jobs', function () {
    $job = Job::factory()->create();

    $this->post(route('applications.store', $job))->assertRedirect('/login');

    $employer = User::factory()->create(['role' => 'employer']);
    $this->actingAs($employer)
        ->post(route('applications.store', $job))
        ->assertForbidden();
});
