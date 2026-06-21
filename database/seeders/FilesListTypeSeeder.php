<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilesListTypeSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('files_list_type')->insert([
      [
        'id'          => 1,
        'name'        => 'Deep Full File',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
      [
        'id'          => 2,
        'name'        => 'Deep Quick File',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
      [
        'id'          => 3,
        'name'        => 'Domain Full File',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
      [
        'id'          => 4,
        'name'        => 'Domain Quick File',
        'created_at'  => now(),
        'updated_at'  => now(),
      ],
    ]);
  }
}
