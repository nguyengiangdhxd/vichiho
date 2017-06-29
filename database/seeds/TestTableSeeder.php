<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 50; $i++){
            DB::table('test')->insert([
                'username' => str_random(10),
                'age' => random_int(10, 100),
                'address' => str_random(10),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

    }
}
