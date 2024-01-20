<?php

namespace Authentication;

use Authentication\AzureAD;
use Database\MYSQL;
use Logs\SystemLog;

class X5CHandler
{
    public static function load(string $appId, string $tenant, string $kid) : bool|string
    {
        // First we check for cached x5c in the database
        $x5c = MYSQL::queryPrepared("SELECT * FROM `cache` WHERE `type`='x5c' AND `unique_property`=?", [$kid]);

        // Check if we have a result
        if ($x5c->num_rows === 1) {
            $x5cResultArray = $x5c->fetch_assoc();
            // We have a result, let's check if it's expired
            if (strtotime($x5cResultArray['expiration']) > time()) {
                // We have a valid x5c, let's return it
                return $x5cResultArray['value'];
            } else {
                // We have an expired x5c, let's delete it
                MYSQL::queryPrepared("DELETE FROM `cache` WHERE `type`='x5c' AND `unique_property`=?", [$kid]);
                // Let's fetch a new x5c
                $newX5c = self::fetch($appId, $tenant, $kid);

                SystemLog::write('Fetched new X5c', 'X5C');
                // Let's return the x5c if not false
                return (!$newX5c) ? false : $newX5c;
            }
        // If no result, let's fetch a new x5c
        } else {
            // If we don't have a result, let's fetch a new x5c
            $newX5c = self::fetch($appId, $tenant, $kid);
            // Let's return the x5c if not false
            return (!$newX5c) ? false : $newX5c;
        }
    }
    public static function fetch(string $appId, string $tenant, string $kid) : ?string
    {
        // We don't have a valid x5c, let's fetch it from AzureAD
        $x5cResult = AzureAD::getSignatures($appId, $tenant, $kid);

        // Check if we have a result
        if (isset($x5cResult['x5c'][0])) {
            $x5c = $x5cResult['x5c'][0];
            // We have a result, let's cache it
            MYSQL::queryPrepared("INSERT INTO `cache` (`type`, `unique_property`, `value`, `expiration`) VALUES ('x5c', ?, ?, ?)", [$kid, $x5c, date('Y-m-d H:i:s', strtotime('+1 day'))]);

            // Return the x5c
            SystemLog::write('Fetched new X5c and wrote it to DB', 'X5C');
            return $x5c;
        } else {
            // We don't have a result, let's return false
            return false;
        }
    }
}
