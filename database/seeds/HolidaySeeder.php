<?php

use Illuminate\Database\Seeder;
use App\Holiday;

class HolidaySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Holiday::truncate();
    Holiday::create(['name' => 'Happy New Year', 'date'  => '2019-01-01' ]);
  }
}
