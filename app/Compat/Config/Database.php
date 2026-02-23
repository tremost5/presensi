<?php

namespace Config;

use App\Compat\Database\CiDatabaseConnection;

class Database
{
    private static ?CiDatabaseConnection $connection = null;

    public static function connect(): CiDatabaseConnection
    {
        return self::$connection ??= new CiDatabaseConnection();
    }
}
