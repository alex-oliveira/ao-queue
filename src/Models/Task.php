<?php

namespace AoQueue\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $table = 'ao_queue__tasks';

    protected $fillable = [
        'flag_id', 'worker_id', 'unique', 'group_unique', 'reference_id', 'data', 'selectable_at'
    ];

    public function flag()
    {
        return $this->belongsTo(Flag::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

}