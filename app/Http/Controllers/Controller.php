<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseSuccess($data = null)
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    protected function responseFailed($data = null)
    {
        return response()->json([
            'success' => false,
            'data' => $data
        ], 500);
    }
}
