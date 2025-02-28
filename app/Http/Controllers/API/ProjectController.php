<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProjectStoreRequest;
use App\Http\Requests\API\ProjectUpdateRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Return a listing of the resource.
     */
    public function index()
    {
        //Get all projects associated with the authenticated user
        //For now only the ones that he created will be returned future improvements will include the ones he is assigned to
        $projects = auth()->user()->projects;

        return response()->json($projects);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectStoreRequest $request)
    {
        //Create a new project
        $project = auth()->user()->projects()->create($request->validated());

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //retrieve the project
        $project = auth()->user()->projects()->findOrFail($id);

        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectUpdateRequest $request, string $id)
    {
        //retrieve the project
        $project = auth()->user()->projects()->findOrFail($id);

        //update the project
        $project->update($request->validated());

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //retrieve the project
        $project = auth()->user()->projects()->findOrFail($id);

        //delete the project
        $project->delete();

        return response()->json(null, 204);
    }
}
