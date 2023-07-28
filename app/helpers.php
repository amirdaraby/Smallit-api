<?php


if (!function_exists(!"generateShortUrl")) {
    function generateShortUrl(int $id): string
    {
        return base_convert($id, 10, 36);
    }
}

if (!function_exists("responseSuccess")) {
    function responseSuccess($data, $message = null, $status = 200)
    {
        return response()->json(["status" => "success", "data" => $data, "message" => $message], $status);
    }
}

if (!function_exists("responseError")) {
    function responseError($message = null, $status = 422, $data = null)
    {
        return response()->json(["status" => "error", "data" => $data, "message" => $message], $status);
    }
}