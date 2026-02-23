<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_log';
    protected $allowedFields = [
        'user_id','aksi','keterangan','ip_address'
    ];
}
