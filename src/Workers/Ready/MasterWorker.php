<?php

namespace AoQueue\Workers\Ready;

use AoQueue\Models\Type;
use AoQueue\Workers\RepeaterWorker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MasterWorker extends RepeaterWorker
{

    protected $last_work = 0;

    public function next()
    {
        if (!$this->repeat())
            return false;

        static $count = 0;

        do {
            if ($count > 0)
                $this->command()->confirm('REPEAT ' . $count . '?', true);

        } while ((time() - $this->last_work) < config('ao-queue.master.seconds_between_requests', 5));

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
        $types = Type::query()
            ->where('active', 1)
            ->where(function($q){
                $q->whereNull('selectable_at')->orWhere('selectable_at', '<=', Carbon::now());
            })
            ->get();

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

        $params = $this->params();
        if (isset($params[0]) && $params[0] == true) {
            $this->repeat(false);
        }

        $this->last_work = time();
    }

}