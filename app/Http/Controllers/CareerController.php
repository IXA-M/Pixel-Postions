<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        $jobs = Job::query()
            ->with('employer')
            ->latest()
            ->get();

        return view('careers.index', [
            'jobs' => $jobs,
        ]);
    }
}
