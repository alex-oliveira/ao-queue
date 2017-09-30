<?php

namespace AoQueue\Workers\Ready;

use AoQueue\Models\Type;
use AoQueue\Workers\RepeaterWorker;
use Illuminate\Support\Facades\Artisan;

class MasterWorker extends RepeaterWorker
{

    public function next()
    {
        static $count = 0;

        if ($count > 0)
            $this->command()->confirm('REPEAT ?', true);

        return ++$count;
    }

    public function work()
    {
        $screens = [];

        $this->log('Checking screen quantity are running...');
        foreach (AoQueue()->screens() as $screen) {
            if (!isset($screens[$screen->type_id]))
                $screens[$screen->type_id] = 0;
            $screens[$screen->type_id]++;
        }

        $this->log('Getting active workers...');
        $types = Type::query()->where('active', 1)->get();

        foreach ($types as $type) {
            $this->log();

            $limit = 0;

            if (isset($screens[$type->id]))
                $limit = $screens[$type->id];

            $limit = $type->qt_min_instances == 0
                ? $type->qt_max_instances - $limit
                : $type->qt_min_instances - $limit;

            if ($limit <= 0) {
                $this->log('I can\'t create more "' . $type->name . '" workers.');
                continue;
            }

            $this->log('I can create ' . $limit . ' "' . $type->name . '" workers. Let\'s go!');

            for ($c = 0; $c < $limit; $c++) {
                $params = ['worker_class' => $type->class, '--worker_unique' => uniqid()];

                if ($type->qt_min_instances == 0) {
                    $task = AoQueue()->next($type->id, $params['--worker_unique']);
                    if (!$task)
                        break;

                    $params['--task_id'] = $task->id;
                }

                Artisan::call('ao-queue:screen', $params);
            }

            $this->log($c . ' "' . $type->name . '" workers with tasks were created.');
        }
    }

}