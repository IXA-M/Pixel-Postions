<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employer;
use App\Models\JobSeeker;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'testcompany',
            'email' => 'testcompany@test.com',
            'password' => '12345678',
            'role' => 'employer',
        ]);

        Company::create([
            'user_id' => $user->id,
            'name' => 'Pixel Positions Demo Company',
            'logo' => null,
            'description' => 'A demo company account for local development.',
            'website' => 'https://example.com',
            'location' => 'Remote',
        ]);

        Employer::create([
            'user_id' => $user->id,
            'name' => 'Pixel Positions Demo Company',
            'logo' => '',
        ]);

        $jobSeeker = User::create([
            'name' => 'testuser',
            'email' => 'testuser@test.com',
            'password' => '12345678',
            'role' => 'job_seeker',
        ]);

        JobSeeker::create([
            'user_id' => $jobSeeker->id,
            'headline' => 'Demo Normal User',
            'bio' => 'A demo normal user account for local development.',
            'location' => 'Remote',
        ]);
    }
}
