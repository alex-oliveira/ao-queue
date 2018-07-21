<?php

namespace AoQueue\Models;

use function GuzzleHttp\Psr7\str;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $fillable = [
        'status', 'type_id', 'worker_unique', 'group_unique', 'reference_id', 'data', 'selectable_at'
    ];

    public function __construct(array $attributes = [])
    {
        $this->connection = AoQueue()->getConnectionName();
        $this->table = AoQueue()->getTasksTableName();

        parent::__construct($attributes);
    }

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

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

}