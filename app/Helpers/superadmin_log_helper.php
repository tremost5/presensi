<?php

use Config\Database;

if (!function_exists('logSuperadmin')) {

    function logSuperadmin(string $action, string $description = null)
    {
        if (!session('user_id') || session('role_id') != 1) {
            return;
        }

        $db = Database::connect();

        $db->table('superadmin_log')->insert([
            'superadmin_id' => session('user_id'),
            'action'        => $action,
            'description'   => $description,
            'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
