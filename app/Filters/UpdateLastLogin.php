<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class UpdateLastLogin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return;
        }

        $userId = session()->get('user_id');
        if (!$userId) return;

        (new UserModel())->update($userId, [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
