<?php

use App\Models\{User, Site};
use App\Http\Livewire\DeleteSiteForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains a delete site form livewire component', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteSiteForm::class, ['site' => $site])
        ->set('password', 'password')
        ->assertViewIs('livewire.delete-site-form');
});

it('display modal for confirming site deletion', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteSiteForm::class, ['site' => $site])
        ->assertViewIs('livewire.delete-site-form')
        ->call('confirmSiteDeletion')
        ->assertSet('password', '')
        ->assertSet('confirmingSiteDeletion', true)
        ->assertDispatchedBrowserEvent('confirming-delete-site')
        ->assertStatus(200);
});

it('can delete a site', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteSiteForm::class, ['site' => $site])
        ->assertViewIs('livewire.delete-site-form')
        ->set('password', 'password')
        ->call('destroy')
        ->assertStatus(200);
});

it('fails with the wrong password', function () {
    $user = User::factory()->create();
    $site = Site::factory()->create();
    $this->actingAs($user)
        ->livewire(DeleteSiteForm::class, ['site' => $site])
        ->assertViewIs('livewire.delete-site-form')
        ->set('password', 'wrong password')
        ->call('destroy')
        ->assertHasErrors('password')
        ->assertStatus(200);
});
