<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;

    public function __construct(){
        parent::__construct();
        $this->userModel = new UserModel();
    }

    // Tabel Data Guru
    public function dataGuru()
    {
        $guru = $this->userModel
            ->where('role_id', 3)
            ->orderBy('nama_depan', 'ASC')
            ->findAll();

        return view('admin/data_guru', [
            'title' => 'Data Guru',
            'guru'  => $guru,
        ]);
    }

    // Aktif / Nonaktif
    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);
        if (!$user || $user['role_id'] != 3) {
            return redirect()->back();
        }

        $statusBaru = ($user['status'] === 'aktif') ? 'nonaktif' : 'aktif';

        $this->userModel->update($id, [
            'status' => $statusBaru
        ]);

        return redirect()->back()->with('success', 'Status guru diperbarui');
    }
}
