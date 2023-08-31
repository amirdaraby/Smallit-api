<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

class Response
{
    public static function success($data, $message = null,int $status = 200): JsonResponse
    {
        return response()->json(["status" => "success", "data" => $data, "message" => $message], $status);
    }

    public static function error(string $message = null, int $status = 422, $data = null): JsonResponse
    {
        return response()->json(["status" => "error", "data" => $data, "message" => $message], $status);
    }
}