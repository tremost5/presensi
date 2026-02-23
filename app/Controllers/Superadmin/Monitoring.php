<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use Config\Database;

class Monitoring extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = Database::connect();
        helper('tahun_ajaran');
    }

    public function index()
    {
        $tahunAjaranId = tahunAjaranIdAktif();

if (!$tahunAjaranId) {
    return redirect()->back()->with('error','Tahun ajaran aktif tidak ditemukan');
}


        // ADMIN (role 2)
        $admin = $this->db->table('users u')
            ->select('
                u.id,
                u.nama_depan,
                u.nama_belakang,
                u.last_seen,
                u.is_active
            ')
            ->where('u.role_id', 2)
            ->orderBy('u.nama_depan', 'ASC')
            ->get()
            ->getResultArray();

        // GURU (role 3) + statistik absensi
        $guru = $this->db->table('users u')
            ->select('
                u.id,
                u.nama_depan,
                u.nama_belakang,
                u.last_seen,
                COUNT(a.id) AS total_absen
            ')
            ->join(
                'absensi a',
                'a.guru_id = u.id AND a.tahun_ajaran_id = ' . (int) $tahunAjaranId,
                'left'
            )
            ->where('u.role_id', 3)
            ->groupBy('u.id')
            ->orderBy('u.nama_depan', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($guru as &$g) {
            $lastSeen = $g['last_seen'] ?? null;
            $g['online'] = $lastSeen && (time() - strtotime($lastSeen) <= 300);
        }
        unset($g);


        return view('superadmin/monitoring/index', [
            'admin' => $admin,
            'guru'  => $guru
        ]);
    }
}
