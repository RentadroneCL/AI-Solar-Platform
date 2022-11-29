<?php

use App\Models\User;
use App\Http\Livewire\UserTable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users table', function () {
    $this->actingAs($user = User::factory()->create())
        ->livewire(UserTable::class, ['model' => User::class])
        ->assertViewIs('livewire-tables::datatable')
        ->assertStatus(200);
});
