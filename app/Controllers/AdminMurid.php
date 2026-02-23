<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminMurid extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = \Config\Database::connect();
        helper(['session', 'wa']);
    }

    /* =========================
     * LIST MURID
     * ========================= */
    public function index()
    {
        $kelasId = (int) ($this->request->getGet('kelas_id') ?? 0);
        $q       = trim((string) ($this->request->getGet('q') ?? ''));

        $builder = $this->db->table('murid m')
            ->select('m.*, k.nama_kelas')
            ->join('kelas k', 'k.id = m.kelas_id', 'left')
            ->orderBy('k.nama_kelas', 'ASC')
            ->orderBy('m.nama_depan', 'ASC');

        if ($kelasId > 0) {
            $builder->where('m.kelas_id', $kelasId);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('m.nama_depan', $q)
                ->orLike('m.nama_belakang', $q)
                ->orLike('m.panggilan', $q)
                ->groupEnd();
        }

        return view('admin/murid_index', [
            'murid'      => $builder->get()->getResultArray(),
            'kelas'      => $this->db->table('kelas')->orderBy('nama_kelas', 'ASC')->get()->getResultArray(),
            'kelasAktif' => $kelasId,
            'q'          => $q,
        ]);
    }

    /* =========================
     * FORM UPLOAD
     * ========================= */
    public function importForm()
    {
        return view('admin/murid_import');
    }

    /* =========================
     * PREVIEW EXCEL
     * ========================= */
    public function importPreview()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        $spreadsheet = IOFactory::load($file->getTempName());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        unset($rows[0]); // hapus header

        $data = [];

        foreach ($rows as $row) {

            /**
             * ASUMSI KOLOM EXCEL:
             * [1] Nama Lengkap
             * [3] ID Kelas
             * [6] No HP Ortu
             * [7] Alamat
             * [8] Jenis Kelamin
             * [9] Nama Panggilan
             */

            $namaLengkap = trim($row[1] ?? '');
            $kelas       = (int)($row[3] ?? 0);

            if ($namaLengkap === '' || $kelas === 0) {
                continue;
            }

            $namaArr = explode(' ', $namaLengkap, 2);

            $data[] = [
                'nama_depan'    => $namaArr[0],
                'nama_belakang' => $namaArr[1] ?? '',
                'panggilan'     => trim($row[9] ?? $namaArr[0]), // fallback ke nama depan
                'kelas_id'      => $kelas,
                'jenis_kelamin' => strtoupper(trim($row[8] ?? '')),
                'alamat'        => trim($row[7] ?? ''),
                'no_hp'         => formatWA($row[6] ?? ''),
                'foto'          => 'default.png'
            ];
        }

        session()->set('import_murid', $data);

        return view('admin/murid_import_preview', [
            'data' => $data
        ]);
    }

    /* =========================
     * EXECUTE IMPORT
     * ========================= */
    public function importExecute()
    {
        $data = session()->get('import_murid');

        if (!$data) {
            return redirect()->to('/admin/murid/import')
                ->with('error', 'Data preview tidak ditemukan');
        }

        $inserted = 0;

        foreach ($data as $m) {

            // CEGAH DUPLIKAT (NAMA + KELAS)
            $cek = $this->db->table('murid')
                ->where('nama_depan', $m['nama_depan'])
                ->where('nama_belakang', $m['nama_belakang'])
                ->where('kelas_id', $m['kelas_id'])
                ->get()
                ->getRow();

            if ($cek) {
                continue;
            }

            $this->db->table('murid')->insert([
                'nama_depan'    => $m['nama_depan'],
                'nama_belakang' => $m['nama_belakang'],
                'panggilan'     => $m['panggilan'],
                'kelas_id'      => $m['kelas_id'],
                'jenis_kelamin' => $m['jenis_kelamin'],
                'alamat'        => $m['alamat'],
                'no_hp'         => $m['no_hp'],
                'foto'          => $m['foto'],
                'status'        => 'aktif',
                'created_at'    => date('Y-m-d H:i:s')
            ]);

            $inserted++;
        }

        session()->remove('import_murid');

        return redirect()->to('/admin/murid/import')
            ->with('success', "Import berhasil. Murid ditambahkan: {$inserted}");
    }
}
