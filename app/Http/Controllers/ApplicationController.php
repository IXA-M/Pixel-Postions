<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicationRequest $request, Job $job)
    {
        $jobSeeker = $request->user()->jobSeeker;
        abort_unless($jobSeeker, 403, 'Only job seekers can apply for jobs.');

        Application::firstOrCreate([
            'job_id' => $job->id,
            'job_seeker_id' => $jobSeeker->id,
        ], ['status' => 'pending']);

        return back()->with('success', 'Your application has been submitted.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        //
    }
}
