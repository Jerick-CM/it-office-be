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

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {

        // if (User::where('email', $request->email)->first()) {
        //     return response()->json([
        //         'data' => 'E-mail does not exist.',
        //         '_benchmark' => microtime(true) -  $this->time_start,
        //     ], 401);
        // }


        if (User::where('email', $request->email)->first()) {
            // return response()->json([
            //     'data' => 'E-mail does not exist.',
            //     '_benchmark' => microtime(true) -  $this->time_start,
            // ], 401);
        } else if (User::where('name', $request->email)->first()) {
        } else {
            return response()->json([
                'data' => 'E-mail does not exist.',
                '_benchmark' => microtime(true) -  $this->time_start,
            ], 401);
        }

        // if (User::where('is_active', 0)->where('email', $request->email)->first()) {
        //     return response()->json([
        //         'data' => 'Account is inactive.',
        //         '_benchmark' => microtime(true) -  $this->time_start,
        //     ], 401);
        // }

        if (auth()->attempt($request->only('email', 'password'))) {


        }else if(auth()->attempt($request->only('name', 'password'))){


        } else{

            return response()->json([
                'data' => 'Invalid Credentials.',
                '_benchmark' => microtime(true) -  $this->time_start,
            ], 401);

        }


        // $user = User::where('is_active', 1)->where('email', $request->email)->first();

        // event(new UserLogsEvent($user->id, AdminUsersLogs::TYPE_USERS_LOGIN, [
        //     'admin'  =>   $user->name,
        //     'admin_id'  =>  $user->id,
        //     'user_id'  =>  $user->id,
        //     'user_name'  =>  $user->name
        // ]));


        // try {
        //     // Validate the value...
        // } catch (Throwable $e) {
        //     report($e);

        //     return false;
        // }

        $request->session()->regenerate();

        return response()->json([
            // 'user' => $user,
            'success' => true,
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

        if ($request->token == $user->verify_token)
        {
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
