<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        $sql = file_get_contents(database_path('schema/ci4_dump.sql'));
        if ($sql === false) {
            throw new RuntimeException('Cannot read CI4 SQL dump file.');
        }

        $sql = str_replace(["\r\n", "\r"], "\n", $sql);
        $sql = preg_replace('/^--.*$/m', '', $sql) ?? $sql;
        $sql = preg_replace('/\/\*!40101.*?\*\//s', '', $sql) ?? $sql;
        $sql = preg_replace('/^\\s*;\\s*$/m', '', $sql) ?? $sql;
        $sql = preg_replace('/DEFINER=`[^`]+`@`[^`]+`/i', '', $sql) ?? $sql;
        $sql = preg_replace('/CREATE\\s+ALGORITHM=.*?VIEW\\s+`murid_point`\\s+AS\\s+SELECT.*?;/is', '', $sql) ?? $sql;
        $sql = str_replace(['START TRANSACTION;', 'COMMIT;'], '', $sql);

        DB::unprepared($sql);

        DB::statement("CREATE TABLE IF NOT EXISTS `guru_kegiatan` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `guru_id` int(11) NOT NULL,
            `tanggal` date NOT NULL,
            `judul` varchar(150) NOT NULL,
            `keterangan` text DEFAULT NULL,
            `foto` varchar(255) NOT NULL,
            `created_at` datetime DEFAULT current_timestamp(),
            `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_guru_tanggal` (`guru_id`, `tanggal`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
        $tables = [
            'guru_kegiatan', 'absensi', 'absensi_detail', 'absensi_log', 'audit_log', 'ci_sessions',
            'kelas', 'kelas_history', 'lokasi_ibadah', 'materi_ajar', 'materi_log', 'murid', 'murid_point',
            'mytable', 'naik_kelas_log', 'notifikasi', 'password_otps', 'point', 'roles', 'setting_absensi',
            'setting_kelas', 'superadmin_log', 'system_log', 'system_settings', 'tahun_ajaran', 'tingkat',
            'users', 'wa_recipients'
        ];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }
};
