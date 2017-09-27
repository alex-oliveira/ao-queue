<?php

namespace AoQueue;

use AoQueue\Constants\Flag;
use AoQueue\Models\Task;
use AoQueue\Models\Worker;
use Illuminate\Support\Facades\DB;

class Tools
{

    /**
     * @param $worker_class string
     * @return Worker
     */
    public function worker($worker_class)
    {
        if (substr($worker_class, 0, 1) == '\\')
            $worker_class = substr($worker_class, 1);

        $worker = Worker::query()->where('class', $worker_class)->get()->first();

        if (!$worker) {
            $worker = new Worker();
            $worker->name = class_basename($worker_class);
            $worker->class = $worker_class;
            $worker->save();
            $worker->refresh();
        }

        return $worker;
    }

    /**
     * @param $worker_class string
     * @param $tasks array
     */
    public function add($worker_class, array $tasks, $group_unique = null)
    {
        $worker = $this->worker($worker_class);

        $data = [];

        foreach ($tasks as $key => $value) {
            $task = ['worker_id' => $worker->id, 'group_unique' => $group_unique];

            if (is_array($value) || is_object($value)) {
                $task['reference_id'] = $key;
                $task['data'] = base64_encode(serialize($value));
            } else {
                $task['reference_id'] = $value;
            }

            $data[] = $task;
        }

        if (count($data) > 0) {
            Task::insert($data);
            $this->start();
        }
    }

    /**
     * @param $worker string
     * @return bool|Task
     */
    public function next($worker_type, $unique)
    {
        $query = Task::query()
            ->where('worker_id', $worker_type)
            ->where('flag_id', Flag::WAITING)
            ->where(function ($q) {
                $q->whereNull('selectable_at')->orWhere('selectable_at', '<', \Carbon\Carbon::now());
            })
            ->orderBy('created_at');

        if (($driver = DB::connection()->getDriverName()) == 'sqlite') {
            $query->whereRaw('ROWID = 1');
        } else {
            $query->limit(1);
        }

        try {
            $query->update(['unique' => $unique, 'flag_id' => Flag::SELECTED]);
        } catch (\Exception $e) {
            return null;
        }

        return Task::query()->where('unique', $unique)->limit(1)->get()->first();
    }

    /**
     * @return array
     */
    public function screens()
    {
        $screens = [];

        exec("screen -list | grep 'ao-queue.' | grep -v grep | awk '{print $1}'", $screens);

        if (count($screens) <= 0)
            return [];

        foreach ($screens as $s => $screen) {
            $screen = explode('.', $screen);

            $obj = new \stdClass();
            $obj->pid = $screen[0];
            $obj->date = $screen[2];
            $obj->time = $screen[3];
            $obj->unique = $screen[4];
            $obj->id = $screen[5];
            $obj->type = $screen[6];

            $screens[$s] = $obj;
        }

        return $screens;
    }

    public function start()
    {
        $pid = null;

        foreach ($this->screens() as $screen) {
            if ($screen->id == 1) {
                $pid = $screen->pid;
                break;
            }
        }

        if (is_null($pid))
            return;

        exec('screen -r ' . $pid . ' -X stuff "yes^M"');
    }

}