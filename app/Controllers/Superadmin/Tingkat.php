<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\TingkatModel;
use Config\Database;

class Tingkat extends BaseController
{
    protected $tingkat;
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->tingkat = new TingkatModel();
        $this->db      = Database::connect();
    }

    public function index()
    {
        return view('superadmin/tingkat/index', [
            'data' => $this->tingkat->getOrdered()
        ]);
    }

    public function store()
    {
        // ===== VALIDASI =====
        $rules = [
            'kode'   => 'required|alpha_numeric|max_length[20]|is_unique[tingkat.kode]',
            'nama'   => 'required|max_length[50]',
            'urutan' => 'required|is_natural'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data tingkat tidak valid atau kode sudah dipakai');
        }

        $data = [
            'kode'     => strtoupper($this->request->getPost('kode')),
            'nama'     => $this->request->getPost('nama'),
            'urutan'   => $this->request->getPost('urutan'),
            'is_lulus' => $this->request->getPost('is_lulus') ? 1 : 0
        ];

        $this->tingkat->insert($data);

        // ===== SYSTEM LOG =====
        systemLog(
            'CREATE_TINGKAT',
            'Menambahkan tingkat '.$data['kode'],
            'tingkat'
        );

        return redirect()->back()
            ->with('success','Tingkat berhasil ditambahkan');
    }

    public function delete($id)
    {
        // ===== CEK RELASI KELAS =====
        $dipakaiKelas = $this->db->table('kelas')
            ->where('tingkat_id', $id)
            ->countAllResults();

        if ($dipakaiKelas > 0) {
            return redirect()->back()
                ->with('error','Tingkat tidak bisa dihapus karena masih digunakan oleh kelas');
        }

        $tingkat = $this->tingkat->find($id);
        if (! $tingkat) {
            return redirect()->back()
                ->with('error','Data tingkat tidak ditemukan');
        }

        $this->tingkat->delete($id);

        // ===== SYSTEM LOG =====
        systemLog(
            'DELETE_TINGKAT',
            'Menghapus tingkat '.$tingkat['kode'],
            'tingkat',
            $id
        );

        return redirect()->back()
            ->with('success','Tingkat berhasil dihapus');
    }
}
