<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profil extends BaseController
{
    protected $userModel;

    public function __construct(){
        parent::__construct();
        $this->userModel = new UserModel();

        // helper aman
        helper(['text', 'audit']);
    }

    public function index()
    {
        $id = session()->get('user_id');
        if (!$id) {
            return redirect()->to('/login');
        }

        return view('admin/profil', [
            'admin' => $this->userModel->find($id)
        ]);
    }

    public function update()
    {
        $id = session()->get('user_id');
        if (!$id) {
            return redirect()->to('/login');
        }

        $old = $this->userModel->find($id);

        /* =========================
           NORMALISASI NO HP (AMAN)
        ========================= */
        $noHpRaw = $this->request->getPost('no_hp');

        if (function_exists('normalizeWa')) {
            $noHp = normalizeWa($noHpRaw);
        } else {
            $noHp = preg_replace('/[^0-9]/', '', (string)$noHpRaw);
            if (str_starts_with($noHp, '0')) {
                $noHp = '62' . substr($noHp, 1);
            }
        }

        /* =========================
           DATA BARU
        ========================= */
        $data = [
            'nama_depan'    => $this->request->getPost('nama_depan'),
            'nama_belakang' => $this->request->getPost('nama_belakang'),
            'email'         => $this->request->getPost('email'),
            'no_hp'         => $noHp,
        ];

        /* =========================
           FOTO ADMIN (AUTO CROP)
        ========================= */
        $fotoCrop = $this->request->getPost('foto_crop');
        if (!empty($fotoCrop)) {

            $path = FCPATH . 'uploads/admin/';
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            $fotoCrop = preg_replace('#^data:image/\w+;base64,#i', '', $fotoCrop);
            $fotoCrop = base64_decode($fotoCrop);

            $file = 'admin_' . uniqid() . '.jpg';
            file_put_contents($path . $file, $fotoCrop);

            if (!empty($old['foto']) && file_exists($path . $old['foto'])) {
                unlink($path . $old['foto']);
            }

            $data['foto'] = $file;
        }

        /* =========================
           UPDATE DATA
        ========================= */
        $this->userModel->update($id, $data);

        /* =========================
           AUDIT LOG (HANYA JIKA ADA PERUBAHAN)
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
                'update_profil_admin',
                'info',
                [
                    'user_id' => $id,
                    'role'    => 'admin',
                    'changes' => $changes
                ]
            );
        }

        /* =========================
           SYNC SESSION
        ========================= */
        session()->set([
            'nama_depan' => $data['nama_depan'],
            'foto'       => $data['foto'] ?? $old['foto']
        ]);

        return redirect()
            ->to(base_url('admin/profil'))
            ->with('success', 'Profil admin diperbarui');
    }
}
