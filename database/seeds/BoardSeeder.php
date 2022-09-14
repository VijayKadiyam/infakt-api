<?php

use App\Board;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boards = [
            [
                'name' => 'CBSE',
            ],
            [
                'name' => 'ICSE',
            ],
            [
                'name' => 'ISC',
            ],
            [
                'name' => 'IGCSE',
            ],
            [
                'name' => 'IB',
            ],
            [
                'name' => 'MYP',
            ],
        ];
        Board::truncate();
        foreach ($boards as  $Board) {

            $NewBoard = Board::where('name', '=', $Board['name'])->where('is_active', true)
                ->first();
            if ($NewBoard == '' || $NewBoard == null) {
                Board::create([
                    'name' => $Board['name'],
                ]);
            }
        }
    }
}
