<?php

namespace AoQueue\Models;

use Illuminate\Database\Eloquent\Model;

class Flag extends Model
{

    protected $table = 'ao_queue__flags';

    public $timestamps = false;

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}