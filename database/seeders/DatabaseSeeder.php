<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // \App\Models\User::factory(10)->create();
    if (DB::table('levels')->get()->count() == 0) {
      DB::table('levels')->insert([
        ['title' => 'Invited'],
        ['title' => 'Prospect'],
        ['title' => 'Member'],
      ]);
    }

    if (DB::table('challenges')->get()->count() == 0) {
      DB::table('challenges')->insert([
        [
          'name' => 'Challenge 1',
          'description' => 'Challenge 1',
          'repeat' => 5,
          'position' => 1,
          'points' => 100,
          'level_id' => 1,
        ],
      ]);
    }
  }
}