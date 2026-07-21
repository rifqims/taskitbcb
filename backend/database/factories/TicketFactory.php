<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Ticket> */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $priority = fake()->randomElement(Priority::cases());
        $createdAt = fake()->dateTimeBetween('-20 days', 'now');
        $slaDue = (clone $createdAt)->modify('+'.$priority->defaultSlaMinutes().' minutes');

        return [
            'ticket_number' => 'TKT-'.date('Ym', $createdAt->getTimestamp()).'-'.fake()->unique()->numerify('######'),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'division_id' => Division::factory(),
            'sender_name' => fake()->name(),
            'category_id' => Category::factory(),
            'priority' => $priority,
            'priority_weight' => $priority->weight(),
            'status' => TicketStatus::Baru,
            'progress' => 0,
            'location' => fake()->optional()->streetName(),
            'created_by' => User::factory()->client(),
            'assigned_to' => null,
            'deadline' => fake()->optional()->dateTimeBetween('now', '+7 days'),
            'sla_due_at' => $slaDue,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::SedangDikerjakan,
            'assigned_to' => User::factory()->itSupport(),
            'started_at' => now(),
            'progress' => fake()->randomElement([25, 50, 75]),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Selesai,
            'assigned_to' => User::factory()->itSupport(),
            'started_at' => now()->subHours(3),
            'completed_at' => now(),
            'progress' => 100,
        ]);
    }
}
