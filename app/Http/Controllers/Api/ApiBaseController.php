<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiBaseController extends Controller
{
    public function sendSuccess($data = null, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public function sendError($message = 'Error', $errors = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    public function sendUnauthorized($message = 'Unauthorized')
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => ['auth' => ['You must login first to access this resource']]
        ], 401);
    }

    protected function sendValidationError(ValidationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $exception->errors()
        ], 422);
    }

    protected function handleValidationException($e)
    {
        if ($e instanceof ValidationException) {
            throw new HttpResponseException($this->sendValidationError($e));
        }
        throw $e;
    }
}
