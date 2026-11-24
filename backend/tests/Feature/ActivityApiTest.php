<?php

namespace Tests\Feature;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_activities(): void
    {
        Activity::create([
            'title' => 'Probar API',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $this->getJson('/api/activities')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Probar API');
    }

    public function test_it_creates_an_activity(): void
    {
        $this->postJson('/api/activities', [
            'title' => 'Nueva actividad',
            'description' => 'Descripción de prueba',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2026-12-01',
        ])->assertCreated()
          ->assertJsonPath('data.title', 'Nueva actividad');

        $this->assertDatabaseHas('activities', ['title' => 'Nueva actividad']);
    }

    public function test_it_rejects_invalid_activity_data(): void
    {
        $this->postJson('/api/activities', [
            'title' => '',
            'status' => 'unknown',
            'priority' => 'urgent',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['title', 'status', 'priority']);
    }

    public function test_it_updates_and_deletes_an_activity(): void
    {
        $activity = Activity::create([
            'title' => 'Original',
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $this->putJson("/api/activities/{$activity->id}", [
            'title' => 'Actualizada',
            'status' => 'completed',
        ])->assertOk()->assertJsonPath('data.title', 'Actualizada');

        $this->deleteJson("/api/activities/{$activity->id}")->assertOk();

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    public function test_it_returns_global_statistics_independent_of_filters(): void
    {
        $statuses = [
            ...array_fill(0, 5, 'pending'),
            ...array_fill(0, 4, 'in_progress'),
            ...array_fill(0, 4, 'completed'),
        ];

        foreach ($statuses as $index => $status) {
            Activity::create([
                'title' => "Actividad {$index}",
                'status' => $status,
                'priority' => 'medium',
            ]);
        }

        $this->getJson('/api/activities?status=pending')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.stats.total', 13)
            ->assertJsonPath('meta.stats.pending', 5)
            ->assertJsonPath('meta.stats.in_progress', 4)
            ->assertJsonPath('meta.stats.completed', 4);
    }

}
