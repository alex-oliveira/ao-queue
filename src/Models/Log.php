<?php

namespace AoQueue\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    protected $table = 'ao_queue__logs';

    protected $fillable = [
        'type_id', 'task_id', 'message'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}