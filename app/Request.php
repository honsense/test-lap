<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\User;

class Request extends Model
{
    public function observations()
    {
        return $this->hasMany('App\Observation');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
