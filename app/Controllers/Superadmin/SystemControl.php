<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use Config\Database;

class SystemControl extends BaseController
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->db = Database::connect();
        helper('system_control');
    }

    public function index()
    {
        $menuSettings = [
            'guru_absen'       => ['label' => 'Guru - Absensi',        'value' => (int) setting('guru_absen', 1)],
            'guru_murid'       => ['label' => 'Guru - Data Murid',     'value' => (int) setting('guru_murid', 1)],
            'guru_materi'      => ['label' => 'Guru - Materi',         'value' => (int) setting('guru_materi', 1)],
            'guru_kegiatan'    => ['label' => 'Guru - Kegiatan',       'value' => (int) setting('guru_kegiatan', 1)],
            'admin_absen'      => ['label' => 'Admin - Absensi',       'value' => (int) setting('admin_absen', 1)],
            'admin_naik_kelas' => ['label' => 'Admin - Naik Kelas',    'value' => (int) setting('admin_naik_kelas', 1)],
            'admin_guru'       => ['label' => 'Admin - Manajemen Guru','value' => (int) setting('admin_guru', 1)],
            'admin_materi'     => ['label' => 'Admin - Bahan Ajar',    'value' => (int) setting('admin_materi', 1)],
        ];

        return view('superadmin/system_control/index',[
            'maintenance'=>isMaintenance(),
            'absensi_lock'=>isAbsensiLocked(),
            'menuSettings' => $menuSettings
        ]);
    }

    private function setSetting($key,$value)
    {
        $row = $this->db->table('system_settings')
            ->where('setting_key',$key)
            ->get()->getRowArray();

        if($row){
            $this->db->table('system_settings')
                ->where('setting_key',$key)
                ->update(['value'=>$value]);
        }else{
            $this->db->table('system_settings')
                ->insert([
                    'setting_key'=>$key,
                    'value'=>$value
                ]);
        }
    }

    public function toggleMaintenance()
    {
        $new = isMaintenance() ? '0':'1';
        $this->setSetting('maintenance_mode',$new);

        systemLog('MAINTENANCE_TOGGLE','Maintenance mode changed','system');

        return redirect()->back();
    }

    public function toggleAbsensi()
    {
        $new = isAbsensiLocked() ? '0':'1';
        $this->setSetting('absensi_lock',$new);

        systemLog('ABSENSI_LOCK','Absensi lock toggled','system');

        return redirect()->back();
    }

    public function toggleMenu(string $key)
    {
        $allowed = [
            'guru_absen',
            'guru_murid',
            'guru_materi',
            'guru_kegiatan',
            'admin_absen',
            'admin_naik_kelas',
            'admin_guru',
            'admin_materi',
        ];

        if (! in_array($key, $allowed, true)) {
            return redirect()->back()->with('error', 'Menu tidak dikenal');
        }

        $current = (int) setting($key, 1);
        $new     = $current === 1 ? 0 : 1;
        $this->setSetting($key, (string) $new);

        $statusText = $new === 1 ? 'ON' : 'OFF';
        systemLog('MENU_TOGGLE', 'Toggle '.$key.' -> '.$statusText, 'menu');
        logSuperadmin('toggle_menu', 'Toggle '.$key.' -> '.$statusText);

        return redirect()->back()->with('success', 'Pengaturan menu diperbarui');
    }
}
