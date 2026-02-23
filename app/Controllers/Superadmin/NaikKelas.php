<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use Config\Database;

class NaikKelas extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = Database::connect();
        helper(['tahun_ajaran', 'system_control']);
    }

    public function index()
{
    $model = new \App\Models\TahunAjaranModel();

    $tahunAjaran = $model
        ->orderBy('id', 'DESC')
        ->findAll();

    return view('superadmin/naik_kelas/index', [
        'tahunAjaran' => $tahunAjaran
    ]);
}


    public function proses()
    {
        $tahunLama   = tahunAjaranAktif();
        $tahunBaruId = $this->request->getPost('tahun_baru_id');

        if (! $tahunLama || empty($tahunLama['id'])) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif belum ditentukan');
        }

        if (!$tahunBaruId) {
            return redirect()->back()
                ->with('error','Tahun ajaran tujuan wajib dipilih');
        }

        $this->db->transBegin();

        // Ambil semua murid aktif + kelas + tingkat
        $murid = $this->db->query("
            SELECT 
                m.id AS murid_id,
                m.kelas_id,
                k.tingkat_id,
                t.urutan,
                t.is_lulus
            FROM murid m
            JOIN kelas k ON k.id=m.kelas_id
            JOIN tingkat t ON t.id=k.tingkat_id
            WHERE m.status='aktif'
        ")->getResultArray();

        foreach ($murid as $m) {

            // Jika sudah LULUS
            if ($m['is_lulus'] == 1) {
                $this->db->table('murid')
                    ->where('id', $m['murid_id'])
                    ->update(['status' => 'lulus']);
                continue;
            }

            // Cari tingkat berikutnya
            $nextTingkat = $this->db->table('tingkat')
                ->where('urutan', $m['urutan'] + 1)
                ->get()
                ->getRowArray();

            if (!$nextTingkat) {
                $this->db->transRollback();
                return redirect()->back()
                    ->with(
                        'error',
                        'Tingkat lanjutan tidak ditemukan. Proses dibatalkan.'
                    );
            }

            // Cari kelas tujuan
            $kelasBaru = $this->db->table('kelas')
                ->where('tingkat_id', $nextTingkat['id'])
                ->get()
                ->getRowArray();

            if (!$kelasBaru) {
                $this->db->transRollback();
                return redirect()->back()
                    ->with(
                        'error',
                        'Kelas tujuan belum dibuat untuk tingkat '.$nextTingkat['kode']
                    );
            }

            // Update murid
            $this->db->table('murid')
                ->where('id', $m['murid_id'])
                ->update([
                    'kelas_id' => $kelasBaru['id']
                ]);

            // Log per murid (optional)
            if ($this->db->tableExists('naik_kelas_log')) {
                $this->db->table('naik_kelas_log')->insert([
                    'murid_id'             => $m['murid_id'],
                    'dari_kelas_id'        => $m['kelas_id'],
                    'ke_kelas_id'          => $kelasBaru['id'],
                    'dari_tahun_ajaran_id' => $tahunLama['id'],
                    'ke_tahun_ajaran_id'   => $tahunBaruId
                ]);
            }
        }

        // ===== FINAL TRANSAKSI =====
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return redirect()->back()
                ->with('error','Proses naik kelas gagal');
        }

        $this->db->transCommit();

        // ===== SYSTEM LOG (WAJIB SETELAH COMMIT) =====
        systemLog(
            'NAIK_KELAS',
            'Menjalankan proses naik kelas massal',
            'murid'
        );

        return redirect()->back()
            ->with('success','Proses naik kelas berhasil');
    }
}
