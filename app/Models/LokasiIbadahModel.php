<?php
namespace App\Models;

use CodeIgniter\Model;

class LokasiIbadahModel extends Model
{
    protected $table = 'lokasi_ibadah';
    protected $allowedFields = ['nama_lokasi'];
}
