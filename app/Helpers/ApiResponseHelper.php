<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponseHelper
{
    public static function success($data = null, string $message = '', int $status = 200)
    {
        $response = [];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data instanceof JsonResource) {
            $response['data'] = $data->resolve();
        } elseif (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    public static function error(string $message, int $status = 400, $errors = null, \Throwable $exception = null)
    {
        $response = [
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        if (app()->environment('local') && $exception) {
            $response['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        return response()->json($response, $status);
    }

}
