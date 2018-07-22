<?php

namespace AoQueue\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{

    protected $fillable = [
        'active', 'name', 'class', 'work_days', 'wake_up_hour', 'sleep_hour', 'lock_seconds', 'ignore_seconds', 'qt_min_instances', 'qt_max_instances', 'selectable_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = AoQueue()->getConnectionName();
        $this->table = AoQueue()->getTypesTableName();

        parent::__construct($attributes);
    }

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