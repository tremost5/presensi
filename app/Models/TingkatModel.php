<?php

namespace App\Models;

use CodeIgniter\Model;

class TingkatModel extends Model
{
    protected $table = 'tingkat';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode','nama','urutan','is_lulus'];
    protected $useTimestamps = false;

    public function getOrdered()
    {
        return $this->orderBy('urutan','ASC')->findAll();
    }
}
