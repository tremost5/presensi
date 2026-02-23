<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class GuruKegiatan extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = Database::connect();
        $this->ensureTable();
    }

    public function index()
    {
        $guruId = (int) session('user_id');

        $rows = $this->db->table('guru_kegiatan')
            ->where('guru_id', $guruId)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return view('guru/kegiatan', [
            'rows' => $rows,
            'today' => date('Y-m-d'),
        ]);
    }

    public function store()
    {
        $guruId = (int) session('user_id');
        $judul = trim((string) $this->request->getPost('judul'));
        $keterangan = trim((string) $this->request->getPost('keterangan'));
        $foto = $this->request->getFile('foto');

        if ($judul === '') {
            return redirect()->back()->withInput()->with('error', 'Judul kegiatan wajib diisi');
        }

        if (! $foto || ! $foto->isValid()) {
            return redirect()->back()->withInput()->with('error', 'Foto kegiatan wajib diambil dari kamera');
        }

        $ext = strtolower((string) $foto->getExtension());
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return redirect()->back()->withInput()->with('error', 'Format foto harus JPG, PNG, atau WEBP');
        }

        $uploadPath = FCPATH . 'uploads/kegiatan/';
        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $fileName = 'kegiatan_' . $guruId . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        if (! $foto->move($uploadPath, $fileName)) {
            return redirect()->back()->withInput()->with('error', 'Gagal upload foto kegiatan');
        }

        $this->db->table('guru_kegiatan')->insert([
            'guru_id' => $guruId,
            'tanggal' => date('Y-m-d'),
            'judul' => $judul,
            'keterangan' => $keterangan !== '' ? $keterangan : null,
            'foto' => $fileName,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        logAudit('create_kegiatan_guru', 'info', [
            'user_id' => $guruId,
            'new' => [
                'tanggal' => date('Y-m-d'),
                'judul' => $judul,
                'keterangan' => $keterangan,
                'foto' => $fileName,
            ],
        ]);

        return redirect()->to(base_url('guru/kegiatan'))->with('success', 'Kegiatan berhasil disimpan');
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `guru_kegiatan` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `guru_id` int(11) NOT NULL,
            `tanggal` date NOT NULL,
            `judul` varchar(150) NOT NULL,
            `keterangan` text DEFAULT NULL,
            `foto` varchar(255) NOT NULL,
            `created_at` datetime DEFAULT current_timestamp(),
            `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_guru_tanggal` (`guru_id`, `tanggal`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->query($sql);
    }
}
