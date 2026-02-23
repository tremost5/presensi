<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;

class TahunAjaran extends BaseController
{
    protected $tahunAjaran;

    public function __construct(){
        parent::__construct();
        $this->tahunAjaran = new TahunAjaranModel();
    }

    public function index()
    {
        return view('superadmin/tahun_ajaran/index', [
            'data' => $this->tahunAjaran->orderBy('mulai', 'DESC')->findAll(),
            'active' => $this->tahunAjaran->getActive()
        ]);
    }

    public function store()
    {
        $mulai   = $this->request->getPost('mulai') ?: $this->request->getPost('tanggal_mulai');
        $selesai = $this->request->getPost('selesai') ?: $this->request->getPost('tanggal_selesai');

        if (! $this->request->getPost('nama') || ! $mulai || ! $selesai) {
            return redirect()->back()->with('error', 'Nama dan rentang tanggal wajib diisi');
        }

        $this->tahunAjaran->insert([
            'nama'    => $this->request->getPost('nama'),
            'mulai'   => $mulai,
            'selesai' => $selesai,
        ]);

        return redirect()->back()->with('success', 'Tahun ajaran ditambahkan');
    }

    public function activate($id)
{
    $this->tahunAjaran->deactivateAll();
    $this->tahunAjaran->update($id, ['is_active' => 1]);

    systemLog(
        'ACTIVATE_TAHUN_AJARAN',
        'Mengaktifkan tahun ajaran ID '.$id,
        'tahun_ajaran',
        $id
    );

    return redirect()->back()->with('success','Tahun ajaran diaktifkan');
}


}
