<?php

namespace AoQueue\Workers;

use AoQueue\Constants\Flag;
use AoQueue\Models\Task;
use AoQueue\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class MasterWorker extends AoQueueWorker
{

    public $repeat = true;

    public function next()
    {
        static $count = 0;

        if ($count > 0)
            $this->command->confirm('REPEAT ?', true);

        return ++$count;
    }

    public function work()
    {
        $screens = [];

        $this->log('Checking screen quantity are running...');
        foreach (AoQueue()->screens() as $screen) {
            if (!isset($screens[$screen->id]))
                $screens[$screen->id] = 0;
            $screens[$screen->id]++;
        }

        $this->log('Getting active workers...');
        $workers = Worker::query()->where('active', 1)->get();

        foreach ($workers as $worker) {
            $this->log();

            $limit = 0;

            if (isset($screens[$worker->id]))
                $limit = $screens[$worker->id];

            $limit = $worker->qt_min_instances == 0
                ? $worker->qt_max_instances - $limit
                : $worker->qt_min_instances - $limit;

            if ($limit <= 0) {
                $this->log('I can\'t create more "' . $worker->name . '" workers.');
                continue;
            }

            $this->log('I can create ' . $limit . ' "' . $worker->name . '" workers...');

            if ($worker->qt_min_instances == 0) {
                $this->log('Finding tasks to "' . $worker->name . '" worker...');

                $limit = Task::query()->where('worker_id', $worker->id)->where('flag_id', Flag::WAITING)->where(function ($q) {
                    $q->whereNull('selectable_at')->orWhere('selectable_at', '<', Carbon::now());
                })->limit($limit)->get()->count();

                if ($limit <= 0) {
                    $this->log('There are no tasks to "' . $worker->name . '" workers.');
                    continue;
                }
            }

            $this->log('Create ' . $limit . ' workers of type "' . $worker->name . '"...');
            for ($c = 1; $c <= $limit; $c++)
                Artisan::call('ao-queue:screen', [
                    'worker_class' => $worker->class
                ]);
        }
    }

}