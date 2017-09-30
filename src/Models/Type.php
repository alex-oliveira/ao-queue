<?php

namespace AoQueue\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{

    protected $table = 'ao_queue__types';

    protected $fillable = [
        'active', 'name', 'class', 'description', 'work_days', 'wake_up_hour', 'sleep_hour', 'relax_seconds'
    ];

    public function getWorkDaysAttribute($value)
    {
        return explode(',', $value);
    }

    public function setWorkDaysAttribute($value)
    {
        $this->attributes['work_days'] = implode(',', $value);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}