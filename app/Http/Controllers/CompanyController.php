<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = Company::query()
            ->withCount('jobs')
            ->latest()
            ->get();

        return view('companies.index', [
            'companies' => $companies,
        ]);
    }

    public function show(Company $company): View
    {
        $company->load(['jobs' => fn ($query) => $query->with(['employer', 'tags'])->withCount('applications')->latest()]);

        return view('companies.show', compact('company'));
    }
}
