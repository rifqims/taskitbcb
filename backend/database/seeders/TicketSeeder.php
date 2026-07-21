<?php

namespace Database\Seeders;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Division;
use App\Models\SlaPolicy;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $rifqi = User::where('email', 'rifqi@gawe-qi.test')->first();
        $agus = User::where('email', 'agus@gawe-qi.test')->first();
        $bagus = User::where('email', 'bagus@gawe-qi.test')->first();
        $dewi = User::where('email', 'dewi@gawe-qi.test')->first();

        $cat = fn (string $n) => Category::where('name', $n)->value('id');
        $div = fn (string $n) => Division::where('name', $n)->value('id');

        $samples = [
            ['VPN error saat kerja dari rumah', 'Network', 'Sales', Priority::Urgent, TicketStatus::SedangDikerjakan, 50, $bagus, $rifqi],
            ['Printer lantai 3 tidak bisa cetak', 'Printer', 'Finance', Priority::High, TicketStatus::SedangDikerjakan, 75, $dewi, $rifqi],
            ['Email tidak bisa kirim lampiran besar', 'Email', 'HRD', Priority::Medium, TicketStatus::Baru, 0, $dewi, null],
            ['CCTV gudang belakang mati total', 'CCTV', 'Operasional', Priority::Urgent, TicketStatus::Pending, 50, $bagus, $agus],
            ['Reset password aplikasi absensi', 'Aplikasi', 'Produksi', Priority::Medium, TicketStatus::Selesai, 100, $bagus, $rifqi],
            ['Install Adobe Acrobat di PC baru', 'Software', 'Marketing', Priority::Low, TicketStatus::Baru, 0, $dewi, null],
        ];

        $seq = 1;
        foreach ($samples as [$title, $category, $division, $priority, $status, $progress, $creator, $assignee]) {
            $createdAt = Carbon::now()->subDays(rand(0, 6))->subHours(rand(0, 12));
            $slaMinutes = SlaPolicy::where('priority', $priority->value)->value('resolution_minutes')
                ?? $priority->defaultSlaMinutes();

            $ticket = Ticket::create([
                'ticket_number' => 'TKT-'.$createdAt->format('Ym').'-'.str_pad((string) $seq++, 6, '0', STR_PAD_LEFT),
                'title' => $title,
                'description' => 'Detail masalah: '.$title.'. Mohon bantuannya tim IT.',
                'division_id' => $div($division),
                'sender_name' => $creator->name,
                'category_id' => $cat($category),
                'priority' => $priority,
                'priority_weight' => $priority->weight(),
                'status' => $status,
                'progress' => $progress,
                'created_by' => $creator->id,
                'assigned_to' => $assignee?->id,
                'sla_due_at' => (clone $createdAt)->addMinutes($slaMinutes),
                'started_at' => $assignee ? (clone $createdAt)->addHours(1) : null,
                'completed_at' => $status === TicketStatus::Selesai ? (clone $createdAt)->addHours(2) : null,
                'created_at' => $createdAt,
                'updated_at' => now(),
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $creator->id,
                'action' => 'created',
                'meta' => ['priority' => $priority->value],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if ($assignee) {
                TicketActivity::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $assignee->id,
                    'action' => 'assigned',
                    'meta' => ['assignee' => $assignee->name],
                    'created_at' => (clone $createdAt)->addHours(1),
                    'updated_at' => (clone $createdAt)->addHours(1),
                ]);

                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $creator->id,
                    'body' => 'Halo tim IT, mohon dibantu ya untuk masalah ini. Terima kasih.',
                    'created_at' => (clone $createdAt)->addMinutes(5),
                    'updated_at' => (clone $createdAt)->addMinutes(5),
                ]);
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $assignee->id,
                    'body' => 'Siap, saya ambil tiketnya dan cek sekarang.',
                    'created_at' => (clone $createdAt)->addHours(1),
                    'updated_at' => (clone $createdAt)->addHours(1),
                ]);
            }
        }
    }
}
