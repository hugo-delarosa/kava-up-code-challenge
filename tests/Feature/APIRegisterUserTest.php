<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Constants
const REGISTER_ENDPOINT = 'api/register';
const USER_TABLE = 'users';
const USER_NAME = 'Test User';
const USER_EMAIL = 'test@example.com';
const USER_PASSWORD = 'password';
const USER_PASSWORD_CONFIRMATION = 'password';
const USER_WRONG_PASSWORD = 'wrong_password';

// The test below checks if a user can register successfully
it('allows a user to register', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas(USER_TABLE, [
        'email' => USER_EMAIL,
    ]);
});

// The test below checks if a user can register and receive a token
it('returns a token after registration', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'token',
    ]);
});

// The test below checks if a user can register without a name
it('requires a name', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

// The test below checks if a user can register without an email
it('requires an email', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

// The test below checks if a user can register without a password
it('requires a password', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('password');
});

// The test below checks if a user can register without a password confirmation
it('requires a password confirmation', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('password');
});

// The test below checks if a user can register with a wrong password confirmation
it('requires a password confirmation to match the password', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_WRONG_PASSWORD,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('password');
});

// The test below checks if a user can register with an invalid email
it('requires a valid email', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => 'invalid-email',
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});

// The test below checks if a user can register with a weak password
it('requires a strong password', function () {
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password' => '123',
        'password_confirmation' => '123',
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('password');
});

// The test below checks if a user can register with a duplicate email
it('requires a unique email', function () {
    // First registration
    $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ]);

    // Attempt to register again with the same email
    $response = $this->post(REGISTER_ENDPOINT, [
        'name' => USER_NAME,
        'email' => USER_EMAIL,
        'password' => USER_PASSWORD,
        'password_confirmation' => USER_PASSWORD_CONFIRMATION,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');
});
