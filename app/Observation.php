<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    public function request()
    {
        return $this->belongsTo('App\Request');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
