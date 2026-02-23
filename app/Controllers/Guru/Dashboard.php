<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $userModel;

    public function __construct(){
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');

        $user = $this->userModel
            ->select('name, last_login')
            ->find($userId);

        return view('dashboard/guru', [
            'user' => $user
        ]);
    }

    // AJAX: status online / idle / offline
    public function ajaxStatus()
    {
        $lastLogin = session()->get('last_login');

        if (!$lastLogin) {
            return $this->response->setJSON([
                'status' => 'Offline',
                'last_login' => '-'
            ]);
        }

        $diff = time() - strtotime($lastLogin);

        if ($diff <= 300) {
            $status = 'Online';
        } elseif ($diff <= 900) {
            $status = 'Idle';
        } else {
            $status = 'Offline';
        }

        return $this->response->setJSON([
            'status' => $status,
            'last_login' => date('d M Y H:i', strtotime($lastLogin))
        ]);
    }
}
