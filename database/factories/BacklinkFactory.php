<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BacklinkFactory extends Factory
{
  public function definition(): array
  {
    return [
      'uid'           => (int) rand(1, 10),
      'ses_id'        => $this->faker->uuid(),
      'project_name'  => $this->faker->words(3, true),
      'links'         => $this->faker->url(),
      'keywords'      => implode(',', $this->faker->words(5)),
      'qty'           => $this->faker->numberBetween(1, 100),
      'status_id'     => $this->faker->randomElement(['1', '2', '3']),
    ];
  }
}
