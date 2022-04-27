<?php


namespace App\Http\Controllers\Admin;

// exports
use App\Exports\RequestLoginExport;
use App\Exports\UsersExport;
use App\Exports\UsersLogsExport;

// models
use App\Models\AdminUsersLogs;
use App\Models\UserLogin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function userlogs_datatable(Request $request)
    {
        if ($request->sort[0]['type'] == ""  ||  $request->sort[0]['field'] == "" ||   $request->sort[0]['type'] == "none") {

            $limit = $request->has('perPage') ? $request->get('perPage') : 10;

            $reqs =

                AdminUsersLogs::join('users', 'users.id', '=', 'admin_users_logs.user_id')
                ->select('admin_users_logs.*', 'users.name', 'users.email', 'users.username')
                ->where([['users.name', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['admin_users_logs.id', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->offset(($request->page - 1) * $limit)
                ->take($request->perPage)
                // ->orderBy('created_at','DESC')
                ->get();

            foreach ($reqs as $key => $value) {

                $reqs[$key]['created'] = Carbon::parse($value['created_at'])->isoFormat('HH:mm - MMM Do YYYY ');
                $reqs[$key]['updated'] = Carbon::parse($value['updated_at'])->isoFormat('HH:mm - MMM Do YYYY ');
            }

            $count =   UserLogin::with('user')->get()->count();
            $query = 1;

        } else {

            $query = 2;
            $limit = $request->has('perPage') ? $request->get('perPage') : 10;

            $reqs =

                AdminUsersLogs::join('users', 'users.id', '=', 'admin_users_logs.user_id')
                ->select('user_logins.*', 'users.name', 'users.email', 'users.username')
                ->where([['users.name', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['user_logins.id', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['user_logins.browser', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->offset(($request->page - 1) * $limit)
                ->take($request->perPage)
                ->orderBy($request->sort[0]['field'], strtoupper($request->sort[0]['type']))
                ->get();

            foreach ($reqs as $key => $value) {

                $reqs[$key]['created'] = Carbon::parse($value['created_at'])->isoFormat('HH:mm - MMM Do YYYY ');
                $reqs[$key]['updated'] = Carbon::parse($value['updated_at'])->isoFormat('HH:mm - MMM Do YYYY ');
            }

            $count =   UserLogin::with('user')->get()->count();
        }

        return response()->json([
            'query' =>  $query,
            'sort-field' => $request->sort[0]['field'],
            'sort-type' => strtoupper($request->sort[0]['type']),
            'page' => $request->page,
            'data' => $reqs,
            'totalRecords' => $count,
            '_benchmark' => microtime(true) -  $this->time_start
        ]);
    }

    public function userlogs_export(Request $request)
    {
        // to avoid file corruption add start
        ob_end_clean();
        ob_start();
        // to avoid file corruption add end
        return Excel::download(new UsersLogsExport($request->id), 'userlogs-' . Carbon::now() . '.xlsx');
    }
}
