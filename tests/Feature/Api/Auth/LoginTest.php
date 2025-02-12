<?php

use App\Models\User;
use function Pest\Laravel\postJson;

it('show auth user', function () {

    $user = User::factory()->create();

    $data = [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'test',
    ];

    postJson(route('auth.login'), $data)
        ->assertJsonStructure(['token'])
        ->assertStatus(200);
});

it('should fail auth - with wrong password', function () {
    $user = User::factory()->create();

    $data = [
        'email' => $user->email,
        'password' => 'wooo',
        'device_name' => 'test',
    ];

    postJson(route('auth.login'), $data)->assertStatus(422);
});

it('should fail auth - with wrong email', function () {
    $user = User::factory()->create();

    $data = [
        //'email' => 'fake@gmail.com',
        'password' => 'password',
        'device_name' => 'test',
    ];

    postJson(route('auth.login'), $data)->assertStatus(422);
});

describe('validations', function () {
    it('should require email', function () {
        postJson(route('auth.login'), [
            'password' => 'password',
            'device_name' => 'test',
        ])
        ->assertJsonValidationErrors([
            'email' => trans('validation.required', ['attribute' => 'email']),
        ])
        ->assertStatus(422);
    });

    it('should require email caracter', function () {
        postJson(route('auth.login'), [
            'email' => 'fake',
            'password' => 'password',
            'device_name' => 'test',
        ])
            ->assertJsonValidationErrors([
                'email' => trans('validation.email', ['attribute' => 'email']),
            ])
            ->assertStatus(422);
    });

    it('should require password', function () {

        $user = User::factory()->create();

        postJson(route('auth.login'), [
            'email' => $user->email,
            'device_name' => 'test',
        ])
            ->assertJsonValidationErrors([
                'password' => trans('validation.required', ['attribute' => 'password']),
            ])
            ->assertStatus(422);
    });

    it('should require device_name', function () {

        $user = User::factory()->create();

        postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertJsonValidationErrors([
                'device_name' => trans('validation.required', ['attribute' => 'device name']),
            ])
            ->assertStatus(422);
    });
});
