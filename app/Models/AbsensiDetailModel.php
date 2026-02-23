<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiDetailModel extends Model
{
    protected $table = 'absensi_detail';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'absensi_id',
        'murid_id',
        'status'
    ];

    protected $useTimestamps = false;
}
