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

        $limit = $request->has('perPage') ? $request->get('perPage') : 10;

        $reqs = UserLogin::with('user')
            ->orderBy('id', 'desc')
            ->offset(($request->page - 1) * $limit)
            ->take($request->perPage)
            ->get();

        $count =   UserLogin::with('user')->get()->count();

        return response()->json([
            'page' => $request->page,
            // 'req' => $request,
            'requests' => $reqs,
            'totalRecords' => $count,
            'rows' => $reqs,
        ]);
    }
}
