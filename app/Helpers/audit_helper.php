<?php

use Config\Database;

if (! function_exists('audit_log')) {
    function audit_log(array $data): void
    {
        try {
            $db      = Database::connect();
            $builder = $db->table('audit_log');
            $jsonFlags = JSON_UNESCAPED_UNICODE;
            if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
                $jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
            }

            $payload = [
                'user_id'    => (int) (session('user_id') ?? 0),
                'role'       => (string) (session('role_name') ?? session('role') ?? 'unknown'),
                'action'     => (string) ($data['action'] ?? 'unknown_action'),
                'severity'   => (string) ($data['severity'] ?? 'info'),
                'old_data'   => isset($data['old'])
                    ? json_encode($data['old'], $jsonFlags)
                    : null,
                'new_data'   => isset($data['new'])
                    ? json_encode($data['new'], $jsonFlags)
                    : null,
                'device'     => (string) ($data['device'] ?? 'web'),
                'ip_address' => service('request')->getIPAddress(),
                'user_agent' => service('request')->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Preferred schema in this project dump.
            if ($db->fieldExists('murid_id', 'audit_log')) {
                $payload['murid_id'] = isset($data['murid_id']) ? (int) $data['murid_id'] : null;
            }
            if ($db->fieldExists('absensi_id', 'audit_log')) {
                $payload['absensi_id'] = isset($data['absensi_id']) ? (int) $data['absensi_id'] : null;
            }
            if ($db->fieldExists('tanggal', 'audit_log')) {
                $payload['tanggal'] = $data['tanggal'] ?? date('Y-m-d');
            }

            // Backward compatibility for older schema variants.
            if ($db->fieldExists('target', 'audit_log')) {
                $payload['target'] = $data['target'] ?? null;
            }
            if ($db->fieldExists('target_id', 'audit_log')) {
                $payload['target_id'] = $data['target_id']
                    ?? $data['murid_id']
                    ?? $data['user_id']
                    ?? null;
            }

            $builder->insert($payload);
        } catch (\Throwable $e) {
            // Never break business flow because of audit logging failure.
            log_message('error', 'audit_log insert failed: {message}', ['message' => $e->getMessage()]);
        }
    }
}

if (! function_exists('logAudit')) {
    /**
     * Compatibility wrapper for legacy calls:
     * logAudit('aksi', 'severity', [...payload...])
     */
    function logAudit(string $action, string $severity = 'info', array $payload = []): void
    {
        audit_log([
            'action'     => $action,
            'severity'   => $severity,
            'target'     => $payload['target'] ?? null,
            'target_id'  => $payload['target_id'] ?? null,
            'murid_id'   => $payload['murid_id'] ?? null,
            'absensi_id' => $payload['absensi_id'] ?? null,
            'tanggal'    => $payload['tanggal'] ?? null,
            'old'        => $payload['old'] ?? null,
            'new'        => $payload['new'] ?? $payload['changes'] ?? $payload,
            'device'     => $payload['device'] ?? null,
        ]);
    }
}
