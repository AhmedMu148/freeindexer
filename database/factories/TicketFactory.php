<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class TicketFactory extends Factory
{
  protected $model = Ticket::class;

  public function definition(): array
  {
    $status   = Arr::random(['open', 'pending', 'closed']);
    $priority = Arr::random(['low', 'normal', 'high']);
    $created  = $this->faker->dateTimeBetween('-60 days', '-1 day');

    return [
      'uid'         => User::factory(),
      'subject'     => $this->faker->sentence(6),
      'status'      => $status,
      'priority'    => $priority,
      'created_at'  => $created,
      'updated_at'  => $this->faker->dateTimeBetween($created, 'now'),
    ];
  }
}
