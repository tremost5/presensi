<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
{
    $role = session()->get('role_id');

    if (!$role) {
        return redirect()->to('/login');
    }

    if (! in_array($role, $arguments)) {
        return redirect()->to('/login');
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
