<?php

use App\Models\User;
use function Pest\Faker\faker;
use App\Http\Livewire\UserManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a user management livewire component', function () {
    $users = User::factory()->count(10)->make();
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(UserManagement::class, ['users' => $users])
        ->assertViewIs('livewire.user-management')
        ->assertStatus(200);
});

it('can create a new user', function () {
    $users = User::factory()->count(10)->make();
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(UserManagement::class, ['users' => $users])
        ->assertViewIs('livewire.user-management')
        ->set('state.name', faker()->name)
        ->set('state.email', faker()->safeEmail())
        ->set('state.password', 'secret')
        ->set('state.password_confirmation', 'secret')
        ->call('store')
        ->assertStatus(200);
});

it('can generate a random password', function () {
    $users = User::factory()->count(10)->make();
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(UserManagement::class, ['users' => $users])
        ->assertViewIs('livewire.user-management')
        ->call('randomPassword')
        ->assertSet('confirmingRandomPassword', true)
        ->assertStatus(200);
});
