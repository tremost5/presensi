<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profil extends BaseController
{
    protected $userModel;

    public function __construct(){
        parent::__construct();
        $this->userModel = new UserModel();
        helper('audit'); // 🔥 WAJIB
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        return view('guru/profil', [
            'guru' => $this->userModel->find($userId)
        ]);
    }

    public function update()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $old = $this->userModel->find($userId);

        /* =========================
           DATA BARU
        ========================= */
        $data = [
            'nama_depan'    => $this->request->getPost('nama_depan'),
            'nama_belakang' => $this->request->getPost('nama_belakang'),
            'no_hp'         => $this->normalizeWa($this->request->getPost('no_hp'))
        ];

        /* PASSWORD OPSIONAL */
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            );
        }

        /* FOTO PROFIL (BASE64 CROP) */
        $fotoCrop = $this->request->getPost('foto_crop');
        if (!empty($fotoCrop)) {
            $path = FCPATH . 'uploads/guru/';
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            $fotoCrop = preg_replace('#^data:image/\w+;base64,#i', '', $fotoCrop);
            $fotoCrop = base64_decode($fotoCrop);

            $fileName = uniqid('guru_') . '.jpg';
            file_put_contents($path . $fileName, $fotoCrop);

            if (!empty($old['foto']) && file_exists($path . $old['foto'])) {
                unlink($path . $old['foto']);
            }

            $data['foto'] = $fileName;
        }

        /* =========================
           SIMPAN
        ========================= */
        $this->userModel->update($userId, $data);

        /* =========================
           AUDIT LOG (HANYA JIKA BERUBAH)
        ========================= */
        $changes = [];

        foreach ($data as $k => $v) {
            if (($old[$k] ?? null) != $v) {
                $changes[$k] = [
                    'old' => $old[$k] ?? null,
                    'new' => $v
                ];
            }
        }

        if (!empty($changes)) {
            logAudit(
                'update_profil_guru',
                'info',
                [
                    'user_id' => $userId,
                    'changes' => $changes
                ]
            );
        }

        /* 🔄 SYNC SESSION */
        session()->set([
            'nama_depan'    => $data['nama_depan'],
            'nama_belakang' => $data['nama_belakang'],
            'foto'          => $data['foto'] ?? $old['foto'],
        ]);

        return redirect()
            ->to(base_url('guru/profil'))
            ->with('success', 'Profil berhasil diperbarui');
    }

    /* =========================
       NORMALISASI WA
    ========================= */
    private function normalizeWa($hp)
    {
        $hp = preg_replace('/[^0-9]/', '', (string)$hp);

        if (str_starts_with($hp, '0')) {
            $hp = '62' . substr($hp, 1);
        }

        if (!str_starts_with($hp, '62')) {
            $hp = '62' . $hp;
        }

        return $hp;
    }
}
