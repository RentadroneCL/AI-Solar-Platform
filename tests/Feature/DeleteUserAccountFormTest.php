<?php

use App\Models\User;
use App\Http\Livewire\DeleteUserAccountForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a delete user account form livewire component', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteUserAccountForm::class, ['user' => $user])
        ->assertViewIs('livewire.delete-user-account-form')
        ->assertStatus(200);
});

it('display modal for confirming user account deletion', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteUserAccountForm::class, ['user' => $user])
        ->assertViewIs('livewire.delete-user-account-form')
        ->call('confirmUserAccountDeletion')
        ->assertSet('password', '')
        ->assertDispatchedBrowserEvent('confirming-delete-user-account')
        ->assertSet('confirmingUserAccountDeletion', true)
        ->assertStatus(200);
});

it('can delete the user account', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteUserAccountForm::class, ['user' => $user])
        ->assertViewIs('livewire.delete-user-account-form')
        ->set('password', 'password')
        ->call('destroy', $user->id)
        ->assertStatus(200);
});

it('fails with the wrong password', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteUserAccountForm::class, ['user' => $user])
        ->assertViewIs('livewire.delete-user-account-form')
        ->set('password', 'wrong password')
        ->call('destroy')
        ->assertHasErrors('password')
        ->assertStatus(200);
});
