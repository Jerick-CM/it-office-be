<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// models
use App\Models\User;
use App\Models\UserLogin;
use App\Models\UserDetails;
use App\Models\AdminUsersLogs;
// requests
use App\Http\Requests\UserStoreRequest;
// event
use App\Events\UserLogsEvent;
use App\Events\ApproveLoginEvent;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $user = User::create(array_merge($request->except('first_name', 'last_name', 'password'), [
            'name' => $request->first_name . ' ' . $request->last_name,
            'password' => bcrypt($request->password)
        ]));

        if ($request->is_admin) {
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => 1,
                'created_at' =>  now(),
                'updated_at' =>  now(),
            ]);
        }

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = User::findOrFail($request->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->all());

        return response()->json([
            'message' => 'User successfully updated!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function datatable(Request $request)
    {

        if ($request->page == 1) {
            $skip = 0;
        } else {
            $skip = $request->page * $request->page;
        }

        $table = 'users';

        if ($request->sortBy == ""  && $request->sortDesc == "") {

            $page = $request->has('page') ? $request->get('page') : 1;

            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*', 'roles.name AS role_name', 'role_user.role_id AS role_id')
                ->where([['users.name', 'LIKE', "%" . $request->search . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->search . "%"]])
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->take($request->itemsPerPage)
                ->get();

            $Data_count = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->where([['users.name', 'LIKE', "%" . $request->search . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->search . "%"]])
                ->get();
        } else {

            if ($request->sortDesc) {
                $order = 'desc';
            } else {
                $order = 'asc';
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*', 'roles.name AS role_name', 'role_user.role_id AS role_id')
                ->where([['users.name', 'LIKE', "%" . $request->search . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->search . "%"]])
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->take($request->itemsPerPage)
                ->get();

            $Data_count = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->where([['users.name', 'LIKE', "%" . $request->search . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->search . "%"]])
                ->get();
        }

        $DataCs =   $Data->count();
        $DataCount =  $Data_count->count();

        foreach ($Data as $key => $value) {
            $Data[$key]['created'] = Carbon::parse($value['created_at'])->isoFormat('MMM Do YYYY - HH:mm');
            $Data[$key]['updated'] = Carbon::parse($value['updated_at'])->isoFormat('MMM Do YYYY - HH:mm');
        }

        if ($DataCs > 0 && $DataCount == 0) {
            $DataCount =   $DataCs;
        }

        return response()->json([
            'data' => $Data,
            'total' =>  $DataCount,
            // 'total' =>  14,
            'skip' => $skip,
            'take' => $request->itemsPerPage
        ], 200);
    }



    public function delete(Request $request, $table_id)
    {
        $table = User::findOrFail($table_id);
        $table->delete();

        event(new UserLogsEvent($request->id, AdminUsersLogs::TYPE_USERS_DELETUSER, [
            'user_id'  =>  $request->id,
            'user_name'  =>  $request->user()->name,
            'remove_id'  =>    $table->id,
            'remove_name'  =>  $table->name
        ]));

        return response()->json([
            'success' => 1,
            'user' => $request->user(),
            '_benchmark' => microtime(true) -  $this->time_start,
        ], 200);
    }

    public function resetpassword(Request $request, $table_id)
    {

        $user = User::where('id', $table_id)->first();
        $user->password = bcrypt($request->newpassword);
        $user->save();

        event(new UserLogsEvent($request->id, AdminUsersLogs::TYPE_USERS_RESETPASSWORD, [
            'user_id'  =>  $request->id,
            'user_name'  =>  $request->user()->name,
            'change_id'  =>    $user->id,
            'change_name'  =>  $user->name
        ]));

        return response()->json([
            'success' => 1,
            'user_id' =>  $table_id,
            'request' => $request,
            'request_np' => $request->newpassword,
            '_benchmark' => microtime(true) -  $this->time_start,
        ], 200);
    }

    public function changestatus(Request $request, $table_id)
    {

        $user = User::where('id', $table_id)->first();
        $user->is_active = ($request->selectedstatus == 'Active') ? 1 : 0;
        $user->save();


        event(new UserLogsEvent($request->id, AdminUsersLogs::TYPE_USERS_CHANGESTATUS, [
            'user_id'  =>  $request->id,
            'user_name'  =>  $request->user()->name,
            'change_id'  =>    $user->id,
            'change_name'  =>  $user->name,
            'status' => ($request->selectedstatus == 'Active') ? 'Active' : 'Inactive'
        ]));

        return response()->json([
            'success' => 1,
            'status' => $request->selectedstatus,
            'request' => $request,
            '_benchmark' => microtime(true) -  $this->time_start,
        ], 200);
    }

    public function user_datatable(Request $request)
    {

        if ($request->page == 1) {
            $skip = 0;
        } else {
            $skip = $request->page * $request->page;
        }

        $table = 'users';

        if ($request->sortBy == ""  && $request->sortDesc == "") {

            $page = $request->has('page') ? $request->get('page') : 1;

            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*', 'roles.name AS role_name', 'role_user.role_id AS role_id')
                ->where('users.id', $request->user()->id)
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->take($request->itemsPerPage)
                ->get();

            $Data_count = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->where('users.id', $request->user()->id)

                ->get();
        } else {

            if ($request->sortDesc) {
                $order = 'desc';
            } else {
                $order = 'asc';
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*', 'roles.name AS role_name', 'role_user.role_id AS role_id')
                ->where('users.id', $request->user()->id)
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->take($request->itemsPerPage)
                ->get();

            $Data_count = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->where('users.id', $request->user()->id)
                ->get();
        }

        $DataCs =   $Data->count();
        $DataCount =  $Data_count->count();

        foreach ($Data as $key => $value) {
            $Data[$key]['created'] = Carbon::parse($value['created_at'])->isoFormat('MMM Do YYYY - HH:mm');
            $Data[$key]['updated'] = Carbon::parse($value['updated_at'])->isoFormat('MMM Do YYYY - HH:mm');
        }

        if ($DataCs > 0 && $DataCount == 0) {
            $DataCount =   $DataCs;
        }

        return response()->json([
            'data' => $Data,
            'total' =>  $DataCount,
            'skip' => $skip,
            'take' => $request->itemsPerPage
        ], 200);
    }

    public function changepassword(Request $request, $table_id)
    {

        $user = DB::table('users')->where('id', $table_id)->get();
        if (Hash::check($request->password, $user[0]->password)) {
            // The passwords match...
            $hash = 'a';
            $user = User::where('id', $table_id)->first();
            $user->password = bcrypt($request->newpassword);
            $user->save();
            $success = 1;
            $data['msg'] = 'Password changes successfully.';
        } else {
            $hash = 'b';
            $data['msg'] = 'Invalid password.';
            $success = 0;
        }


        event(new UserLogsEvent($request->id, AdminUsersLogs::TYPE_USERS_CHANGEPASSWORD, [
            'admin'  =>     $request->user()->name,
            'admin_id'  =>  $request->id,
            'user_id'  =>  $request->id,
            'user_name'  =>  $request->user()->name
        ]));



        return response()->json([
            'hash' =>  $hash,
            'user_id' => $table_id,
            // 'pass_old' => $user[0]->password,
            // 'pass_new' => bcrypt($request->password),
            'success' =>    $success,
            'data' => $data,
            'user' => $request->user(),
            '_benchmark' => microtime(true) -  $this->time_start,
        ], 200);
    }

    public function update_name(Request $request, $table_id)
    {

        $user = User::findOrFail($table_id);
        $current_name = $user->name;
        $user->name = $request->user_name;

        $user->save();

        event(new UserLogsEvent($request->id, AdminUsersLogs::TYPE_USERS_CHANGEUSERNAME, [
            'user_id'  =>  $request->id,
            'user_name'  =>  $request->user()->name,
            'change_id'  =>    $user->id,
            'change_name'  =>  $request->user_name,
            'from_name'  =>  $current_name,
            'to_name'  => $request->user_name
        ]));

        return response()->json([
            'success' => 1,
            'user' => $request->user(),
            '_benchmark' => microtime(true) -  $this->time_start
        ], 200);
    }

    // public function update_role(Request $request, $table_id)
    // {
    //     $user = User::findOrFail($table_id);
    //     $user->name = $request->user_name;
    //     $user->save();

    //     return response()->json([
    //         'success' => 1,
    //         'user' => $request->user(),
    //         '_benchmark' => microtime(true) -  $this->time_start,
    //     ], 200);
    // }

    public function register_admin(Request $request)
    {

        // $time_start = microtime(true);
        // $time_end = microtime(true);
        // $timeend = $time_end - $time_start;

        // return response()->json([
        //     'test' => 'hello world',
        //     'success' => true,
        //     '_elapsed_time' => $timeend,
        // ], 200);


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

        UserDetails::create([
            ['user_id' => $user->id]
        ]);

        $now = Carbon::now();
        DB::table('role_user')->insert([
            'user_id' => $user->id,
            'role_id' => 4, //member
            'created_at' =>  $now,
            'updated_at' =>  $now,
        ]);


        event(new UserLogsEvent($request->user()->id, AdminUsersLogs::TYPE_USERS_CREATEUSERFROMADMIN, [
            'user_id'  =>  $request->user()->id,
            'user_name'  =>  $request->user()->name,
            'create_name'  =>    $request->name,
            'create_email'  =>  $request->email,
        ]));

        return response()->json([
            'success' => true,
            'data' => $user,
            '_benchmark' => microtime(true) -  $this->time_start,
        ], 200);
    }

    public function userlogin_datatable(Request $request)
    {

        if ($request->page == 1) {
            $skip = 0;
        } else {
            $skip = $request->page * $request->page;
        }

        $table = 'users';

        if ($request->sortBy == ""  && $request->sortDesc == "") {

            $page = $request->has('page') ? $request->get('page') : 1;

            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = UserLogin::join('users', 'users.id', '=', 'user_logins.user_id')
                ->select('user_logins.*', 'users.email', 'users.name')
                ->get();

            $Data_count = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->where([['users.name', 'LIKE', "%" . $request->search . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->search . "%"]])
                ->get();
        } else {

            if ($request->sortDesc) {
                $order = 'desc';
            } else {
                $order = 'asc';
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = UserLogin::join('users', 'users.id', '=', 'user_logins.user_id')
                ->select('user_logins.*', 'users.email', 'users.name')
                ->get();

            $Data_count = User::join('role_user', 'role_user.user_id', '=', $table . '.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->where([['users.name', 'LIKE', "%" . $request->search . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->search . "%"]])
                ->get();
        }

        $DataCs =   $Data->count();
        $DataCount =  $Data_count->count();

        foreach ($Data as $key => $value) {
            $Data[$key]['created'] = Carbon::parse($value['created_at'])->isoFormat('MMM Do YYYY - HH:mm');
            $Data[$key]['updated'] = Carbon::parse($value['updated_at'])->isoFormat('MMM Do YYYY - HH:mm');
        }

        if ($DataCs > 0 && $DataCount == 0) {
            $DataCount =   $DataCs;
        }

        return response()->json([
            'data' => $Data,
            'total' =>  $DataCount,
            // 'total' =>  14,
            'skip' => $skip,
            'take' => $request->itemsPerPage
        ], 200);
    }

    public function userlogin_approve(Request $request, $table_id)
    {

        $userlogin = UserLogin::where('id', $table_id)->first();
        $user =  $userlogin->user;
        $userlogin->is_approved = 1;
        $userlogin->save();

        broadcast(new ApproveLoginEvent($user));

        return response()->json([
            'success' => 1,
            'user' => $request->user(),
            '_benchmark' => microtime(true) -  $this->time_start
        ], 200);
    }

    public function data_table(Request $request)
    {


        if ($request->sort[0]['type'] == ""  ||  $request->sort[0]['field'] == "" ||   $request->sort[0]['type'] == "none") {
            $limit = $request->has('perPage') ? $request->get('perPage') : 10;

            $reqs = User::orderBy('is_admin', 'desc')
                ->offset(($request->page - 1) * $limit)
                ->take($request->perPage)
                ->get();

            foreach ($reqs as $key => $value) {

                $reqs[$key]['created'] = Carbon::parse($value['created_at'])->isoFormat('HH:mm - MMM Do YYYY ');
                $reqs[$key]['updated'] = Carbon::parse($value['updated_at'])->isoFormat('HH:mm - MMM Do YYYY ');
            }

            $count =   User::get()->count();
        } else {

            $limit = $request->has('perPage') ? $request->get('perPage') : 10;

            $reqs = User::offset(($request->page - 1) * $limit)
                ->take($request->perPage)
                ->orderBy($request->sort[0]['field'], strtoupper($request->sort[0]['type']))
                ->get();

            foreach ($reqs as $key => $value) {

                $reqs[$key]['created'] = Carbon::parse($value['created_at'])->isoFormat('HH:mm - MMM Do YYYY ');
                $reqs[$key]['updated'] = Carbon::parse($value['updated_at'])->isoFormat('HH:mm - MMM Do YYYY ');
            }

            $count =   User::get()->count();
        }


        return response()->json([
            'page' => $request->page,
            'data' => $reqs,
            'totalRecords' => $count,
            '_benchmark' => microtime(true) -  $this->time_start

        ]);
    }

    public function export(){
        return Excel::download(new UsersExport, 'users-'.Carbon::now().'.xlsx');
    }
}
