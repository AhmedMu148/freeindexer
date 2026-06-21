<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    User::factory()->create([
      'name'      => 'user',
      'email'     => 'user@freeindexer.com',
      'password'  => Hash::make('123456'),
      'status_id' => 1,
    ]);
    User::factory()->count(20)->create();
  }
}
