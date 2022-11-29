<?php

use App\Models\{User, Role};
use App\Http\Livewire\SyncUserRolesForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a sync of the user\'s roles livewire component', function () {
    $userAccount = User::factory()->create();
    $this->actingAs($user = User::factory()->create())
        ->livewire(SyncUserRolesForm::class, ['user' => $userAccount])
        ->assertViewIs('livewire.sync-user-roles-form')
        ->assertStatus(200);
});

it('can sync new roles to the user', function () {
    $userAccount = User::factory()->create();
    $role = Role::factory()->create([
        'name' => 'random-role',
        'guard_name' => 'web',
    ]);
    $this->actingAs($user = User::factory()->create())
        ->livewire(SyncUserRolesForm::class, ['user' => $userAccount])
        ->assertViewIs('livewire.sync-user-roles-form')
        ->set('state.roles', [$role->id])
        ->call('update')
        ->assertEmitted('saved')
        ->assertStatus(200);
});
