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

    public function upDates($request = null)
    {
        if($request)
        {
            if($request->status == 'In Progress' && !$this->started_on)
            {
                $this->started_on = now();
            }
            
            if($this->status != 'Submitted' && $request->status == 'Submitted')
            {
                $this->submitted_on = now();
            }
            
            if($this->status != 'Approved' && $request->status == 'Approved')
            {
                $this->approved = 1;
                $this->approved_on = now();
                $this->approved_by = $request->user()->username;
            }
            
            if($this->status == 'Approved' && $request->status != 'Approved')
            {
                $this->approved = 0;
            }

            if(!$this->to_analyst_on && $request->status == 'Waiting on Someone')
            {
                $this->to_analyst_on = now();
            }
            
            if(!$this->assigned_reviewer && !!$request->assigned_reviewer)
            {
                $this->assigned_on = now();
            }
        }
        else
        {
            if(!!$this->assigned_reviewer && !$this->assigned_on)
            {
                $this->assigned_on = now();
            }            
        }
        return $this;
    }
}
