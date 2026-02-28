<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use Config\Database;

class WaToken extends BaseController
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::connect();
    }

    public function index()
    {
        $row = $this->db->table('system_settings')
            ->where('setting_key', 'fonnte_token')
            ->get()
            ->getRowArray();

        $dbToken = trim((string) ($row['value'] ?? ''));
        $envToken = trim((string) env('FONNTE_TOKEN', ''));
        $effectiveToken = $dbToken !== '' ? $dbToken : $envToken;

        return view('superadmin/wa_token/index', [
            'effectiveToken' => $effectiveToken,
            'source' => $dbToken !== '' ? 'database' : 'env',
        ]);
    }

    public function save()
    {
        $token = trim((string) $this->request->getPost('fonnte_token'));
        if ($token === '') {
            return redirect()->back()->with('error', 'Token Fonnte wajib diisi.');
        }

        $row = $this->db->table('system_settings')
            ->where('setting_key', 'fonnte_token')
            ->get()
            ->getRowArray();

        if ($row) {
            $this->db->table('system_settings')
                ->where('setting_key', 'fonnte_token')
                ->update([
                    'value' => $token,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $this->db->table('system_settings')->insert([
                'setting_key' => 'fonnte_token',
                'value' => $token,
                'description' => 'Token API Fonnte',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if (function_exists('logSuperadmin')) {
            logSuperadmin('update_wa_token', 'Superadmin memperbarui token Fonnte');
        }

        return redirect()->back()->with('success', 'Token Fonnte berhasil diperbarui.');
    }
}
