<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\View\View;

class SalaryController extends Controller
{
    public function index(): View
    {
        $salaryBands = Job::query()
            ->select('salary')
            ->selectRaw('COUNT(*) as jobs_count')
            ->groupBy('salary')
            ->orderByDesc('jobs_count')
            ->get();

        return view('salaries.index', [
            'salaryBands' => $salaryBands,
        ]);
    }
}
