<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\Socials;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\UserLogsEvent;
class RegisterController extends Controller
{
    public function create(Request $request)
    {

        if ($request->email) {

            $user = User::where('email', $request->email)->first();

            if (!$user) {

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);

                UserDetails::create([
                    'user_id' => $user->id,
                    'profile_picture' => $request->image
                ]);

                Socials::create([
                    'user_id' => $user->id,
                    'name' => $request->social,
                    'social_id' => $request->id
                ]);
            }

            $user = User::where('email', $request->email)->first();

            $user_details = UserDetails::where('user_id', $user->id)->first();

            if (!$user_details->profile_picture) {
                $user_details->profile_picture = $request->image;
                $user_details->save();
            }

            if (!Socials::where('name', $request->social)->first()) {
                Socials::create([
                    'user_id' => $user->id,
                    'name' => $request->social,
                    'social_id' => $request->id
                ]);
            }

            Auth::login($user);
            $request->session()->regenerate();
        }

        return response()->json([
            'user' => $request->user(),
            'success' => true,
            'data' => $user,
            '_benchmark' => microtime(true) -  $this->time_start,
        ], 200);
    }

    public function register(Request $request)
    {

        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6',]
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $details = UserDetails::create([
            ['user_id' => $user->id]
        ]);

        $details = RoleUser::create([
            [
                'user_id' => $user->id,
                'role_id' => 4, //4 is member
            ]
        ]);


        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'data' => $user,
            '_benchmark' => microtime(true) -  $this->time_start,
        ], 200);
    }



}
