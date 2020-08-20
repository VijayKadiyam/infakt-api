<?php

use Illuminate\Database\Seeder;
use App\LeavePattern;

class LeavePatternSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // LeavePattern::truncate();
    LeavePattern::create(['name' => 'Pattern 1']); // Different Types
    LeavePattern::create(['name' => 'Pattern 2']); // On the basis of months
  }
}
