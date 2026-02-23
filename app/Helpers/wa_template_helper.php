<?php

use Config\Database;

if (!function_exists('waTemplateDefaults')) {
    function waTemplateDefaults(): array
    {
        return [
            'register_admin' => "PENDAFTARAN GURU BARU\n\nNama: {nama_lengkap}\nUsername: {username}\nNo WA: {no_hp}\nStatus: {status}",
            'register_user' => "Shalom {nama_lengkap}\n\nTerima kasih telah mendaftar sebagai guru.\nStatus akun Anda saat ini: {status}\n\nKami akan menghubungi Anda kembali setelah akun diaktifkan.",
            'guru_status_active' => "Shalom {nama_lengkap}\n\nAkun guru Anda telah AKTIF.\nUsername: {username}\nSilakan login dan mulai menggunakan sistem.",
            'guru_status_inactive' => "Shalom {nama_lengkap}\n\nAkun guru Anda saat ini DINONAKTIFKAN.\nJika ada kesalahan, silakan hubungi Admin Sekolah.",
        ];
    }
}

if (!function_exists('waTemplateEnsureSchema')) {
    function waTemplateEnsureSchema(): void
    {
        $db = Database::connect();
        $db->query("
            CREATE TABLE IF NOT EXISTS wa_templates (
                id INT(11) NOT NULL AUTO_INCREMENT,
                template_key VARCHAR(120) NOT NULL,
                template_text TEXT NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_template_key (template_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

if (!function_exists('waRecipientEnsureSchema')) {
    function waRecipientEnsureSchema(): void
    {
        $db = Database::connect();
        $db->query("
            CREATE TABLE IF NOT EXISTS wa_recipients (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                role_id INT(11) NOT NULL,
                no_hp VARCHAR(25) NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_wa_recipient_user (user_id),
                KEY idx_wa_recipient_role_active (role_id, is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

if (!function_exists('waTemplateGet')) {
    function waTemplateGet(string $key, string $default = ''): string
    {
        waTemplateEnsureSchema();
        $db = Database::connect();
        $row = $db->table('wa_templates')
            ->where('template_key', $key)
            ->get()
            ->getRowArray();

        if ($row && isset($row['template_text'])) {
            return (string) $row['template_text'];
        }

        if ($default !== '') {
            return $default;
        }

        $defaults = waTemplateDefaults();
        return (string) ($defaults[$key] ?? '');
    }
}

if (!function_exists('waTemplateUpsert')) {
    function waTemplateUpsert(string $key, string $text): void
    {
        waTemplateEnsureSchema();
        $db = Database::connect();
        $row = $db->table('wa_templates')
            ->where('template_key', $key)
            ->get()
            ->getRowArray();

        if ($row) {
            $db->table('wa_templates')
                ->where('template_key', $key)
                ->update(['template_text' => $text]);
            return;
        }

        $db->table('wa_templates')->insert([
            'template_key' => $key,
            'template_text' => $text,
        ]);
    }
}

if (!function_exists('waTemplateRender')) {
    function waTemplateRender(string $template, array $vars = []): string
    {
        $result = $template;
        foreach ($vars as $key => $value) {
            $result = str_replace('{' . $key . '}', (string) $value, $result);
        }
        return $result;
    }
}

if (!function_exists('waRecipients')) {
    function waRecipients(array $roleIds = [1, 2], bool $onlyActive = true): array
    {
        waRecipientEnsureSchema();
        $db = Database::connect();
        $builder = $db->table('wa_recipients wr')
            ->select('wr.user_id, wr.no_hp, wr.role_id, wr.is_active')
            ->join('users u', 'u.id = wr.user_id', 'inner')
            ->whereIn('wr.role_id', $roleIds);

        if ($onlyActive) {
            $builder->where('wr.is_active', 1);
        }

        return $builder->get()->getResultArray();
    }
}

if (!function_exists('waRecipientNumbers')) {
    function waRecipientNumbers(array $roleIds = [1, 2], bool $onlyActive = true): array
    {
        $rows = waRecipients($roleIds, $onlyActive);
        $numbers = [];
        foreach ($rows as $row) {
            $no = trim((string) ($row['no_hp'] ?? ''));
            if ($no !== '') {
                $numbers[$no] = true;
            }
        }
        return array_keys($numbers);
    }
}
