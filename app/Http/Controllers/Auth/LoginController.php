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


class LoginController extends Controller
{

    public function username()
    {
        return 'username';
    }

    public function __invoke(Request $request)
    {

        $input = $request->all();

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
        }
    }

    public function verify(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($request->token == $user->verify_token) {
            return response()->json([
                'success' => true,
                '_benchmark' => microtime(true) -  $this->time_start
            ]);
        } else {
            return response()->json([
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
                'user_id' => $user->id
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
        $req->is_approved = true;
        $req->save();

        // ApproveLoginEvent::broadcast(); 
        broadcast(new ApproveLoginEvent());       
    }
}
