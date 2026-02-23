<?php

if (!function_exists('logSuperadmin')) {
    function logSuperadmin(string $action, string $description = null)
    {
        try {
            $db = \Config\Database::connect();

            $db->table('superadmin_log')->insert([
                'superadmin_id' => session('user_id'),
                'action'        => $action,
                'description'   => $description,
                'ip_address'    => service('request')->getIPAddress(),
                'user_agent'    => service('request')->getUserAgent()->getAgentString(),
                'created_at'    => date('Y-m-d H:i:s')
            ]);

        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
        }
    }
}

if (!function_exists('isMaintenance')) {
    function isMaintenance()
    {
        $db = \Config\Database::connect();

        $row = $db->table('system_settings')
            ->where('setting_key', 'maintenance')
            ->get()
            ->getRowArray();

        return $row && $row['setting_value'] == 1;
    }
}

if (!function_exists('isAbsensiLocked')) {
    function isAbsensiLocked()
    {
        $db = \Config\Database::connect();

        $row = $db->table('system_settings')
            ->where('setting_key', 'absensi_lock')
            ->get()
            ->getRowArray();

        return $row && $row['setting_value'] == 1;
    }
}
