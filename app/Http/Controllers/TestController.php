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
    public function test(Request $request){
        $user = User::find(Auth::user()->id);
        if ($request->mode == 'insert'){
            DB::table('requests')->insert([
                ['reference' => $request->reference, 'requester' => $user->username, 'comments' => $request->comments, 'method' => $request->method]
            ]);
        }
        elseif ($request->mode == 'update'){
            DB::table('requests')
                ->where('Id', $request->Id)
                ->update(['reference' => $request->reference, 'comments' => $request->comments, 'method' => $request->method]);
        }
        // $postdata = $request;
        return response($request);
    }

    public function approveObs(Request $request)
    {
        DB::table('observations')
        ->wherein('Id', $request)
        ->update(['approved' => 1]);
        return $request;
    }
}