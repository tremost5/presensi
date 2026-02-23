<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriAjarModel extends Model
{
    protected $table = 'materi_ajar';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'judul',
        'catatan',     // ⬅️ INI YANG KURANG
        'kelas_id',
        'kategori_id',
        'file',
        'link',
        'created_by',
        'created_at'
    ];

    protected $useTimestamps = false;
}
