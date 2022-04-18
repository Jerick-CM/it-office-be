<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// models
use App\Models\User;
use App\Models\AdminUsersLogs;
// event
use App\Events\UserLogsEvent;
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
                'sucess' => true,
                '_benchmark' => microtime(true) -  $this->time_start
            ]);
        } else {
            return response()->json([
                'data' => 'Invalid Token.',
                '_benchmark' => microtime(true) -  $this->time_start,
            ], 401);
        }
    }
}
