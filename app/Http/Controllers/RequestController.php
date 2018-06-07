<?php

namespace App\Http\Controllers;

use App\Request as Requests;
use App\User;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'requests'=>Requests::all(), 
            'users'=>User::select('username')->where('roles', 'reviewer')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $req = new Requests;
        $req->user_id = $request->user()->id;
        $req->reference = $request->reference;
        $req->location = $request->location;
        $req->comments = $request->comments;
        $req->method = $request->method;
        $req->sample_type = $request->sample_type;
        $req->due_on = $request->due_on;
        $req->assigned_reviewer = $request->assigned_reviewer;
        $req->requester = $request->user()->username;
        $req->status = 'Submitted';
        $req->prnumber = $request->prnumber;
        $req->updated_by = $request->user()->username;
        $req->upDates()->save();

        return $req;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Requests $id)
    {
        return response(['request'=>$id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Requests $requests)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Requests $req)
    {
        $req = Requests::find($request->id);
        $req->upDates($request);
        $req->reference = $request->reference;
        $req->location = $request->location;
        $req->comments = $request->comments;
        $req->method = $request->method;
        $req->sample_type = $request->sample_type;
        $req->due_on = $request->due_on;
        $req->assigned_reviewer = $request->assigned_reviewer;
        $req->status = $request->status;
        $req->prnumber = $request->prnumber;
        $req->updated_by = $request->user()->username;
        $req->save();

        return $req;
    }

    public function observations(Requests $id)
    {
        return response(['observations'=>$id->observations]);
    }
}
