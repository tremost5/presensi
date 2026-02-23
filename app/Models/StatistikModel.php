<?php

namespace App\Models;

use CodeIgniter\Model;

class StatistikModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /* ===============================
       TOTAL MURID (GLOBAL)
    =============================== */
    public function totalMurid(): int
    {
        return $this->db->table('murid')->countAllResults();
    }

    /* ===============================
       HADIR HARI INI (GLOBAL)
       source: absensi_detail + absensi
    =============================== */
    public function hadirHariIni(): int
    {
        return $this->db->table('absensi_detail ad')
            ->join('absensi a', 'a.id = ad.absensi_id')
            ->where('ad.status', 'hadir')
            ->where('a.tanggal', date('Y-m-d'))
            ->countAllResults();
    }

    /* ===============================
       GRAFIK HADIR PER BULAN (TAHUN INI)
       return: [{bulan:1, total:xx}, ...]
    =============================== */
    public function absenPerBulan(): array
    {
        return $this->db->query("
            SELECT
                MONTH(a.tanggal) AS bulan,
                COUNT(ad.id) AS total
            FROM absensi_detail ad
            JOIN absensi a ON a.id = ad.absensi_id
            WHERE ad.status = 'hadir'
              AND YEAR(a.tanggal) = YEAR(CURDATE())
            GROUP BY MONTH(a.tanggal)
            ORDER BY bulan ASC
        ")->getResultArray();
    }
}
