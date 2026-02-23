<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Database;

class SingleLoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
{
    log_message('debug', '--- SINGLE LOGIN FILTER ---');

    if (! session()->has('user_id')) {
        log_message('debug', 'NO user_id IN SESSION');
        return redirect()->to('/login');
    }

    log_message('debug', 'SESSION user_id='.session('user_id'));
    log_message('debug', 'SESSION token='.session('session_token'));

    $db = Database::connect();

    $user = $db->table('users')
        ->select('session_token')
        ->where('id', session('user_id'))
        ->get()
        ->getRowArray();

    log_message('debug', 'DB token='.($user['session_token'] ?? 'NULL'));

    if (! $user || $user['session_token'] !== session('session_token')) {
        log_message('debug', 'TOKEN MISMATCH → LOGOUT');
        session()->destroy();
        return redirect()->to('/login')
            ->with('error', 'Akun anda login di device lain');
    }

    log_message('debug', 'TOKEN OK');
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
