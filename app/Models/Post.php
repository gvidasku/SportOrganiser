<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisator_id', 'sportevent_title', 'sport_category',
        'attendance', 'event_type',
        'sportevent_location', 'price', 'date',
        'level', 'age',
        'time', 'description'
    ];

    //user post piviot for savedsportevents
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function organisator()
    {
        return $this->belongsTo('App\Models\organisator');
    }

    public function dateTimestamp()
    {
        return Carbon::parse($this->date)->timestamp;
    }

    public function remainingDays()
    {
        $date = $this->date;
        $timestamp = Carbon::parse($date)->timestamp - Carbon::now()->timestamp;
        return $timestamp;
    }

    public function gettime()
    {
        return explode(',', $this->time);
    }
}
