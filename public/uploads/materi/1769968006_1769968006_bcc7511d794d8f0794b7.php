<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    public function before($request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        // session invalid / double login
        if (!$user || $user['session_token'] !== session()->get('session_token')) {
            session()->destroy();
            return redirect()->to('/login');
        }

        // 🔥 UPDATE LAST_LOGIN SETIAP REQUEST
        $userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    public function after($request, $response, $arguments = null)
    {
    }
}
