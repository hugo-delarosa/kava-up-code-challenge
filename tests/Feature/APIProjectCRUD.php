<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Project;
use App\Models\User;

uses(RefreshDatabase::class);

const PROJECT_ENDPOINT = '/api/projects';
const PROJECT_NAME = 'Project Name';
const PROJECT_DESCRIPTION = 'Project Description';
const PROJECT_DUE_DATE = '2023-12-31';
const UPDATED_PROJECT_NAME = 'Updated Project Name';
const UPDATED_PROJECT_DESCRIPTION = 'Updated Project Description';
const UPDATED_PROJECT_DUE_DATE = '2024-01-01';

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->postJson('/api/login', [
        'email' => $this->user->email,
        'password' => 'password',
    ], ['Accept' => 'application/json'])->json('token');
});

it('creates a project successfully', function () {
    $response = $this->postJson(PROJECT_ENDPOINT, [
        'name' => PROJECT_NAME,
        'description' => PROJECT_DESCRIPTION,
        'due_date' => PROJECT_DUE_DATE,
    ], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure(['id', 'name', 'description', 'due_date']);
});

it('validates required fields when creating a project', function () {
    $response = $this->postJson(PROJECT_ENDPOINT, [], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'description', 'due_date']);
});

it('retrieves a project successfully', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson(PROJECT_ENDPOINT . '/' . $project->id, [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['id', 'name', 'description', 'due_date']);
});

it('updates a project successfully', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->putJson(PROJECT_ENDPOINT . '/' . $project->id, [
        'name' => UPDATED_PROJECT_NAME,
        'description' => UPDATED_PROJECT_DESCRIPTION,
        'due_date' => UPDATED_PROJECT_DUE_DATE,
    ], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(200);
    $response->assertJson(['name' => UPDATED_PROJECT_NAME, 'description' => UPDATED_PROJECT_DESCRIPTION, 'due_date' => UPDATED_PROJECT_DUE_DATE]);
});

it('validates required fields when updating a project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    // Test with invalid data
    $response = $this->putJson(PROJECT_ENDPOINT . '/' . $project->id, [
        'name' => '',
        'description' => '',
        'due_date' => 'invalid-date',
    ], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'description', 'due_date']);
});

it('soft deletes a project successfully', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson(PROJECT_ENDPOINT . '/' . $project->id, [], [
        'Accept' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
    ]);

    $response->assertStatus(204);
    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});
