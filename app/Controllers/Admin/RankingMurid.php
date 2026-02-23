<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;

class RankingMurid extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = Database::connect();
    }

    public function index()
    {
        $start   = $this->request->getGet('start');
        $end     = $this->request->getGet('end');
        $kelasId = $this->request->getGet('kelas_id');

        // default: bulan ini
        if (!$start || !$end) {
            $start = date('Y-m-01');
            $end   = date('Y-m-t');
        }

        $builder = $this->db->table('murid m')
            ->select('
                m.id,
                m.nama_depan,
                m.nama_belakang,
                k.nama_kelas,
                COUNT(d.id) AS total_point
            ')
            ->join('kelas k', 'k.id = m.kelas_id', 'left')
            ->join(
                'absensi_detail d',
                "d.murid_id = m.id
                 AND d.status = 'hadir'
                 AND d.tanggal BETWEEN '{$start}' AND '{$end}'",
                'left'
            )
            ->groupBy('m.id')
            ->orderBy('total_point', 'DESC');

        if ($kelasId) {
            $builder->where('m.kelas_id', $kelasId);
        }

        return view('admin/ranking_murid', [
            'rows'    => $builder->get()->getResultArray(),
            'kelas'   => $this->db->table('kelas')->orderBy('nama_kelas','ASC')->get()->getResultArray(),
            'start'   => $start,
            'end'     => $end,
            'kelasId' => $kelasId
        ]);
    }

    /* =========================
       EXPORT PDF
    ========================= */
    public function exportPdf()
    {
        $start   = $this->request->getGet('start');
        $end     = $this->request->getGet('end');
        $kelasId = $this->request->getGet('kelas_id');

        if (!$start || !$end) {
            return redirect()->back()->with('error', 'Pilih rentang tanggal');
        }

        $builder = $this->db->table('murid m')
            ->select('
                m.nama_depan,
                m.nama_belakang,
                k.nama_kelas,
                COUNT(d.id) AS total_point
            ')
            ->join('kelas k', 'k.id = m.kelas_id', 'left')
            ->join(
                'absensi_detail d',
                "d.murid_id = m.id
                 AND d.status = 'hadir'
                 AND d.tanggal BETWEEN '{$start}' AND '{$end}'",
                'left'
            )
            ->groupBy('m.id')
            ->orderBy('total_point', 'DESC');

        if ($kelasId) {
            $builder->where('m.kelas_id', $kelasId);
        }

        return view('admin/ranking_murid_pdf', [
            'rows'  => $builder->get()->getResultArray(),
            'start' => $start,
            'end'   => $end
        ]);
    }
}
