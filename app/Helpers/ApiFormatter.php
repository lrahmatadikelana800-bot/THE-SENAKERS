<?php

namespace App\Helpers;

class ApiFormatter
{
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message = 'Error', $errors = null, $code = 400)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    public static function unauthorized($message = 'Unauthorized')
    {
        return self::error($message, null, 401);
    }

    public static function forbidden($message = 'Forbidden')
    {
        return self::error($message, null, 403);
    }

    public static function notFound($message = 'Not Found')
    {
        return self::error($message, null, 404);
    }

    public static function validationError($errors, $message = 'Validation Error')
    {
        return self::error($message, $errors, 422);
    }
}