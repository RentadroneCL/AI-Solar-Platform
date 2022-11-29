<?php

use function Pest\Faker\faker;
use Illuminate\Support\Carbon;
use App\Http\Livewire\Annotations;
use App\Models\{Inspection, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('contains an annotations livewire component', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $model = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(Annotations::class, ['model' => $model])
        ->assertViewIs('livewire.annotations')
        ->assertStatus(200);
});

it('set feature at pixel event', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $model = Inspection::factory()->create();
    $fakeFeatures = [
        'values_' => [
            'zone' => 'P1',
            'panel' => 01,
            'geometry' => [
                'layout' => 'XY',
                'values' => null,
            ]
        ],
    ];
    $this->actingAs($user)
        ->livewire(Annotations::class, ['model' => $model])
        ->assertViewIs('livewire.annotations')
        ->call('setFeatureAtPixel', $fakeFeatures)
        ->assertStatus(200);
});

it('reset the component state', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $model = Inspection::factory()->create();
    $fakeFeatures = [
        'values_' => [
            'zone' => 'P1',
            'panel' => 01,
            'geometry' => [
                'layout' => 'XY',
                'values' => null,
            ]
        ],
    ];
    $this->actingAs($user)
        ->livewire(Annotations::class, ['model' => $model])
        ->set('state', [
            'content' => faker()->text(),
            'custom_properties' => [
                'title' => faker()->title(),
                'assignees' => $user->currentTeam->users,
                'status' => 'to-do',
                'priority' => 'low',
                'commissioning_at' => Carbon::now()->toDateTime(),
                'feature' => [
                    'values' => $fakeFeatures['values_'],
                    'geometry' => $fakeFeatures['values_']['geometry'],
                ]
            ]
        ])
        ->call('resetState')
        ->assertSet('state', [
            'user_id' => $user->id,
            'content' => '',
            'custom_properties' => [
                'title' => '',
                'assignees' => [],
                'status' => '',
                'priority' => '',
                'commissioning_at' => null,
                'feature' => [
                    'values' => null,
                    'geometry' => null,
                ]
            ]
        ])
        ->assertSet('suggestions', $user->currentTeam->users)
        ->assertSet('assignees', collect([]))
        ->assertViewIs('livewire.annotations')
        ->assertStatus(200);
});

it('add assigned to list', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $model = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(Annotations::class, ['model' => $model])
        ->assertViewIs('livewire.annotations')
        ->call('toggleAssigned', $user)
        ->assertSet('displayAssignees', true)
        ->assertStatus(200);
});

it('remove assigned from list', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $model = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(Annotations::class, ['model' => $model])
        ->assertViewIs('livewire.annotations')
        ->call('removeAssigned', $user)
        ->assertSet('displayAssignees', false) // cause suggestions list it's empty
        ->assertStatus(200);
});

it('store a newly created resource in storage', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $model = Inspection::factory()->create();
    $fakeFeatures = [
        'values_' => [
            'zone' => 'P1',
            'panel' => 01,
            'geometry' => [
                'layout' => 'XY',
                'values' => null,
            ]
        ],
    ];
    $this->actingAs($user)
        ->livewire(Annotations::class, ['model' => $model])
        ->assertViewIs('livewire.annotations')
        ->call('toggleAssigned', $user)
        ->assertSet('displayAssignees', true)
        ->set('state', [
            'user_id' => $user->id,
            'content' => faker()->text(),
            'custom_properties' => [
                'title' => faker()->title(),
                'assignees' => $user->currentTeam->users,
                'status' => 'to-do',
                'priority' => 'low',
                'commissioning_at' => Carbon::now()->toDateTime(),
                'feature' => [
                    'values' => $fakeFeatures['values_'],
                    'geometry' => $fakeFeatures['values_']['geometry'],
                ]
            ]
        ])
        ->call('store')
        ->assertSet('displayAnnotationCreationForm', false)
        ->assertDispatchedBrowserEvent('alert')
        ->assertStatus(200);
});
