<?php

use Illuminate\Support\Carbon;
use App\Http\Livewire\InspectionAnnotations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Annotation, Inspection, User, Site};

use function Pest\Faker\faker;

uses(RefreshDatabase::class);

it('contains an inspection annotations livewire component', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->assertStatus(200);
});

it('can show the form for editing the specified resource', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->call('edit', $annotation->toArray())
        ->assertSet('displayOverlayPane', true)
        ->assertStatus(200);
});

it('administrator or owner can update the specified resource in storage', function () {
    $user = User::factory()->withPersonalTeam()->create()->assignRole('administrator');
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'title' => faker()->text(),
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'created_at' => '2022-09-19T17:03:00.000000Z',
                    'updated_at' => '2022-10-06T17:27:18.000000Z',
                    'current_team_id' => $user->currentTeam->id,
                    'email_verified_at' => null,
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=J+D&color=7F9CF5&background=EBF4FF',
                    'profile_photo_path' => null,
                ],
                [
                    'id' => 2,
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'created_at' => '2022-09-19T17:03:00.000000Z',
                    'updated_at' => '2022-10-06T17:27:18.000000Z',
                    'current_team_id' => $user->currentTeam->id,
                    'email_verified_at' => null,
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=J+D&color=7F9CF5&background=EBF4FF',
                    'profile_photo_path' => null,
                ],
            ],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $state = [
        'user_id' => $user->id,
        'annotation_id' => $annotation->id,
        'content' => $annotation->content,
        'custom_properties' => [...$annotation->custom_properties]
    ];
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->set('state', $state)
        ->call('update')
        ->assertEmitted('updated-annotation-row')
        ->assertDispatchedBrowserEvent('alert')
        ->assertStatus(200);
});

it('fails to update the specified resource in storage when it\'s not the administrator or owner', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'title' => faker()->text(),
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'created_at' => '2022-09-19T17:03:00.000000Z',
                    'updated_at' => '2022-10-06T17:27:18.000000Z',
                    'current_team_id' => $user->currentTeam->id,
                    'email_verified_at' => null,
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=J+D&color=7F9CF5&background=EBF4FF',
                    'profile_photo_path' => null,
                ],
                [
                    'id' => 2,
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                    'created_at' => '2022-09-19T17:03:00.000000Z',
                    'updated_at' => '2022-10-06T17:27:18.000000Z',
                    'current_team_id' => $user->currentTeam->id,
                    'email_verified_at' => null,
                    'profile_photo_url' => 'https://ui-avatars.com/api/?name=J+D&color=7F9CF5&background=EBF4FF',
                    'profile_photo_path' => null,
                ],
            ],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $state = [
        'user_id' => $user->id,
        'annotation_id' => $annotation->id,
        'content' => $annotation->content,
        'custom_properties' => [...$annotation->custom_properties]
    ];
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->set('state', $state)
        ->call('update')
        ->assertDispatchedBrowserEvent('alert')
        ->assertStatus(200);
});

it('can handle the delete event.', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->call('delete', $annotation->toArray())
        ->assertSet('confirmingAnnotationDeletion', true)
        ->assertStatus(200);
});

it('administrator or owner can delete the specified resource in storage', function () {
    $user = User::factory()->withPersonalTeam()->create()->assignRole('administrator');
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $state = [...$annotation->toArray()];
    $state['annotation_id'] = $annotation->id;
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->set('password', 'password')
        ->set('state', $state)
        ->call('destroy')
        ->assertEmitted('deleted-annotation-row')
        ->assertDispatchedBrowserEvent('alert')
        ->assertStatus(200);
});

it('fails when administrator or owner tries delete the specified resource in storage with the wrong password', function () {
    $user = User::factory()->withPersonalTeam()->create()->assignRole('administrator');
    $inspection = Inspection::factory()->create();
    $annotation = Annotation::factory()->create([
        'user_id' => $user->id,
        'annotable_type' => Inspection::class,
        'annotable_id' => $inspection->id,
        'content' => faker()->text(),
        'custom_properties' => [
            'status' => 'to_do',
            'priority' => 'low',
            'assignees' => [],
            'commissioning_at' => Carbon::now()->toDateString(),
        ],
    ]);
    $state = [...$annotation->toArray()];
    $state['annotation_id'] = $annotation->id;
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->set('password', 'wrong-password')
        ->set('state', $state)
        ->call('destroy')
        ->assertDispatchedBrowserEvent('alert')
        ->assertStatus(200);
});

it('can reset the component state', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->call('resetState')
        ->assertSet('password', '')
        ->assertSet('confirmingAnnotationDeletion', false)
        ->assertSet('displayOverlayPane', false)
        ->assertSet('suggestions', $user->currentTeam->users)
        ->assertSet('state', [
            'user_id' => $user->id,
            'annotation_id' => null,
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
        ->assertStatus(200);
});

it('can add the selected user to the current custom properties array state', function () {
    $user = User::factory()->withPersonalTeam()->create()->assignRole('administrator');
    $assignee = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->call('addAssigned', $assignee)
        ->assertStatus(200);
});

it('fails to push the selected assigned to the current state when it\'s not the administrator or owner', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->call('addAssigned', $user)
        ->assertStatus(403);
});

it('fails to remove the selected assigned in the current state when it\'s not the administrator or owner', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->call('removeAssigned', $user)
        ->assertStatus(403);
});

it('can remove the selected user to the current custom properties array state', function () {
    $user = User::factory()->withPersonalTeam()->create()->assignRole('administrator');
    $assignee = User::factory()->create();
    $inspection = Inspection::factory()->create();
    $this->actingAs($user)
        ->livewire(InspectionAnnotations::class, ['inspection' => $inspection])
        ->assertViewIs('livewire.inspection-annotations')
        ->set('state.custom_properties.assignees', [$assignee->toArray()])
        ->call('removeAssigned', $assignee)
        ->assertStatus(200);
});
