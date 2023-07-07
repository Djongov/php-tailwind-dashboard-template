<?php
namespace Response;

class DieCode
{
    public static $message;
    public static $code;

    final public static function kill(string $message, int $code): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        die(json_encode([
            "timestamp (UTC)" => gmdate("Y-m-d H:i:s"),
            "error" => $message
        ]));
    }
    final public static function success(string $message, int $code = 200) : string
    {
        http_response_code($code);
        header('Content-Type: application/json');
        die(json_encode([
            "timestamp (UTC)" => gmdate("Y-m-d H:i:s"),
            "success" => $message
        ]));
    }
}
