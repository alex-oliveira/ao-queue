<?php

use Illuminate\Database\Seeder;

class AoQueueSeeder extends Seeder
{

    public function run()
    {
        $items = [
            [
                'name' => 'MasterWorker',
                'class' => \AoQueue\Workers\Ready\MasterWorker::class,
                'qt_min_instances' => 1,
                'qt_max_instances' => 1
            ],
//            [
//                'active' => 0,
//                'name' => 'SleepTaskFinderWorker',
//                'class' => \AoQueue\Workers\Ready\SleepTaskFinderWorker::class,
//                'lock_seconds' => 10,
//                'qt_min_instances' => 1,
//                'qt_max_instances' => 1
//            ],
//            [
//                'active' => 0,
//                'name' => 'SleeperWorker',
//                'class' => \AoQueue\Workers\Ready\SleeperWorker::class,
//                'qt_max_instances' => 10
//            ]
        ];

        foreach ($items as $item) {
            $type = new \AoQueue\Models\Type();

            foreach ($item as $key => $value)
                $type->{$key} = $value;

            $type->save();
        }
    }

}
