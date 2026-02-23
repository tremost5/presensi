<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StatistikModel;

class Statistik extends BaseController
{
    protected $statistik;

    public function __construct(){
        parent::__construct();
        $this->statistik = new StatistikModel();
    }

    public function index()
    {
        $data = [
            'title'           => 'Statistik Kehadiran',
            'total_murid'     => $this->statistik->totalMurid(),
            'hadir_hari_ini'  => $this->statistik->hadirHariIni(),
            'absen_bulan_ini' => $this->statistik->absenPerBulan()
        ];

        return view('admin/statistik', $data);
    }
}
