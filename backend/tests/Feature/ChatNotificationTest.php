<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_message_notifies_client(): void
    {
        $client = User::factory()->client()->create();
        $it = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create([
            'created_by' => $client->id, 'assigned_to' => $it->id, 'status' => 'sedang_dikerjakan',
        ]);

        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/messages", [
            'body' => 'Sedang saya cek ya',
        ])->assertCreated()->assertJsonPath('data.body', 'Sedang saya cek ya');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $client->id, 'type' => 'chat_reply', 'ticket_id' => $ticket->id,
        ]);
    }

    public function test_mention_creates_notification(): void
    {
        $client = User::factory()->client()->create();
        $it = User::factory()->itSupport()->create();
        $other = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create(['created_by' => $client->id, 'assigned_to' => $it->id]);

        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/messages", [
            'body' => 'Tolong bantu @rekan',
            'mentions' => [$other->id],
        ])->assertCreated();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $other->id, 'type' => 'mention', 'ticket_id' => $ticket->id,
        ]);
    }

    public function test_non_participant_client_cannot_post_message(): void
    {
        $owner = User::factory()->client()->create();
        $stranger = User::factory()->client()->create();
        $ticket = Ticket::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($stranger)->postJson("/api/tickets/{$ticket->id}/messages", [
            'body' => 'halo',
        ])->assertStatus(403);
    }

    public function test_assign_and_resolve_notify_client(): void
    {
        $this->seed(\Database\Seeders\SlaPolicySeeder::class);
        $client = User::factory()->client()->create();
        $it = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create(['created_by' => $client->id, 'status' => 'baru', 'assigned_to' => null]);

        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/assign")->assertOk();
        $this->assertDatabaseHas('notifications', ['user_id' => $client->id, 'type' => 'ticket_accepted']);

        $this->actingAs($it)->postJson("/api/tickets/{$ticket->id}/resolve", ['outcome' => 'completed'])->assertOk();
        $this->assertDatabaseHas('notifications', ['user_id' => $client->id, 'type' => 'ticket_completed']);
    }

    public function test_user_can_read_and_count_notifications(): void
    {
        $user = User::factory()->create();
        Notification::create(['user_id' => $user->id, 'type' => 'x', 'is_read' => false]);
        $n = Notification::create(['user_id' => $user->id, 'type' => 'y', 'is_read' => false]);

        $this->actingAs($user)->getJson('/api/notifications/unread-count')
            ->assertOk()->assertJsonPath('data.count', 2);

        $this->actingAs($user)->postJson("/api/notifications/{$n->id}/read")->assertOk();

        $this->actingAs($user)->getJson('/api/notifications/unread-count')
            ->assertOk()->assertJsonPath('data.count', 1);

        $this->actingAs($user)->postJson('/api/notifications/read-all')->assertOk();
        $this->actingAs($user)->getJson('/api/notifications/unread-count')
            ->assertOk()->assertJsonPath('data.count', 0);
    }
}
