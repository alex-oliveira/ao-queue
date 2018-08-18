<?php

namespace AoQueue;

use AoQueue\Constants\Status;
use AoQueue\Models\Task;
use AoQueue\Models\Type;
use Illuminate\Support\Facades\DB;

class Tools
{

    /**
     * @return string
     */
    public function getConnectionName()
    {
        return config('ao-queue.db.connection', env('DB_CONNECTION', 'mysql'));
    }

    /**
     * @return string
     */
    public function getTypesTableName()
    {
        return config('ao-queue.db.tables.types', 'ao_queue__types');
    }

    /**
     * @return string
     */
    public function getTasksTableName()
    {
        return config('ao-queue.db.tables.tasks', 'ao_queue__tasks');
    }

    /**
     * @return string
     */
    public function getScreenNamespace()
    {
        return str_slug(config('ao-queue.screens.namespace', 'ao-queue'));
    }

    /**
     * @param $type_class string
     * @return Type
     */
    public function type($type_class)
    {
        if (substr($type_class, 0, 1) == '\\')
            $type_class = substr($type_class, 1);

        $type = Type::query()->where('class', $type_class)->get()->first();

        if (!$type) {
            $type = new Type();
            $type->name = class_basename($type_class);
            $type->class = $type_class;
            $type->save();
            $type->refresh();
        }

        return $type;
    }

    /**
     * @param $type_class string
     * @param $tasks array
     * @param $group_unique string
     * @param $start bool
     */
    public function add($type_class, array $tasks, $group_unique = null, $start = true)
    {
        $type = $this->type($type_class);

        $inserts = [];

        foreach ($tasks as $key => $value) {
            $task = [
                'type_id' => $type->id,
                'group_unique' => $group_unique,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString()
            ];

            if (is_array($value) || is_object($value)) {
                $task['reference_id'] = $key;
                $task['data'] = base64_encode(serialize($value));
            } else {
                $task['reference_id'] = $value;
            }

            $inserts[] = $task;
        }

        if (count($inserts) > 0) {
            Task::insert($inserts);
            if ($start)
                $this->start();
        }
    }

    /**
     * @param $type_id int
     * @param $worker_unique string
     * @return bool|Task
     */
    public function next($type_id, $worker_unique)
    {
        $qt = Task::query()
            ->where('type_id', $type_id)
            ->where('status', Status::WAITING)
            ->where(function ($q) {
                $q->whereNull('selectable_at')->orWhere('selectable_at', '<', \Carbon\Carbon::now());
            })
            ->orderBy('created_at')
            ->limit(1)
            ->update(['worker_unique' => $worker_unique, 'status' => Status::SELECTED]);

        if ($qt == 0) {
            return null;
        }

        return Task::query()->where('worker_unique', $worker_unique)->limit(1)->get()->first();
    }

    /**
     * @return array
     */
    public function screens()
    {
        $screens = [];

        exec("screen -list | grep '." . $this->getScreenNamespace() . ".' | grep -v grep | awk '{print $1}'", $screens);

        if (count($screens) <= 0)
            return [];

        foreach ($screens as $s => $screen) {
            $screen = explode('.', $screen);

            $obj = new \stdClass();
            $obj->pid = $screen[0];
            $obj->date = $screen[1];
            $obj->time = $screen[2];
            $obj->unique = $screen[4];
            $obj->type_id = $screen[5];
            $obj->type_class = $screen[6];

            $screens[$s] = $obj;
        }

        return $screens;
    }

    public function start()
    {
        $pid = null;

        foreach ($this->screens() as $screen) {
            if ($screen->type_id == 1) {
                $pid = $screen->pid;
                break;
            }
        }

        if (is_null($pid))
            return;

        exec('screen -r ' . $pid . ' -X stuff "yes^M"');
    }

}