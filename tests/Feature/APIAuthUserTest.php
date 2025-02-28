<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);

// Constants
const LOGIN_ENDPOINT = '/api/login';
const USER_EMAIL = 'test@example.com';
const USER_PASSWORD = 'password';

//test for user login
it('allows a user to log in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => USER_EMAIL,
        'password' => bcrypt(USER_PASSWORD),
    ]);

    $response = $this->postJson(LOGIN_ENDPOINT, [
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});

//test for user login with invalid credentials
it('does not allow a user to log in with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => USER_EMAIL,
        'password' => bcrypt(USER_PASSWORD),
    ]);

    $response = $this->postJson(LOGIN_ENDPOINT, [
        'email' => USER_EMAIL,
        'password' => 'wrongpassword',
    ], ['Accept' => 'application/json']);

    $response->assertStatus(401);
});

//test for user login with missing credentials
it('does not allow a user to log in with missing credentials', function () {
    $response = $this->postJson(LOGIN_ENDPOINT, [
        'email' => USER_EMAIL,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('password');
});

//test for user login with missing email
it('allows a logged-in user to access a protected route', function () {
    $user = User::factory()->create([
        'email' => USER_EMAIL,
        'password' => bcrypt(USER_PASSWORD),
    ]);

    $token = $this->postJson(LOGIN_ENDPOINT, [
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
    ], ['Accept' => 'application/json'])->json('token');

    $response = $this->getJson('/api/protected-route', [
        'Authorization' => "Bearer $token",
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(200);
});

//test for user logout
it('allows a user to log out', function () {
    $user = User::factory()->create([
        'email' => USER_EMAIL,
        'password' => bcrypt(USER_PASSWORD),
    ]);

    $token = $this->postJson(LOGIN_ENDPOINT, [
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
    ], ['Accept' => 'application/json'])->json('token');

    $response = $this->postJson('/api/logout', [], [
        'Authorization' => "Bearer $token",
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(200);
});
