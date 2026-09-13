<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'company_id',
        'title',
        'salary',
        'location',
        'schedule',
        'url',
        'featured',
    ];

    public function tag(string $name): void
    {
        $tag = Tag::firstOrCreate(['name' => ucwords(strtolower(trim($name)))]);

        $this->tags()->syncWithoutDetaching($tag);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobSeekers(): BelongsToMany
    {
        return $this->belongsToMany(JobSeeker::class, 'applications')
            ->withPivot('status')
            ->withTimestamps();
    }
}
