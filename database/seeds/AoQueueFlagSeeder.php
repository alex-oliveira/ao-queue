<?php

use Illuminate\Database\Seeder;

class AoQueueFlagSeeder extends Seeder
{

    public function run()
    {
        $items = [
            ['id' => \AoQueue\Constants\Flag::WAITING, 'name' => 'Waiting'],
            ['id' => \AoQueue\Constants\Flag::SELECTED, 'name' => 'Selected'],
            ['id' => \AoQueue\Constants\Flag::PROCESSING, 'name' => 'Processing'],
            ['id' => \AoQueue\Constants\Flag::FINISHED, 'name' => 'Finished'],
            ['id' => \AoQueue\Constants\Flag::ABORTED, 'name' => 'Aborted'],
        ];

        foreach ($items as $item) {
            $flag = new \AoQueue\Models\Flag();

            foreach ($item as $key => $value)
                $flag->{$key} = $value;

            $flag->save();
        }
    }

}
