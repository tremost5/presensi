<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use Config\Database;

class SystemLog extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = Database::connect();
    }

    public function index()
    {
        $log = $this->db->table('system_log l')
            ->select('
                l.*,
                u.nama_depan,
                u.nama_belakang
            ')
            ->join('users u','u.id=l.user_id','left')
            ->orderBy('l.id','DESC')
            ->limit(200)
            ->get()
            ->getResultArray();

        return view('superadmin/system_log/index', [
            'log' => $log
        ]);
    }
}
