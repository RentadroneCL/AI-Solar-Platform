<?php

use App\Models\User;
use function Pest\Faker\faker;
use App\Http\Livewire\UserSitesManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user sites management component', function () {
    $this->actingAs($user = User::factory()->create())
        ->livewire(UserSitesManagement::class, ['user' => $user])
        ->assertViewIs('livewire.user-sites-management')
        ->assertStatus(200);
});
