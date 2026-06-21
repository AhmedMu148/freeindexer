<?php

namespace Database\Factories;

use App\Models\TicketMessage;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketMessageFactory extends Factory
{
  protected $model = TicketMessage::class;

  public function definition(): array
  {
    $created = $this->faker->dateTimeBetween('-30 days', 'now');

    return [
      'ticket_id'   => Ticket::factory(),
      'uid'     => User::factory(),
      'body'        => $this->faker->paragraphs(2, true),
      'attachments' => null,
      'created_at'  => $created,
      'updated_at'  => $created,
    ];
  }
}
