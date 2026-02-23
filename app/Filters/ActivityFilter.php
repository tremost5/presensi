<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;

class ActivityFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->has('user_id')) {
            $model = new UserModel();
            $model->update(session()->get('user_id'), [
                'last_activity' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
