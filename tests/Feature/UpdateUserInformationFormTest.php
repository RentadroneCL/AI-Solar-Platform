<?php

use App\Models\User;
use function Pest\Faker\faker;
use App\Http\Livewire\UpdateUserInformationForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('update user information form', function () {
    $this->actingAs($user = User::factory()->create())
        ->livewire(UpdateUserInformationForm::class, ['user' => $user])
        ->assertViewIs('livewire.update-user-information-form')
        ->set('state.name', faker()->name)
        ->set('state.email', faker()->email)
        ->call('update')
        ->assertStatus(200);
});
