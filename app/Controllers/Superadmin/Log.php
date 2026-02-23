<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use Config\Database;

class Log extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $logs = $db->table('superadmin_log')
            ->select('id, action, description AS detail, ip_address, created_at')
            ->orderBy('created_at','DESC')
            ->limit(200)
            ->get()
            ->getResultArray();

        logSuperadmin('view_log','Membuka halaman superadmin log');

        return view('superadmin/log',[
            'logs' => $logs
        ]);
    }
}
