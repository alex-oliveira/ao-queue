<?php

namespace AoQueue\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{

    protected $table = 'ao_queue__workers';

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}