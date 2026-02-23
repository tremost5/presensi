<?php

use Config\Database;

if (!function_exists('syncWaRecipient')) {
    function syncWaRecipient(int $userId, int $roleId, string $noHp)
    {
        $db = Database::connect();

        $aktif = in_array($roleId, [1,2]) ? 1 : 0;

        $exists = $db->table('wa_recipients')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($exists) {
            $db->table('wa_recipients')
                ->where('user_id', $userId)
                ->update([
                    'role_id'   => $roleId,
                    'no_hp'     => $noHp,
                    'is_active' => $aktif
                ]);
        } else {
            if ($aktif) {
                $db->table('wa_recipients')->insert([
                    'user_id'   => $userId,
                    'role_id'   => $roleId,
                    'no_hp'     => $noHp,
                    'is_active' => 1
                ]);
            }
        }
    }
}
