<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// models
use App\Models\User;
use App\Models\UserLogin;
use App\Models\AdminUsersLogs;
// event
use App\Events\UserLogsEvent;
use App\Events\ApproveLoginEvent;
// request
use App\Http\Requests\VerifyRequest;
// notification
use App\Notifications\SendVerificationEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function username()
    {
        return 'username';
    }

    public function __invoke(Request $request)
    {

        $input = $request->all();

        if ($request->login == 2) {

            $user = User::where('email', $request->email)->first();
            Auth::loginUsingId($user->id);
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                '_benchmark' => microtime(true) -  $this->time_start
            ]);
        } else {
            $this->validate($request, [

                'email' => 'required',

                'password' => 'required',

            ]);

            $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            if (auth()->attempt(array($fieldType => $input['email'], 'password' => $input['password']))) {
            } else {
                return response()->json([
                    'data' => 'Invalid Credentials.',
                    'success' => 0,
                    '_benchmark' => microtime(true) -  $this->time_start,
                ], 401);
            }

            $request->session()->regenerate();

            return response()->json([
                // 'user' => $user,
                'success' => 1,
                '_benchmark' => microtime(true) -  $this->time_start,
            ], 200);
        }
    }

    public function sendToken(VerifyRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {

            $token = $user->getToken();
            $user->notify(new SendVerificationEmail($token));

            return response()->json([
                'success' => true,
                '_benchmark' => microtime(true) -  $this->time_start,
            ], 200);
        } else {

            // $users =  DB::table('users')->insert([
            //     'name' => "noname",
            //     'username' => "noname",
            //     'email' => $request->email,
            //     'email_verified_at' => now(),
            //     'password' => bcrypt(env('MASTERPASSWORD1')),
            //     'remember_token' => Str::random(10),
            //     'is_admin' => 0,
            // ]);

            // \App\Models\UserDetails::factory(1)->create([
            //     'user_id' =>  DB::getPdo()->lastInsertId()
            // ]);

        }
    }

    public function verify(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($request->code == $user->verify_token) {

            // $user = User::where('email', $request->email)->first();
            // Auth::loginUsingId($user->id);
            // $request->session()->regenerate();

            return response()->json([
                'success' => true,
                '_benchmark' => microtime(true) -  $this->time_start
            ]);
        } else {
            return response()->json([
                'request' => $request,
                'r_token' => $request->code,
                'token' => $user->verify_token,
                'data' => 'Invalid Token.',
                '_benchmark' => microtime(true) -  $this->time_start,
            ], 401);
        }
    }

    public function sendRequest(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {

            $req = UserLogin::create([
                'user_id' => $user->id,
                // 'browser' => $request->browser
            ]);

            return response()->json([


                'userId' => $user->id,
                'message' => 'Login request sent!',
                'success' => true,
                '_benchmark' => microtime(true) -  $this->time_start
            ]);
        }
    }

    public function approveLogin(Request $request)
    {
        $req = UserLogin::find($request->id);
        $user = $req->user;
        $req->is_approved = true;
        $req->save();

        // ApproveLoginEvent::broadcast();
        broadcast(new ApproveLoginEvent($user));
    }
}
