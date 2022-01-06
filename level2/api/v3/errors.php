<?php

function responseError($response_code, $errorText = null)
{
    switch ($response_code) {
        case $errorText != null:
            break;
        case 400:
            $errorText = 'Bad Request';
            break;
        case 401:
            $errorText = 'Unauthorized';
            break;
        case 405:
            $errorText = 'Method Not Allowed';
            break;
        case 500:
            $errorText = 'Internal Server Error';
            break;
    }
    http_response_code($response_code);
    die(json_encode(["error" => $errorText]));
}