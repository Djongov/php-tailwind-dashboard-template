<?php

namespace Core;

class Session
{
    public static function start()
    {
        session_name("__Secure-SSID");
        session_set_cookie_params([
            'lifetime' => 86400,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None' // Set to None because of trip to MS Azure AD authentication endpoint and back
        ]);
        session_start();
    }
}
