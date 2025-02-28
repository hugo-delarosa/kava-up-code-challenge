<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;

uses(RefreshDatabase::class);

const TASK_ENDPOINT = '/api/tasks';
const TASK_NAME = 'Task Name';
const TASK_DESCRIPTION = 'Task Description';
const TASK_DUE_DATE = '2023-12-31';
const UPDATED_TASK_NAME = 'Updated Task Name';
const UPDATED_TASK_DESCRIPTION = 'Updated Task Description';
const UPDATED_TASK_DUE_DATE = '2024-01-01';

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->postJson('/api/login', [
        'email' => $this->user->email,
        'password' => 'password',
    ], ['Accept' => 'application/json'])->json('token');
    $this->project = Project::factory()->create(['user_id' => $this->user->id, 'due_date' => '2023-12-31']);
});

it('creates a task successfully', function () {
    $response = $this->postJson(TASK_ENDPOINT, [
        'name' => TASK_NAME,
        'description' => TASK_DESCRIPTION,
        'due_date' => TASK_DUE_DATE,
        'project_id' => $this->project->id,
    ], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure(['id', 'name', 'description', 'due_date', 'project_id']);
});

it('validates required fields when creating a task', function () {
    $response = $this->postJson(TASK_ENDPOINT, [], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'description', 'due_date', 'project_id']);
});

it('validates task due date is not later than project due date', function () {
    $response = $this->postJson(TASK_ENDPOINT, [
        'name' => TASK_NAME,
        'description' => TASK_DESCRIPTION,
        'due_date' => '2024-01-01', // Invalid due date
        'project_id' => $this->project->id,
    ], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['due_date']);
});

it('retrieves a task successfully', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id]);

    $response = $this->getJson(TASK_ENDPOINT . '/' . $task->id, [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['id', 'name', 'description', 'due_date', 'project_id']);
});

it('updates a task successfully', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id]);

    $response = $this->putJson(TASK_ENDPOINT . '/' . $task->id, [
        'name' => UPDATED_TASK_NAME,
        'description' => UPDATED_TASK_DESCRIPTION,
        'due_date' => UPDATED_TASK_DUE_DATE,
        'project_id' => $this->project->id,
    ], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(200);
    $response->assertJson(['name' => UPDATED_TASK_NAME, 'description' => UPDATED_TASK_DESCRIPTION, 'due_date' => UPDATED_TASK_DUE_DATE, 'project_id' => $this->project->id]);
});

it('validates required fields and data matching TaskUpdateRequest when updating a task', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id]);

    // Test with empty data
    $response = $this->putJson(TASK_ENDPOINT . '/' . $task->id, [], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'description', 'due_date', 'project_id']);

    // Test with invalid data
    $response = $this->putJson(TASK_ENDPOINT . '/' . $task->id, [
        'name' => '',
        'description' => '',
        'due_date' => 'invalid-date',
        'project_id' => $this->project->id,
    ], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'description', 'due_date']);
});

it('soft deletes a task successfully', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id]);

    $response = $this->deleteJson(TASK_ENDPOINT . '/' . $task->id, [], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(204);
    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
});
