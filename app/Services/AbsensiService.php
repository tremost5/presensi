<?php

namespace App\Services;

use Config\Database;

class AbsensiService
{
    public function unresolvedDoubleCount(string $date): int
    {
        return Database::connect()
            ->table('absensi_detail')
            ->where('tanggal', $date)
            ->where('status', 'dobel')
            ->countAllResults();
    }
}
