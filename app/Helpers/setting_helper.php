<?php

function setting($key, $default = 1)
{
    static $cache = [];

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $db = \Config\Database::connect();
    $row = $db->table('system_settings')
        ->where('setting_key', $key)
        ->get()
        ->getRowArray();

    $cache[$key] = $row ? (int)$row['value'] : $default;
    return $cache[$key];
}
