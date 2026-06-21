<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {

    $this->call([
      StatusSeeder::class,
      UserSeeder::class,
      PlansSeeder::class,
      PageMetaSeeder::class,
      OrderStatusSeeder::class,
      AppSeeder::class,
      BacklinkSeeder::class,
      PymProductsTableSeeder::class,
      PymStatusTableSeeder::class,
      PymGatewayTypeTableSeeder::class,
      PymGatewaysTableSeeder::class,
      PymSubscriptionsTableSeeder::class,
      PymPaymentsTableSeeder::class,
      TicketSeeder::class,
      FilesListTypeSeeder::class,
    ]);

    // need to run this command to create seeder
    // need to run db seeder command to seed the database
    // php artisan db:seed
    // User::factory(10)->create();

    // User::factory()->create([
    //     'name' => 'Test User',
    //     'email' => 'test@example.com',
    // ]);
  }
}
