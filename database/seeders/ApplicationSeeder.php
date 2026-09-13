<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Application;
use App\Models\JobSeeker;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::query()->get();
        $jobSeekers = JobSeeker::query()->get();

        foreach ($jobSeekers as $jobSeeker) {
            foreach ($jobs->random(min(3, $jobs->count())) as $job) {
                Application::factory()->create([
                    'job_id' => $job->id,
                    'job_seeker_id' => $jobSeeker->id,
                ]);
            }
        }
    }
}
