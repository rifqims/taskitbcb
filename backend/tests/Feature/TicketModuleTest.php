<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketModuleTest extends TestCase
{
    use RefreshDatabase;

    private function refData(): array
    {
        $this->seed(\Database\Seeders\SlaPolicySeeder::class);

        return [Division::factory()->create(), Category::factory()->create()];
    }

    public function test_client_can_create_ticket_with_generated_number_and_sla(): void
    {
        [$division, $category] = $this->refData();
        $client = User::factory()->client()->create();

        $response = $this->actingAs($client)->postJson('/api/tickets', [
            'title' => 'Printer rusak',
            'description' => 'Tidak bisa mencetak',
            'division_id' => $division->id,
            'sender_name' => 'Budi',
            'category_id' => $category->id,
            'priority' => 'high',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', 'baru')
            ->assertJsonPath('data.priority_weight', 3);

        $this->assertMatchesRegularExpression('/^TKT-\d{6}-\d{6}$/', $response->json('data.ticket_number'));
        $this->assertNotNull(Ticket::first()->sla_due_at);
    }

    public function test_it_support_cannot_create_ticket(): void
    {
        [$division, $category] = $this->refData();
        $it = User::factory()->itSupport()->create();

        $this->actingAs($it)->postJson('/api/tickets', [
            'title' => 'x', 'description' => 'y',
            'division_id' => $division->id, 'sender_name' => 'z',
            'category_id' => $category->id, 'priority' => 'low',
        ])->assertStatus(403);
    }

    public function test_index_is_ordered_by_priority_then_fifo(): void
    {
        $this->refData();
        $it = User::factory()->itSupport()->create();
        Ticket::factory()->create(['priority' => 'low', 'priority_weight' => 1]);
        Ticket::factory()->create(['priority' => 'urgent', 'priority_weight' => 4]);
        Ticket::factory()->create(['priority' => 'medium', 'priority_weight' => 2]);

        $order = collect($this->actingAs($it)->getJson('/api/tickets')->json('data'))
            ->pluck('priority')->all();

        $this->assertSame(['urgent', 'medium', 'low'], $order);
    }

    public function test_client_only_sees_own_tickets(): void
    {
        $this->refData();
        $a = User::factory()->client()->create();
        $b = User::factory()->client()->create();
        Ticket::factory()->create(['created_by' => $a->id]);
        Ticket::factory()->create(['created_by' => $b->id]);

        $data = $this->actingAs($a)->getJson('/api/tickets')->json('data');
        $this->assertCount(1, $data);
    }

    public function test_it_can_take_ticket_and_it_locks(): void
    {
        $this->refData();
        $it = User::factory()->itSupport()->create();
        $other = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create(['status' => 'baru', 'assigned_to' => null]);

        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/assign")
            ->assertOk()->assertJsonPath('data.status', 'sedang_dikerjakan');

        // Teknisi lain mencoba mengambil tiket yang sama -> 403 (policy: bukan Baru lagi).
        $this->actingAs($other)->postJson("/api/tickets/{$ticket->id}/assign")
            ->assertStatus(403);
    }

    public function test_only_assignee_can_update_progress(): void
    {
        $this->refData();
        $it = User::factory()->itSupport()->create();
        $stranger = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create([
            'status' => 'sedang_dikerjakan', 'assigned_to' => $it->id,
        ]);

        $this->actingAs($stranger)->patchJson("/api/tickets/{$ticket->id}/progress", ['progress' => 50])
            ->assertStatus(403);

        $this->actingAs($it)->patchJson("/api/tickets/{$ticket->id}/progress", ['progress' => 50])
            ->assertOk()->assertJsonPath('data.progress', 50);
    }

    public function test_progress_must_be_multiple_of_25(): void
    {
        $this->refData();
        $it = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => $it->id, 'status' => 'sedang_dikerjakan']);

        $this->actingAs($it)->patchJson("/api/tickets/{$ticket->id}/progress", ['progress' => 33])
            ->assertStatus(422);
    }

    public function test_resolve_completed_sets_status_and_progress(): void
    {
        $this->refData();
        $it = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => $it->id, 'status' => 'sedang_dikerjakan']);

        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/resolve", ['outcome' => 'completed'])
            ->assertOk()
            ->assertJsonPath('data.status', TicketStatus::Selesai->value)
            ->assertJsonPath('data.progress', 100);
    }

    public function test_resolve_rejected_requires_berita_acara(): void
    {
        $this->refData();
        $it = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => $it->id, 'status' => 'sedang_dikerjakan']);

        // Tanpa alasan/berita acara -> 422
        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/resolve", ['outcome' => 'rejected'])
            ->assertStatus(422);

        // Lengkap -> sukses, status Ditolak
        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/resolve", [
            'outcome' => 'rejected',
            'reason' => 'Perangkat rusak fisik',
            'berita_acara' => 'Sudah dicek, mainboard mati',
            'recommendation' => 'Ganti unit baru',
        ])->assertOk()->assertJsonPath('data.status', TicketStatus::Ditolak->value);
    }
}
