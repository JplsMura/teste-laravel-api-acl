<?php

use function Pest\Laravel\getJson;
use App\Models\User;
use App\Models\Permission;

it('unautenticated user cannot get our data', function() {
    getJson(route('auth.me'), [])
        ->assertJson([
            'message' => 'Unauthenticated.'
        ])
        ->assertStatus(401);
});

it('should get user with our data', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    getJson(route('auth.me'), [
        'Authorization' => "Bearer {$token}"
    ])
    ->assertJsonStructure([
        'data'  => [
            'id',
            'name',
            'email',
            'permissions' => []
        ]
    ])
    ->assertStatus(200);
});

it('should get user with our data and permissions', function () {
    Permission::factory()->count(10)->create();
    $permissionsId = Permission::factory()->count(10)->create()->pluck('id')->toArray();
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $user->permissions()->attach($permissionsId);

    getJson(route('auth.me'), [
        'Authorization' => "Bearer {$token}"
    ])
    ->assertJsonStructure([
        'data'  => [
            'id',
            'name',
            'email',
            'permissions' => [
                '*' => [
                    'id',
                    'name',
                    'description'
                ]
            ]
        ]
    ])
    ->assertJsonCount(10, 'data.permissions')
    ->assertStatus(200);
});