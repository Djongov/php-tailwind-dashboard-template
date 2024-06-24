<?php declare(strict_types=1);

namespace App\Logs;

use App\Authentication\JWT;
use App\Database\DB;
use App\Authentication\AuthToken;

class SystemLog
{
    public static function write($message, $category) : void
    {
        if (AuthToken::get() !== null) {
            $username = JWT::extractUserName(AuthToken::get());
        } else {
            $username = 'unknown';
        }
        // Find out the real client IP
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $clientIp = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = str_replace(strstr($_SERVER['HTTP_CLIENT_IP'], ':'), '', $_SERVER['HTTP_CLIENT_IP']);
        } else {
            // or just use the normal remote addr
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }
        $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("INSERT INTO system_log (text, client_ip, user_agent, created_by, category, uri, method) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$message, $clientIp, $_SERVER['HTTP_USER_AGENT'], $username, $category, $fullUrl, $_SERVER['REQUEST_METHOD']]);
    }
}
