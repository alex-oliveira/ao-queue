<?php

namespace AoQueue\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    protected $table = 'ao_queue__logs';

    protected $fillable = [
        'task_id', 'type', 'message'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}