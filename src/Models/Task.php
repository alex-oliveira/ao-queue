<?php

namespace AoQueue\Models;

use function GuzzleHttp\Psr7\str;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $table = 'ao_queue__tasks';

    protected $fillable = [
        'flag_id', 'type_id', 'worker_unique', 'group_unique', 'reference_id', 'data', 'selectable_at'
    ];

    public function getDataAttribute($value)
    {
        if (is_null($value))
            return null;
        return unserialize(base64_decode($value));
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = base64_encode(serialize($value));
    }

    public function flag()
    {
        return $this->belongsTo(Flag::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

}