<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('plans')->insert([
      [
        'name' => 'Free',
        'price' => 0.00,
        'price_offer' => 0.00,
        'type' => 'lifetime',
        'indexer' => 1000,
        'bg_indexer' => 0,
        'backlinks' => 0,
        'indexer_list' => 300,
        'bg_indexer_list' => 0,
        'trial' => 0,
        'status' => 1,
      ],
      [
        'name' => 'Basic',
        'price' => 3.99,
        'price_offer' => 0.00,
        'type' => 'lifetime',
        'indexer' => 2500,
        'bg_indexer' => 0,
        'backlinks' => 0,
        'indexer_list' => 1600,
        'bg_indexer_list' => 300,
        'trial' => 0,
        'status' => 2,
      ],
      [
        'name' => 'VIP',
        'price' => 14.99,
        'price_offer' => 0.00,
        'type' => 'lifetime',
        'indexer' => 10000,
        'bg_indexer' => 0,
        'backlinks' => 100,
        'indexer_list' => 1600,
        'bg_indexer_list' => 1600,
        'trial' => 0,
        'status' => 2,
      ],
      [
        'name' => 'Not Subscribed',
        'price' => 0.00,
        'price_offer' => 0.00,
        'type' => 'monthly',
        'indexer' => 0,
        'bg_indexer' => 0,
        'backlinks' => 0,
        'indexer_list' => 0,
        'bg_indexer_list' => 0,
        'trial' => 0,
        'status' => 1,
      ],
      [
        'name' => 'Silver',
        'price' => 5.00,
        'price_offer' => 0.00,
        'type' => 'monthly',
        'indexer' => 10000,
        'bg_indexer' => 500,
        'backlinks' => 1000,
        'indexer_list' => 1600,
        'bg_indexer_list' => 1600,
        'trial' => 0,
        'status' => 1,
      ],
      [
        'name' => 'Gold',
        'price' => 15.00,
        'price_offer' => 0.00,
        'type' => 'monthly',
        'indexer' => 10000,
        'bg_indexer' => 1000,
        'backlinks' => 3000,
        'indexer_list' => 1600,
        'bg_indexer_list' => 1600,
        'trial' => 0,
        'status' => 1,
      ],
      [
        'name' => 'Platinum',
        'price' => 35.00,
        'price_offer' => 0.00,
        'type' => 'monthly',
        'indexer' => 10000,
        'bg_indexer' => 5000,
        'backlinks' => 10000,
        'indexer_list' => 1600,
        'bg_indexer_list' => 1600,
        'trial' => 0,
        'status' => 1,
      ],
      [
        'name' => 'Free Indexer App',
        'price' => 10.00,
        'price_offer' => 0.00,
        'type' => 'app',
        'indexer' => 0,
        'bg_indexer' => 0,
        'backlinks' => 0,
        'indexer_list' => 0,
        'bg_indexer_list' => 0,
        'trial' => 0,
        'status' => 1,
      ],
    ]);
  }
}
