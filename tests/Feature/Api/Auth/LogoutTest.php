<?php

use App\Models\User;
use function Pest\Laravel\postJson;

it('user anaauthencated cannot logout', function () {
    postJson(route('auth.logout'), [], [])
    ->assertJson([
        'message' => 'Unauthenticated.'
    ])
    ->assertStatus(401);
});

it('user authencated should logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    postJson(route('auth.logout'), [], [
        'Authorization' => 'Bearer ' . $token,
    ])->assertStatus(204);
});