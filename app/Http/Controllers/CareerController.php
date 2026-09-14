<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));
        $schedule = $request->input('schedule');
        $companyId = $request->input('company_id');
        $location = trim((string) $request->input('location'));
        $tag = $request->input('tag');
        $salary = $request->input('salary');
        $jobs = Job::query()
            ->with(['employer', 'tags'])
            ->withCount('applications')
            ->when(in_array($schedule, ['Full Time', 'Part Time'], true), fn ($query) => $query->where('schedule', $schedule))
            ->when($companyId, fn ($query) => $query->whereHas('employer', fn ($query) => $query
                ->where('company_id', $companyId)
                ->orWhereHas('user.company', fn ($query) => $query->whereKey($companyId))))
            ->when($location, fn ($query) => $query->where('location', 'like', "%{$location}%"))
            ->when($tag, fn ($query) => $query->whereHas('tags', fn ($query) => $query->where('tags.id', $tag)))
            ->when($salary, fn ($query) => $query->where('salary', $salary))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('employer', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('tags', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('careers.index', [
            'jobs' => $jobs,
            'search' => $search,
            'schedule' => $schedule,
            'companyId' => $companyId,
            'location' => $location,
            'tag' => $tag,
            'salary' => $salary,
            'companies' => Company::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
            'salaries' => Job::query()->select('salary')->distinct()->orderBy('salary')->pluck('salary'),
        ]);
    }

    public function show(Job $job): View
    {
        $job->load(['employer', 'tags'])->loadCount('applications');
        $hasApplied = auth()->check() && auth()->user()->jobSeeker
            && $job->jobSeekers()->whereKey(auth()->user()->jobSeeker->id)->exists();

        return view('careers.show', compact('job', 'hasApplied'));
    }
}
