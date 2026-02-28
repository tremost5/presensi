<?php

namespace App\Controllers;

class AdminMateri extends BaseController
{
    protected $db;
    protected $perPage = 8;

    public function __construct(){
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /* =============================
     * MAIN PAGE
     * ============================= */
    public function index()
    {
        return view('admin/bahan_ajar');
    }

    // =============================
// FETCH (AJAX LIST + SEARCH + PAGINATION)
// =============================
public function fetch()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }

    $page = (int) ($this->request->getGet('page') ?? 1);
    $q    = trim($this->request->getGet('q') ?? '');

    $limit  = 10;
    $offset = ($page - 1) * $limit;

    $builder = $this->db->table('materi_ajar m')
        ->select('m.*, k.nama_kelas')
        ->join('kelas k', 'k.id = m.kelas_id', 'left');

    if ($q !== '') {
        $builder->groupStart()
            ->like('m.judul', $q)
            ->orLike('m.catatan', $q)
        ->groupEnd();
    }

    $total = $builder->countAllResults(false);

    $data = $builder
        ->orderBy('m.created_at', 'DESC')
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();

    return $this->response->setJSON([
        'data' => $data,
        'page' => $page,
        'last' => ceil($total / $limit),
    ]);
}

    /* =============================
     * UPLOAD (AJAX)
     * ============================= */
    public function upload()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);

        $file = $this->request->getFile('file');
        $link = trim((string) ($this->request->getPost('link') ?? ''));
        $namaFile = '';

        if ($file && $file->isValid()) {
            $namaFile = time().'_'.$file->getRandomName();
            $file->move(FCPATH.'uploads/materi', $namaFile);
        } elseif ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File upload tidak valid'
            ])->setStatusCode(422);
        }

        if ($namaFile === '' && $link === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Isi minimal file atau link'
            ])->setStatusCode(422);
        }

        $this->db->table('materi_ajar')->insert([
            'judul'      => $this->request->getPost('judul'),
            'catatan'    => $this->request->getPost('catatan'),
            'kelas_id'   => $this->request->getPost('kelas_id'),
            'kategori'   => $this->request->getPost('kategori'),
            'file'       => $namaFile,
            'link'       => $link,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status'=>'ok']);
    }

    /* =============================
     * UPDATE
     * ============================= */
    public function updateAjax($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);

        $update = [
            'judul'    => $this->request->getPost('judul'),
            'catatan'  => $this->request->getPost('catatan'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'kategori' => $this->request->getPost('kategori'),
            'link'     => $this->request->getPost('link'),
        ];

        $file = $this->request->getFile('file');
        if ($file && $file->isValid()) {
            $namaFile = time().'_'.$file->getRandomName();
            $file->move(FCPATH.'uploads/materi', $namaFile);
            $update['file'] = $namaFile;
        }

        $this->db->table('materi_ajar')->where('id',$id)->update($update);
        return $this->response->setJSON(['status'=>'ok']);
    }

    /* =============================
     * DELETE
     * ============================= */
    public function deleteAjax($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);

        $this->db->table('materi_ajar')->where('id',$id)->delete();
        return $this->response->setJSON(['status'=>'ok']);
    }
}
