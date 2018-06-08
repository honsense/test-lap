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

    public function upDates($request)
    {
        // if ($this->approved == 0 && $request->approved == 1)
        // {
        //     $this->approved_on = now();
        //     $this->approved_by = $request->user()->username;
        // }

        if (!$this->responded_on && !!$request->response)
        {
            $this->responded_on = now();
        }

        return $this;
    }
}
