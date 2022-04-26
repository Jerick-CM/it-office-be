<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUsersLogs;
use App\Models\UserLogin;
use Carbon\Carbon;


class LogController extends Controller
{
    public function index(Request $request)
    {
        // $query = $this->prepareQuery($request);
        // return response()->json([
        //     'success' => 1,
        //     '_benchmark' => microtime(true) -  $this->time_start,
        // ], 200);
    }

    private function prepareQuery(Request $request)
    {
    }

    public function datatable(Request $request)
    {

        if ($request->page == 1) {
            $skip = 0;
        } else {
            $skip = $request->page * $request->page;
        }


        if ($request->sortBy == ""  && $request->sortDesc == "") {

            $page = $request->has('page') ? $request->get('page') : 1;

            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = AdminUsersLogs::limit($limit)
                ->offset(($page - 1) * $limit)
                ->take($request->itemsPerPage)->get();

            $Data_count =  AdminUsersLogs::get();
            // limit($limit)
            // ->offset(($page - 1) * $limit)
            // ->take($request->itemsPerPage)->get();

        } else {

            if ($request->sortDesc) {
                $order = 'desc';
            } else {
                $order = 'asc';
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('itemsPerPage') ? $request->get('itemsPerPage') : 10;

            $Data = AdminUsersLogs::limit($limit)
                ->offset(($page - 1) * $limit)
                ->take($request->itemsPerPage)->get();

            $Data_count =  AdminUsersLogs::get();
            // limit($limit)
            // ->offset(($page - 1) * $limit)
            // ->take($request->itemsPerPage)->get();
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

    public function fetch(Request $request)
    {

        $order = 'desc';
        if ($request->sortDesc) {
            $order = 'desc';
        } else {
            $order = 'asc';
        }

        $reqs = UserLogin::with('user')
            // ->where('is_approved',0)
            ->orderBy('id', 'desc')
            ->get();


        // $query = UserLogin::with('user')->toSql();
        return response()->json([
            'requests' => $reqs,
            // 'sql' => $query
        ]);
    }


    public function request_datatable(Request $request)
    {

        if ($request->sort[0]['type'] == ""  ||  $request->sort[0]['field'] == "" ||   $request->sort[0]['type'] == "none") {

            $limit = $request->has('perPage') ? $request->get('perPage') : 10;
            // with('user')
            $reqs =
                // UserLogin::with('user')
                UserLogin::join('users', 'users.id', '=', 'user_logins.user_id')

                ->select('user_logins.*', 'users.name', 'users.email', 'users.username')
                ->where([['users.name', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['users.email', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['user_logins.id', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orWhere([['user_logins.browser', 'LIKE', "%" . $request->searchTerm . "%"]])
                ->orderBy('is_approved', 'ASC')
                ->offset(($request->page - 1) * $limit)
                ->take($request->perPage)
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
            // with('user')
            $reqs =
                // UserLogin::with('user')
                UserLogin::join('users', 'users.id', '=', 'user_logins.user_id')
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
}
