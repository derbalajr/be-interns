<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Read: Any authenticated user (including Agents).
     */
    public function index()
    {
        Gate::authorize('view-projects');

        $projects = Project::latest()->paginate(15);

        return ProjectResource::collection($projects);
    }

    /**
     * Write: Manager / Admin only (enforced via StoreProjectRequest).
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());

        return new ProjectResource($project);
    }

    /**
     * Read: Any authenticated user (including Agents).
     */
    public function show(Project $project)
    {
        Gate::authorize('view-projects');

        return new ProjectResource($project);
    }

    /**
     * Write: Manager / Admin only (enforced via UpdateProjectRequest).
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return new ProjectResource($project);
    }

    /**
     * Write: Manager / Admin only.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete-projects');

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ], 200);
    }
}