<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjaranModel extends Model
{
    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama', 'mulai', 'selesai', 'is_active'
    ];
    protected $useTimestamps = true;

    public function getActive()
    {
        return $this->where('is_active', 1)->first();
    }

    public function deactivateAll()
    {
        return $this->set('is_active', 0)->update();
    }
}
