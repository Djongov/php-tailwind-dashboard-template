<?php

namespace Database;

class SQLite
{
    public static function connect()
    {
        $db = new \SQLite3(__DIR__ . '/../../database/database.sqlite');
        return $db;
    }
}
