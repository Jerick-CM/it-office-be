<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUsersLogs;
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
}
