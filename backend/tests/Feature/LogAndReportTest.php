<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogAndReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_writes_activity_log(): void
    {
        User::factory()->create(['email' => 'a@test.com']);

        $this->postJson('/api/auth/login', ['email' => 'a@test.com', 'password' => 'password'])->assertOk();

        $this->assertDatabaseHas('activity_logs', ['action' => 'login']);
    }

    public function test_ticket_changes_write_audit_log(): void
    {
        $it = User::factory()->itSupport()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => $it->id, 'status' => 'sedang_dikerjakan']);

        // 'created' tercatat saat factory create
        $this->assertDatabaseHas('audit_logs', ['event' => 'created', 'auditable_id' => $ticket->id]);

        $this->actingAs($it)->patchJson("/api/tickets/{$ticket->id}/progress", ['progress' => 75])->assertOk();

        $this->assertDatabaseHas('audit_logs', ['event' => 'updated', 'auditable_id' => $ticket->id]);
    }

    public function test_only_admin_can_view_logs(): void
    {
        $admin = User::factory()->admin()->create();
        $it = User::factory()->itSupport()->create();

        $this->actingAs($admin)->getJson('/api/admin/activity-logs')->assertOk();
        $this->actingAs($admin)->getJson('/api/admin/audit-logs')->assertOk();
        $this->actingAs($it)->getJson('/api/admin/audit-logs')->assertStatus(403);
    }

    public function test_report_summary_returns_metrics(): void
    {
        $this->seed(\Database\Seeders\SlaPolicySeeder::class);
        $it = User::factory()->itSupport()->create();
        Ticket::factory()->completed()->create();
        Ticket::factory()->create(['status' => 'baru']);

        $this->actingAs($it)->getJson('/api/reports/summary')
            ->assertOk()
            ->assertJsonStructure(['data' => [
                'total', 'open', 'completed', 'rejected', 'overdue',
                'avg_completion_minutes', 'sla_achievement_percent',
            ]]);
    }

    public function test_analytics_returns_expected_sections(): void
    {
        $it = User::factory()->itSupport()->create();
        Ticket::factory()->count(3)->create();

        $this->actingAs($it)->getJson('/api/reports/analytics')
            ->assertOk()
            ->assertJsonStructure(['data' => [
                'tickets_per_month', 'top_categories', 'top_divisions', 'per_technician',
            ]]);
    }

    public function test_client_cannot_access_reports(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->getJson('/api/reports/summary')->assertStatus(403);
    }

    public function test_csv_export_streams_file(): void
    {
        $it = User::factory()->itSupport()->create();
        Ticket::factory()->count(2)->create();

        $response = $this->actingAs($it)->get('/api/reports/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Nomor', $response->streamedContent());
    }
}
