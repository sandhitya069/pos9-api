<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public $data = [];

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __isset($name)
    {
        return isset($this->data[ $name ]);
    }

    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function searchableColumns($columns, $value)
    {
        $data = "";

        foreach ($columns as $key => $column) {
            $values = ($key !== 0) ? 'OR ' : "";
            $data .= $values .''. $column .' LIKE "%'. $value .'%" ';
        }

        return $data;
    }

    public function getListData($data, $searchable, $request, $perPage)
    {
        if ($request->search) {
            $data->whereRaw($this->searchableColumns($searchable, $request->search));
        }

        if ($request->sort) {
            $data->orderBy($request->sort, $request->direction ? $request->direction : "ASC");
        }

        if ($perPage) {
            $success = $data->paginate($perPage);
        } else {
            if ($request->per_page) {
                $success = $data->paginate($request->per_page);
            } else {
                $success = $data->get();
            }
        }

        return $success;
    }

    public function getDropdownData($data)
    {
        $success = $data->select('id', 'name')->get();

        return $success;
    }

    public function __construct()
    {

        $this->middleware(function ($request, $next) {

            return $next($request);
        });
    }
}
