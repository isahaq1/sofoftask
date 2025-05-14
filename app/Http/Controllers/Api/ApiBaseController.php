<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;


class ApiBaseController extends Controller
{
        /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccess($message, $statusCode = 200, $data = null, $status = 'success')
    {
        return response()->json(['status' => $status, 'message' => $message, 'data' => $data], $statusCode);
    }

 

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($message, $code = 404, $errors = null, $status = false)
    {
        dd($errors);
        throw new HttpResponseException(response()->json(['status' => $status, 'message' => $message, 'errors' => $errors], $code));
    }
}
