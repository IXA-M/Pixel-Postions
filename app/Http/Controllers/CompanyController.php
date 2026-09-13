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
}
