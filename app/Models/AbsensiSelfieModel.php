<?php
namespace App\Models;

use CodeIgniter\Model;

class AbsensiSelfieModel extends Model
{
    protected $table = 'absensi_selfie';
    protected $allowedFields = [
        'guru_id','lokasi','tanggal','foto'
    ];
}
