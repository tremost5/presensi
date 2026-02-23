<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use Config\Database;

class UserRole extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = Database::connect();
    }

    public function index()
    {
        $users = $this->db->table('users')
            ->select('id,nama_depan,nama_belakang,role_id')
            ->orderBy('nama_depan')
            ->get()->getResultArray();

        return view('superadmin/users/index', compact('users'));
    }

    public function update()
    {
        $id   = $this->request->getPost('user_id');
        $role = $this->request->getPost('role_id');

        $this->db->table('users')->where('id',$id)->update([
            'role_id'=>$role
        ]);

        systemLog(
            'UPDATE_ROLE',
            'Mengubah role user ID '.$id.' ke '.$role,
            'user',
            $id
        );

        return redirect()->back()->with('success','Role diperbarui');
    }
}
