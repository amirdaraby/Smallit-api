<?php


if (!function_exists(!"generateShortUrl")) {
    function generateShortUrl(int $id): string
    {
        return base_convert($id, 10, 36);
    }
}