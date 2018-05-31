<?php

namespace App\Http\Controllers;

use Auth;
use JWTAuth;
use App\User;
use App\Http\Requests\RegisterFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(RegisterFormRequest $request)
    {
        $user = new User;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->roles = $request->role;
        $user->password = bcrypt($request->password);
        $user->save();

        return response([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if ( ! $token = JWTAuth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'error' => 'invalid.credentials',
                'msg' => 'Invalid Credentials.'
            ], 400);
        }

        return response([
            'status' => 'success'
        ])
        ->header('Authorization', $token);
    }

    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);

        return response([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function getrequests (Request $request)
    {
        $records = DB::table('requests')->get();
        $users = DB::table('users')->select('username')->where('roles', 'reviewer')->get();

        return response([
            'status' => 'success',
            'requests' => $records,
            'users' => $users
        ]);
    }
    public function getobservations (Request $request)
    {
        $observations = DB::table('observations')->where('request_id', $request->Id)->get();
        return response([
            'status' => 'success',
            'observations' => $observations,
        ]);
    }

    public function getselectedrequest (Request $request)
    {
        $request = DB::table('requests')->where('Id', $request->Id)->first();
        return response([
            'status' => 'success',
            'request' => $request
        ]);
    }

    public function refresh()
    {
        return response([
            'status' => 'success'
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate();

        return response([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    public function changePass(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $credentials = array("username" => $user->username, "password" => $request->password);

        if ( ! JWTAuth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'error' => 'invalid.credentials',
                'msg' => 'Invalid Credentials.'
            ], 400);
        };

        $user->password = bcrypt($request->newPassword);
        $user->save();
        return response([
            'status' => 'success'
        ], 200);

    }

    public function passwordReset(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($user->roles == 'admin')
        {
            DB::table('users')
                ->where('username', $request->username)
                ->update(['password' => bcrypt('welcome')]);
            
            return response([
                'status' => 'success'
            ], 200);
        }
    }
}