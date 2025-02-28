<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\TaskStoreRequest;
use App\Http\Requests\API\TaskUpdateRequest;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        // Retrieve the tasks of the specified project
        $tasks = $project->tasks;

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request, Project $project)
    {
        // Ensure the task due date is valid
        if (!$this->validateDueDate($request->due_date, $project->due_date)) {
            return response()->json($this->invalidDueDateResponse(), 422);
        }
        // Create a new task for the specified project and add auth user id to the task
        $task = $project->tasks()->create($request->all() + ['user_id' => auth()->id()]);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Task $task)
    {
        // Ensure the task belongs to the specified project
        if ($task->project_id !== $project->id) {
            return response()->json(['error' => 'Task not found in this project'], 404);
        }

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, Project $project, Task $task)
    {
        // Ensure the task belongs to the specified project
        if ($task->project_id !== $project->id) {
            return response()->json(['error' => 'Task not found in this project'], 404);
        }

        // Ensure the task due date is valid
        if (!$this->validateDueDate($request->due_date, $project->due_date)) {
            return response()->json($this->invalidDueDateResponse(), 422);
        }

        $task->update($request->all());

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        // Ensure the task belongs to the specified project
        if ($task->project_id !== $project->id) {
            return response()->json(['error' => 'Task not found in this project'], 404);
        }

        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * Validate the due date of a task.
     */
    private function validateDueDate($dueDate, $projectDueDate)
    {
        return Carbon::parse($dueDate)->lte(Carbon::parse($projectDueDate));
    }

    /**
     * Return an invalid due date response.
     */
    private function invalidDueDateResponse()
    {
        return [
            'message' => 'The given data was invalid.',
            'errors' => [
                'due_date' => ['The task due date must be before or equal to the project due date.'],
            ],
        ];
    }
}
