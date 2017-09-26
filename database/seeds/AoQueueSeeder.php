<?php

use Illuminate\Database\Seeder;

class AoQueueSeeder extends Seeder
{

    public function run()
    {
        $this->call(AoQueueFlagSeeder::class);
        $this->call(AoQueueWorkerSeeder::class);
    }

}
