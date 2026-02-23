<?php

use Config\Database;

if (!function_exists('settingValue')) {
    function settingValue($key)
    {
        $db = Database::connect();
        $row = $db->table('system_settings')
            ->where('setting_key',$key)
            ->get()->getRowArray();

        if (! $row) {
            return null;
        }

        // dukung skema lama (setting_value) dan skema aktif (value)
        return $row['value'] ?? ($row['setting_value'] ?? null);
    }
}

if (!function_exists('isMaintenance')) {
    function isMaintenance()
    {
        return settingValue('maintenance_mode') == '1';
    }
}

if (!function_exists('isAbsensiLocked')) {
    function isAbsensiLocked()
    {
        return settingValue('absensi_lock') == '1';
    }
}

if (! function_exists('systemLog')) {
    function systemLog(string $aksi, string $deskripsi, ?string $context = null, ?int $targetId = null): void
    {
        try {
            Database::connect()->table('system_log')->insert([
                'user_id'    => session('user_id'),
                'role_id'    => session('role_id'),
                'aksi'       => $aksi,
                'deskripsi'  => $deskripsi,
                'context'    => $context,
                'target_id'  => $targetId,
                'ip_address' => service('request')->getIPAddress(),
                'user_agent' => service('request')->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
        }
    }
}
