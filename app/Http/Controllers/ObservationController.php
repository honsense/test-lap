<?php

namespace App\Http\Controllers;

use App\Observation;
use Illuminate\Http\Request;
use App\Request as Requests;

class ObservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(['observations'=>Observation::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $obs = new Observation;
        $obs->user_id = $request->user()->id;
        $obs->request_id = $request->request_id;
        $obs->reference = $request->reference;
        $obs->observation = $request->observation;
        $obs->actions = $request->actions;
        $obs->criticality = $request->criticality;
        $obs->created_by = $request->user()->username;
        $obs->updated_by = $request->user()->username;
        $obs->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Observation  $observation
     * @return \Illuminate\Http\Response
     */
    public function show(Observation $id)
    {
        return response(['observation'=>$id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Observation  $observation
     * @return \Illuminate\Http\Response
     */
    public function edit(Observation $observation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Observation  $observation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Observation $id)
    {
        $id->upDates($request);
        $id->observation = $request->observation;
        $id->actions = $request->actions;
        $id->response = $request->response;
        $id->criticality = $request->criticality;
        $id->updated_by = $request->user()->username;
        $id->save();
    }

    public function approve(Request $id)
    {
        if($id->user()->roles == 'reviewer'){
            Observation::wherein('id', $id->id)
            ->update(
                ['approved'=>1,
                 'approved_on'=>now(),
                 'approved_by'=>$id->user()->username,
                 'updated_by'=>$id->user()->username
                ]);

            return response('success');
        }
    }
}
