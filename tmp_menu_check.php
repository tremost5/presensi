<?php
$base = 'http://127.0.0.1:8000';
$roles = [
  'superadmin' => [
    'user'=>'demo_superadmin','pass'=>'Demo123',
    'menus'=>[
      '/superadmin/dashboard','/dashboard/superadmin/guru','/dashboard/superadmin/murid','/dashboard/superadmin/kelas',
      '/dashboard/superadmin/materi','/dashboard/superadmin/naik-kelas','/dashboard/superadmin/rekap-absensi',
      '/dashboard/superadmin/statistik-absensi','/dashboard/superadmin/absensi-dobel','/dashboard/superadmin/audit-log',
      '/dashboard/superadmin/profil','/superadmin/monitoring','/superadmin/users','/superadmin/tahun-ajaran',
      '/superadmin/system-control','/superadmin/system-log','/superadmin/activity-log'
    ]
  ],
  'admin' => [
    'user'=>'demo_admin','pass'=>'Demo123',
    'menus'=>[
      '/dashboard/admin','/admin/guru','/admin/absensi-dobel','/admin/rekap-absensi','/admin/statistik','/admin/foto-kegiatan',
      '/admin/audit-log?start=2026-02-22&end=2026-02-22','/admin/export-excel/mingguan','/admin/export-excel/bulanan',
      '/admin/export-excel/tahunan','/admin/bahan-ajar','/admin/naik-kelas','/admin/naik-kelas/histori','/admin/ranking-murid','/admin/profil'
    ]
  ],
  'guru' => [
    'user'=>'demo_guru','pass'=>'Demo123',
    'menus'=>[
      '/dashboard/guru','/guru/absensi','/guru/absensi-hari-ini','/guru/murid','/guru/materi','/guru/kegiatan','/guru/profil'
    ]
  ],
];

function loginCookie($base, $u, $p){
  $cookie = tempnam(sys_get_temp_dir(),'ck_');
  $ch = curl_init($base.'/login');
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie]);
  $html = curl_exec($ch); curl_close($ch);
  preg_match('/name="_token"\s+value="([^"]+)"/', (string)$html, $tm);
  preg_match('/placeholder="\s*(\d+)\s*\+\s*(\d+)\s*=\s*\?\s*"/', (string)$html, $cm);
  $token = $tm[1] ?? '';
  $captcha = ((int)($cm[1] ?? 0) + (int)($cm[2] ?? 0));

  $post = http_build_query(['_token'=>$token,'username'=>$u,'password'=>$p,'captcha'=>$captcha]);
  $ch = curl_init($base.'/login');
  curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$post,CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie]);
  curl_exec($ch); curl_close($ch);

  return $cookie;
}

foreach($roles as $role => $cfg){
  $cookie = loginCookie($base, $cfg['user'], $cfg['pass']);
  echo "\n=== {$role} ===\n";
  foreach($cfg['menus'] as $path){
    $ch = curl_init($base.$path);
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_COOKIEJAR=>$cookie,CURLOPT_COOKIEFILE=>$cookie]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $loc = '';
    if (preg_match('/\r?\nLocation:\s*([^\r\n]+)/i', (string)$resp, $m)) {
      $loc = trim($m[1]);
    }
    echo sprintf("[%3d] %-45s %s\n", $code, $path, $loc);
  }
  @unlink($cookie);
}
