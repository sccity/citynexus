<?php

use App\Models\User;

test('guests are redirected to keycloak login', function () {
    $response = $this->get('/');
    $response->assertRedirect(route('login.keycloak'));
});

test('authenticated users are redirected to dashboard', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/');
    $response->assertRedirect(route('dashboard'));
});
