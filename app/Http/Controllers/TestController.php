<?php

namespace App\Http\Controllers;

use Auth;
use JWTAuth;
use App\User;
use App\Http\Requests\RegisterFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    // public function test(Request $request){
    //     $user = User::find(Auth::user()->id);
    //     if ($request->mode == 'insert'){
    //         DB::table('requests')->insert([
    //             ['reference' => $request->REFERENCE, 'requester' => $user->username, 'comments' => $request->COMMENTS, 'method' => $request->METHOD, 
    //             'sample_type' => $request->SAMPLE_TYPE, 'prnumber' => $request->PRNUMBER, 'assigned_reviewer' => $request->ASSIGNED_REVIEWER, 'updated_by' => $user->username,
    //              'date_due' => $request->DATE_DUE, 'status' => $request->STATUS]
    //         ]);
    //     }
    //     elseif ($request->mode == 'update'){
    //         $sample_type = JSON_ENCODE($request->SAMPLE_TYPE);
    //         DB::table('requests')
    //             ->where('Id', $request->Id)
    //             ->update(['reference' => $request->REFERENCE, 'comments' => $request->COMMENTS, 'method' => $request->METHOD, 'sample_type' => $request->SAMPLE_TYPE,
    //             'prnumber' => $request->PRNUMBER, 'assigned_reviewer' => $request->ASSIGNED_REVIEWER, 'updated_by' => $user->username, 'date_due' => $request->DATE_DUE,
    //             'status' => $request->STATUS]);
    //     }
        // $postdata = $request;
        // return response($request);
    // }
    public function postObs(Request $request){
        $user = User::find(Auth::user()->id);
        // $rid = DB::TABLE('requests')
        // ->select('Id')
        // ->where('reference', $request->REFERENCE);
        if ($request->mode == 'insert'){
            DB::table('observations')->insert([
                ['request_id' => $request->REQUEST_ID, 'reference' => $request->REFERENCE, 'observation' => $request->OBSERVATION,
                'actions' => $request->ACTIONS, 'created_by' => $user->username, 'updated_by' => $user->username, 'criticality' => $request->CRITICALITY]
            ]);
        }
        elseif ($request->mode == 'update'){
            DB::table('observations')
                ->where('Id', $request->Id)
                ->update(['observation' => $request->OBSERVATION, 'actions' => $request->ACTIONS, 'response' => $request->RESPONSE, 'updated_by' => $user->username,
                'criticality' => $request->CRITICALITY]);
        }
        return response($request);
    }

    public function approveObs(Request $request)
    {
        $user = User::find(Auth::user()->id);
        DB::table('observations')
        ->wherein('Id', $request->Id)
        ->update(['approved' => 1, 'updated_by' => $user->username, 'approved_by' => $user->username]);
        return response($request);
    }

    // public function approveRequest(Request $request)
    // {
    //     $user = User::find(Auth::user()->id);

    //     if(DB::table('requests')->select('APPROVED')->where('Id', $request->Id))
    //     {
    //         DB::table('requests')
    //         ->where('Id', $request->Id)
    //         ->update(['approved' => 1, 'updated_by' => $user->username, 'approved_by' => $user->username]);
    //         return response($request);
    //     }
    // }
}