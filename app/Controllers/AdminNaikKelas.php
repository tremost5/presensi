<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Dompdf\Dompdf;

class AdminNaikKelas extends BaseController
{
    protected $db;

    /**
     * URUTAN RESMI KELAS
     */
    protected array $kelasOrder = [
        'PG', 'TKA', 'TKB', '1', '2', '3', '4', '5', '6', 'LULUS'
    ];

    public function __construct(){
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /* =========================
     * CEK LOCK
     * ========================= */
    private function isLocked(): bool
    {
        $tahun = date('Y') . '/' . (date('Y') + 1);

        return $this->db->table('kelas_history')
            ->where('tahun_ajaran', $tahun)
            ->where('is_locked', 1)
            ->countAllResults() > 0;
    }

    /* =========================
     * PREVIEW
     * ========================= */
    public function preview()
    {
        $db = $this->db;

        $now = $db->query("
            SELECT k.kode_kelas, COUNT(m.id) total
            FROM kelas k
            LEFT JOIN murid m ON m.kelas_id = k.id
            GROUP BY k.kode_kelas
            ORDER BY FIELD(k.kode_kelas,'PG','TKA','TKB','1','2','3','4','5','6','LULUS')
        ")->getResultArray();

        $simulasi = [];
        foreach ($this->kelasOrder as $i => $kelas) {
            if ($kelas === 'LULUS') continue;

            $target = $this->kelasOrder[$i + 1];

            $jumlah = $db->query("
                SELECT COUNT(*) total
                FROM murid m
                JOIN kelas k ON k.id = m.kelas_id
                WHERE k.kode_kelas = ?
            ", [$kelas])->getRowArray();

            $simulasi[] = [
                'kelas' => $target,
                'total' => (int) ($jumlah['total'] ?? 0)
            ];
        }

        return view('admin/naik_kelas_preview', [
            'now'          => $now,
            'simulasiNaik' => $simulasi,
            'locked'       => $this->isLocked()
        ]);
    }
    public function histori()
{
    $tahun = $this->request->getGet('tahun_ajaran')
        ?? date('Y').'/'.(date('Y')+1);

    $rows = $this->db->table('kelas_history kh')
        ->select('
            kh.id,
            kh.mode,
            kh.tahun_ajaran,
            kh.executed_at,
            u.nama_depan,
            u.nama_belakang
        ')
        ->join('users u','u.id = kh.executed_by','left')
        ->where('kh.tahun_ajaran', $tahun)
        ->orderBy('kh.executed_at','DESC')
        ->get()
        ->getResultArray();

    return view('admin/naik_kelas_histori', [
        'rows'  => $rows,
        'tahun' => $tahun
    ]);
}


    /* =========================
     * EXECUTE
     * ========================= */
    public function execute()
{
    if ($this->isLocked()) {
        return redirect()->back()->with(
            'error',
            'Kenaikan kelas sudah diproses untuk tahun ajaran ini. Undo terlebih dahulu.'
        );
    }

    $mode = $this->request->getPost('mode');
    if (!in_array($mode, ['naik', 'mundur'])) {
        return redirect()->back()->with('error', 'Mode tidak valid');
    }

    $tahun = date('Y') . '/' . (date('Y') + 1);

    // =========================
    // SNAPSHOT AWAL (UNDO)
    // =========================
    $snapshot = $this->db->table('murid')
        ->select('id, kelas_id')
        ->get()->getResultArray();

    $this->db->transStart();

    /**
     * =========================
     * BUAT TEMP TABLE
     * =========================
     */
    $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmp_naik_kelas");
    $this->db->query("
        CREATE TEMPORARY TABLE tmp_naik_kelas (
            murid_id INT,
            kelas_asal VARCHAR(10),
            kelas_tujuan VARCHAR(10)
        )
    ");

    /**
     * =========================
     * ISI MAPPING (STATIS)
     * =========================
     */
    foreach ($this->kelasOrder as $i => $kelas) {

        if ($mode === 'naik') {
            if ($kelas === 'LULUS') continue;
            $from = $kelas;
            $to   = $this->kelasOrder[$i + 1];
        } else {
            if ($kelas === 'PG') continue;
            $from = $kelas;
            $to   = $this->kelasOrder[$i - 1];
        }

        $this->db->query("
            INSERT INTO tmp_naik_kelas (murid_id, kelas_asal, kelas_tujuan)
            SELECT m.id, k.kode_kelas, ?
            FROM murid m
            JOIN kelas k ON k.id = m.kelas_id
            WHERE k.kode_kelas = ?
        ", [$to, $from]);
    }

    /**
     * =========================
     * UPDATE FINAL (1x SAJA)
     * =========================
     */
    $this->db->query("
        UPDATE murid m
        JOIN tmp_naik_kelas t ON t.murid_id = m.id
        JOIN kelas k_to ON k_to.kode_kelas = t.kelas_tujuan
        SET m.kelas_id = k_to.id
    ");

    /**
     * =========================
     * SIMPAN HISTORI & LOCK
     * =========================
     */
    $this->db->table('kelas_history')->insert([
        'mode'         => $mode,
        'tahun_ajaran' => $tahun,
        'executed_at'  => date('Y-m-d H:i:s'),
        'executed_by'  => session()->get('user_id'),
        'snapshot'     => json_encode($snapshot),
        'is_locked'    => 1
    ]);

    $this->db->transComplete();

    return redirect()->back()->with(
        'success',
        'Proses kenaikan kelas BERHASIL & data aman (tanpa cascading bug)'
    );
}

    /* =========================
     * UNDO
     * ========================= */
    public function undo()
    {
        $last = $this->db->table('kelas_history')
            ->orderBy('id', 'DESC')
            ->get(1)->getRowArray();

        if (!$last) {
            return redirect()->back()->with('error', 'Tidak ada histori');
        }

        $rows = json_decode($last['snapshot'], true);

        $this->db->transStart();

        foreach ($rows as $r) {
            $this->db->table('murid')
                ->where('id', $r['id'])
                ->update(['kelas_id' => $r['kelas_id']]);
        }

        // buka lock
        $this->db->table('kelas_history')
            ->where('id', $last['id'])
            ->delete();

        $this->db->transComplete();

        return redirect()->back()->with('success', 'UNDO berhasil & sistem terbuka');
    }
    public function exportPdf()
{
    $db = $this->db;

    $tahun = $this->request->getGet('tahun_ajaran')
        ?? date('Y').'/'.(date('Y')+1);

    $rows = $db->table('kelas_history kh')
        ->select('
            kh.id,
            kh.mode,
            kh.tahun_ajaran,
            kh.executed_at,
            u.nama_depan,
            u.nama_belakang
        ')
        ->join('users u','u.id = kh.executed_by','left')
        ->where('kh.tahun_ajaran', $tahun)
        ->orderBy('kh.executed_at','DESC')
        ->get()
        ->getResultArray();

    $html = view('admin/naik_kelas_histori_pdf', [
        'rows'  => $rows,
        'tahun' => $tahun
    ]);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4','portrait');
    $dompdf->render();
    $dompdf->stream(
        'histori_naik_kelas_'.$tahun.'.pdf',
        ['Attachment' => true]
    );
    exit;
}
public function exportSnapshotPdf($id)
{
    $db = $this->db;

    // ambil histori
    $row = $db->table('kelas_history')
        ->where('id', $id)
        ->get()
        ->getRowArray();

    if (!$row) {
        return redirect()->back()->with('error', 'Histori tidak ditemukan');
    }

    // decode snapshot
    $snapshot = json_decode($row['snapshot'], true);

    if (empty($snapshot)) {
        return redirect()->back()->with('error', 'Snapshot kosong');
    }

    // ambil detail murid + kelas
    $ids = array_column($snapshot, 'id');

    $data = $db->table('murid m')
        ->select('m.nama_depan, m.nama_belakang, k.kode_kelas')
        ->join('kelas k', 'k.id = m.kelas_id', 'left')
        ->whereIn('m.id', $ids)
        ->orderBy('k.kode_kelas', 'ASC')
        ->orderBy('m.nama_depan', 'ASC')
        ->get()
        ->getResultArray();

    // render view PDF
    $html = view('admin/naik_kelas_histori_snapshot_pdf', [
        'row'  => $row,
        'data' => $data
    ]);

    $dompdf = new Dompdf([
        'isRemoteEnabled' => true
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream(
        'snapshot_kelas_'.$row['tahun_ajaran'].'.pdf',
        ['Attachment' => true]
    );
    exit;
}
public function historiDetail($id)
{
    $db = $this->db;

    // ambil histori utama
    $row = $db->table('kelas_history')
        ->where('id', $id)
        ->get()
        ->getRowArray();

    if (!$row) {
        return redirect()->back()->with('error', 'Histori tidak ditemukan');
    }

    // decode snapshot
    $snapshot = json_decode($row['snapshot'], true);

    if (empty($snapshot)) {
        return redirect()->back()->with('error', 'Snapshot kosong');
    }

    // ambil id murid
    $ids = array_column($snapshot, 'id');

    // ambil detail murid + kelas
    $detail = $db->table('murid m')
        ->select('m.nama_depan, m.nama_belakang, k.kode_kelas')
        ->join('kelas k', 'k.id = m.kelas_id', 'left')
        ->whereIn('m.id', $ids)
        ->orderBy('k.kode_kelas', 'ASC')
        ->orderBy('m.nama_depan', 'ASC')
        ->get()
        ->getResultArray();

    return view('admin/naik_kelas_histori_detail', [
        'row'    => $row,
        'detail' => $detail
    ]);
}

}
