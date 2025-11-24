<?php

namespace App\Domains\Work\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityThread extends Model
{
    protected $fillable = [
        'work_instance_step_id',
        'title',
        'description',
    ];

    public function workInstanceStep()
    {
        return $this->belongsTo(WorkInstanceStep::class);
    }

    public function messages()
    {
        return $this->hasMany(ActivityMessage::class);
    }
}
