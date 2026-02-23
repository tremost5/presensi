<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class GuruMateri extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $kelasId = (int) ($this->request->getGet('kelas_id') ?? 0);

        $builder = $this->db->table('materi_ajar m')
            ->select('m.*, k.nama_kelas')
            ->join('kelas k', 'k.id = m.kelas_id', 'left')
            ->orderBy('m.created_at', 'DESC');

        if ($kelasId > 0) {
            $builder->where('m.kelas_id', $kelasId);
        }

        $materi = $builder->get()->getResultArray();
        $kelas  = $this->db->table('kelas')->orderBy('nama_kelas', 'ASC')->get()->getResultArray();

        return view('guru/materi', [
            'materi'     => $materi,
            'kelas'      => $kelas,
            'kelasAktif' => $kelasId > 0 ? $kelasId : '',
        ]);
    }

    public function ajax($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $m = $this->db->table('materi_ajar m')
            ->select('m.*, k.nama_kelas')
            ->join('kelas k', 'k.id = m.kelas_id', 'left')
            ->where('m.id', $id)
            ->get()
            ->getRowArray();

        if (!$m) {
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Materi tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'error' => false,
            'data' => [
                'id'      => $m['id'],
                'judul'   => $m['judul'],
                'catatan' => $m['catatan'],
                'kelas'   => $m['nama_kelas'] ?? '-',
                'tanggal' => date('d M Y', strtotime($m['created_at'])),
                'file'    => $m['file'] ?? null,
                'file_ext'=> !empty($m['file']) ? strtolower(pathinfo((string) $m['file'], PATHINFO_EXTENSION)) : null,
                'file_url'=> !empty($m['file']) ? base_url('uploads/materi/'.basename((string) $m['file'])) : null,
            ]
        ]);
    }

    public function download($id)
    {
        $materi = $this->db->table('materi_ajar')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$materi || empty($materi['file'])) {
            return redirect()->to('dashboard/guru');
        }

        $file = basename($materi['file']);
        $path = FCPATH.'uploads/materi/'.$file;

        if (!is_file($path)) {
            return redirect()->to('dashboard/guru');
        }

        // audit
        $this->db->table('audit_log')->insert([
            'user_id'    => session('user_id'),
            'role'       => 'guru',
            'action'     => 'download_materi',
            'severity'   => 'info',
            'new_data'   => json_encode(['file'=>$file]),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Content-Length: '.filesize($path));
        readfile($path);
        exit;
    }
}
