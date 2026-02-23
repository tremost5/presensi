<?php
use Config\Database;

if (! function_exists('logSuperadmin')) {
    function logSuperadmin(string $action, string $desc = null)
    {
        if (! session('user_id') || session('role_id') != 1) {
            return;
        }

        Database::connect()->table('superadmin_log')->insert([
            'superadmin_id' => session('user_id'),
            'action'        => $action,
            'description'   => $desc,
            'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent'    => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'created_at'    => date('Y-m-d H:i:s')
        ]);
    }
}
