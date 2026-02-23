<?php
namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $allowedFields = ['kode_kelas','nama_kelas'];
}
