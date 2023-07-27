<?php


if (!function_exists(!"generateShortUrl")) {
    function generateShortUrl(int $id): string
    {
        return base_convert($id, 10, 36);
    }
}

if (!function_exists("responseSuccess")) {
    function responseSuccess($data, $message = "OK", $status = 200)
    {
        return response()->json(["data" => $data, "message" => $message], $status);
    }
}

if (!function_exists("responseError")) {
    function responseError($message = "Error", $status = 422, $data = null)
    {
        return response()->json(["data" => $data, "message" => $message], $status);
    }
}