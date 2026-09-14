<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke()
    {
        $search = trim((string) request('q'));

        $jobs = Job::query()
            ->with(['employer', 'tags'])
            ->withCount('applications')
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('employer', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('tags', fn ($query) => $query->where('name', 'like', "%{$search}%"));
            })
            ->get();

        return view('results', ['jobs' => $jobs]);
    }
}
