<?php

use Illuminate\Database\Seeder;

class AoQueueTypeSeeder extends Seeder
{

    public function run()
    {
        $items = [[
            'name' => 'MasterWorker',
            'class' => \AoQueue\Workers\Ready\MasterWorker::class,
            'description' => 'It is main worker. It is responsible by create new workers when are necessary.',
            'qt_min_instances' => 1,
            'qt_max_instances' => 1
        ], [
            'active' => 0,
            'name' => 'SleepTaskFinderWorker',
            'class' => \AoQueue\Workers\Ready\SleepTaskFinderWorker::class,
            'description' => 'This is a fake worker, used only to tests. It create fake tasks to other fake worker "SleeperWorker".',
            'relax_seconds' => 10,
            'qt_min_instances' => 1,
            'qt_max_instances' => 1
        ], [
            'active' => 0,
            'name' => 'SleeperWorker',
            'class' => \AoQueue\Workers\Ready\SleeperWorker::class,
            'description' => 'This is a fake worker, used only to tests. It resolve the fake tasks created by "SleepTaskFinderWorker".',
            'qt_max_instances' => 10
        ]];

        foreach ($items as $item) {
            $flag = new \AoQueue\Models\Type();

            foreach ($item as $key => $value)
                $flag->{$key} = $value;

            $flag->save();
        }
    }

}
