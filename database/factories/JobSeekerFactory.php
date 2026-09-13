<?php

namespace Database\Factories;

use App\Models\JobSeeker;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobSeeker>
 */
class JobSeekerFactory extends Factory
{
    protected $model = JobSeeker::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
