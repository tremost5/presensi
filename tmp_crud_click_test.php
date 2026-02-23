<?php
$base = 'http://127.0.0.1:8000';

function out($msg){ echo $msg, "\n"; }

function httpReq(string $method, string $url, string $cookie, array $opts = []): array {
    $ch = curl_init($url);
    $headers = $opts['headers'] ?? [];
    $isMultipart = $opts['multipart'] ?? false;
    $data = $opts['data'] ?? null;

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);

    if ($method === 'POST') {
        $xsrf = readXsrfFromCookieFile($cookie);
        if ($xsrf !== '') {
            $headers[] = 'X-XSRF-TOKEN: '.$xsrf;
        }
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data !== null) {
            if ($isMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            }
        }
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $raw = curl_exec($ch);
    if ($raw === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException("Curl error: {$err}");
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hsize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $header = substr($raw, 0, $hsize);
    $body = substr($raw, $hsize);
    $loc = '';
    if (preg_match('/\r?\nLocation:\s*([^\r\n]+)/i', $header, $m)) {
        $loc = trim($m[1]);
    }

    return ['code'=>$code, 'header'=>$header, 'body'=>$body, 'location'=>$loc];
}

function readXsrfFromCookieFile(string $cookieFile): string {
    if (!is_file($cookieFile)) {
        return '';
    }

    $lines = file($cookieFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        $parts = explode("\t", $line);
        if (count($parts) < 7) {
            continue;
        }

        $name = $parts[5] ?? '';
        $value = $parts[6] ?? '';
        if ($name === 'XSRF-TOKEN') {
            return urldecode($value);
        }
    }

    return '';
}

function extractToken(string $html): string {
    if (preg_match('/name="_token"\s+value="([^"]+)"/', $html, $m)) return $m[1];
    if (preg_match('/<meta\s+name="csrf-token"\s+content="([^"]+)"/i', $html, $m)) return $m[1];
    return '';
}

function extractCaptchaAns(string $html): int {
    if (preg_match('/placeholder="\s*(\d+)\s*\+\s*(\d+)\s*=\s*\?\s*"/', $html, $m)) {
        return ((int)$m[1]) + ((int)$m[2]);
    }
    return 0;
}

function loginCookie(string $base, string $u, string $p): string {
    $cookie = tempnam(sys_get_temp_dir(), 'ck_');
    $r1 = httpReq('GET', $base.'/login', $cookie);
    $token = extractToken($r1['body']);
    $captcha = extractCaptchaAns($r1['body']);

    $r2 = httpReq('POST', $base.'/login', $cookie, [
        'data' => [
            '_token' => $token,
            'username' => $u,
            'password' => $p,
            'captcha' => $captcha,
        ],
    ]);

    if (!in_array($r2['code'], [302, 303], true)) {
        throw new RuntimeException("Login gagal {$u}, code={$r2['code']}");
    }

    return $cookie;
}

function assertCode(array $resp, array $allowed, string $label): void {
    if (!in_array($resp['code'], $allowed, true)) {
        throw new RuntimeException("{$label} gagal: HTTP {$resp['code']}");
    }
}

function dbConn(): PDO {
    $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=presensi-dscmkids;charset=utf8mb4';
    return new PDO($dsn, 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

$pdo = dbConn();
$stamp = date('YmdHis');
$result = [];

try {
    // SUPERADMIN
    out("[SUPERADMIN] login");
    $sa = loginCookie($base, 'demo_superadmin', 'Demo123');

    $activeBefore = (int)($pdo->query("SELECT id FROM tahun_ajaran WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetchColumn() ?: 0);

    $r = httpReq('GET', $base.'/superadmin/tingkat', $sa);
    assertCode($r, [200], 'GET tingkat');
    $tok = extractToken($r['body']);

    $kode = 'TST'.$stamp;
    out("[SUPERADMIN] create tingkat {$kode}");
    $r = httpReq('POST', $base.'/superadmin/tingkat/store', $sa, [
        'data' => [
            '_token' => $tok,
            'kode' => $kode,
            'nama' => 'Tingkat Test '.$stamp,
            'urutan' => 99,
            'is_lulus' => 0,
        ],
    ]);
    assertCode($r, [302,303], 'POST tingkat/store');

    $st = $pdo->prepare("SELECT id FROM tingkat WHERE kode=? ORDER BY id DESC LIMIT 1");
    $st->execute([$kode]);
    $tingkatId = (int)($st->fetchColumn() ?: 0);
    if ($tingkatId <= 0) throw new RuntimeException('Tingkat baru tidak ditemukan di DB');

    out("[SUPERADMIN] delete tingkat id={$tingkatId}");
    $r = httpReq('GET', $base.'/superadmin/tingkat', $sa);
    $tok = extractToken($r['body']);
    $r = httpReq('POST', $base.'/superadmin/tingkat/delete/'.$tingkatId, $sa, [
        'data' => ['_token'=>$tok],
    ]);
    assertCode($r, [302,303], 'POST tingkat/delete');

    $cek = $pdo->prepare("SELECT COUNT(*) FROM tingkat WHERE id=?");
    $cek->execute([$tingkatId]);
    if ((int)$cek->fetchColumn() !== 0) throw new RuntimeException('Delete tingkat gagal (masih ada)');

    out("[SUPERADMIN] create+activate tahun ajaran test");
    $r = httpReq('GET', $base.'/superadmin/tahun-ajaran', $sa);
    assertCode($r,[200],'GET tahun-ajaran');
    $tok = extractToken($r['body']);

    $namaTa = 'TA TEST '.$stamp;
    $mulai = date('Y-m-d');
    $selesai = date('Y-m-d', strtotime('+6 months'));
    $r = httpReq('POST', $base.'/superadmin/tahun-ajaran/store', $sa, [
        'data' => ['_token'=>$tok,'nama'=>$namaTa,'mulai'=>$mulai,'selesai'=>$selesai],
    ]);
    assertCode($r,[302,303],'POST tahun-ajaran/store');

    $st = $pdo->prepare("SELECT id FROM tahun_ajaran WHERE nama=? ORDER BY id DESC LIMIT 1");
    $st->execute([$namaTa]);
    $taId = (int)($st->fetchColumn() ?: 0);
    if ($taId <= 0) throw new RuntimeException('Tahun ajaran test tidak ditemukan');

    $r = httpReq('GET', $base.'/superadmin/tahun-ajaran', $sa);
    $tok = extractToken($r['body']);
    $r = httpReq('POST', $base.'/superadmin/tahun-ajaran/activate/'.$taId, $sa, [
        'data' => ['_token'=>$tok],
    ]);
    assertCode($r,[302,303],'POST tahun-ajaran/activate test');

    if ($activeBefore > 0) {
        $r = httpReq('GET', $base.'/superadmin/tahun-ajaran', $sa);
        $tok = extractToken($r['body']);
        $r = httpReq('POST', $base.'/superadmin/tahun-ajaran/activate/'.$activeBefore, $sa, [
            'data' => ['_token'=>$tok],
        ]);
        assertCode($r,[302,303],'POST tahun-ajaran/activate restore');
    }

    @unlink($sa);
    $result[] = 'SUPERADMIN CRUD core: OK';

    // ADMIN
    out("[ADMIN] login");
    $ad = loginCookie($base, 'demo_admin', 'Demo123');

    $kelasId = (int)($pdo->query("SELECT id FROM kelas ORDER BY id ASC LIMIT 1")->fetchColumn() ?: 0);
    if ($kelasId <= 0) throw new RuntimeException('kelas kosong, tidak bisa tes bahan ajar');

    $r = httpReq('GET', $base.'/admin/bahan-ajar', $ad);
    assertCode($r,[200],'GET admin/bahan-ajar');
    $tok = extractToken($r['body']);

    $judul = 'Materi Test '.$stamp;
    out("[ADMIN] create bahan ajar {$judul}");
    $dummyPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'materi_test_'.$stamp.'.txt';
    file_put_contents($dummyPath, 'materi test '.$stamp);
    $r = httpReq('POST', $base.'/admin/bahan-ajar/upload', $ad, [
        'headers' => ['X-Requested-With: XMLHttpRequest','Accept: application/json'],
        'multipart' => true,
        'data' => [
            '_token' => $tok,
            'judul' => $judul,
            'catatan' => 'catatan test',
            'kelas_id' => $kelasId,
            'kategori' => 'umum',
            'link' => 'https://example.com',
            'file' => curl_file_create($dummyPath, 'text/plain', 'materi-test.txt'),
        ],
    ]);
    assertCode($r,[200],'POST bahan-ajar/upload');
    if (stripos($r['body'], '"status":"ok"') === false) throw new RuntimeException('Upload bahan ajar tidak mengembalikan status ok');

    $st = $pdo->prepare("SELECT id FROM materi_ajar WHERE judul=? ORDER BY id DESC LIMIT 1");
    $st->execute([$judul]);
    $materiId = (int)($st->fetchColumn() ?: 0);
    if ($materiId <= 0) throw new RuntimeException('Materi test tidak ditemukan');

    out("[ADMIN] update bahan ajar id={$materiId}");
    $r = httpReq('POST', $base.'/admin/bahan-ajar/update-ajax/'.$materiId, $ad, [
        'headers' => ['X-Requested-With: XMLHttpRequest','Accept: application/json'],
        'data' => [
            '_token' => $tok,
            'judul' => $judul.' Updated',
            'catatan' => 'catatan update',
            'kelas_id' => $kelasId,
            'kategori' => 'update',
            'link' => 'https://example.com/u',
        ],
    ]);
    assertCode($r,[200],'POST bahan-ajar/update');
    if (stripos($r['body'], '"status":"ok"') === false) throw new RuntimeException('Update bahan ajar gagal');

    out("[ADMIN] delete bahan ajar id={$materiId}");
    $r = httpReq('POST', $base.'/admin/bahan-ajar/delete-ajax/'.$materiId, $ad, [
        'headers' => ['X-Requested-With: XMLHttpRequest','Accept: application/json'],
        'data' => ['_token'=>$tok],
    ]);
    assertCode($r,[200],'POST bahan-ajar/delete');
    if (stripos($r['body'], '"status":"ok"') === false) throw new RuntimeException('Delete bahan ajar gagal');

    $cek = $pdo->prepare("SELECT COUNT(*) FROM materi_ajar WHERE id=?");
    $cek->execute([$materiId]);
    if ((int)$cek->fetchColumn() !== 0) throw new RuntimeException('Delete materi gagal (masih ada)');
    @unlink($dummyPath);

    @unlink($ad);
    $result[] = 'ADMIN CRUD core: OK';

    // GURU
    out("[GURU] login");
    $gu = loginCookie($base, 'demo_guru', 'Demo123');

    $r = httpReq('GET', $base.'/guru/murid/create', $gu);
    assertCode($r,[200],'GET guru/murid/create');
    $tok = extractToken($r['body']);

    if (!preg_match('/<option\s+value="(\d+)"[^>]*>/', $r['body'], $m)) {
        throw new RuntimeException('Tidak ada kelas untuk input murid');
    }
    $kelasGuru = (int)$m[1];

    $muridDepan = 'Uji'.$stamp;
    out("[GURU] create murid {$muridDepan}");
    $r = httpReq('POST', $base.'/guru/murid/store', $gu, [
        'data' => [
            '_token' => $tok,
            'nama_depan' => $muridDepan,
            'nama_belakang' => 'QA',
            'panggilan' => 'Uji',
            'kelas_id' => $kelasGuru,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2018-01-01',
            'alamat' => 'Alamat test',
            'no_hp' => '081234567890',
        ],
    ]);
    assertCode($r,[302,303],'POST guru/murid/store');

    $st = $pdo->prepare("SELECT id FROM murid WHERE nama_depan=? AND nama_belakang='QA' ORDER BY id DESC LIMIT 1");
    $st->execute([$muridDepan]);
    $muridId = (int)($st->fetchColumn() ?: 0);
    if ($muridId <= 0) throw new RuntimeException('Murid test tidak ditemukan');

    out("[GURU] update murid id={$muridId}");
    $r = httpReq('GET', $base.'/guru/murid/edit/'.$muridId, $gu);
    assertCode($r,[200],'GET guru/murid/edit');
    $tok = extractToken($r['body']);

    $r = httpReq('POST', $base.'/guru/murid/update/'.$muridId, $gu, [
        'data' => [
            '_token' => $tok,
            'nama_depan' => $muridDepan,
            'nama_belakang' => 'QA-UPDATED',
            'panggilan' => 'Uji2',
            'kelas_id' => $kelasGuru,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2018-01-01',
            'alamat' => 'Alamat update',
            'no_hp' => '081234567891',
        ],
    ]);
    assertCode($r,[302,303],'POST guru/murid/update');

    $cek = $pdo->prepare("SELECT nama_belakang,panggilan FROM murid WHERE id=?");
    $cek->execute([$muridId]);
    $row = $cek->fetch(PDO::FETCH_ASSOC) ?: [];
    if (($row['nama_belakang'] ?? '') !== 'QA-UPDATED') throw new RuntimeException('Update murid tidak tersimpan');

    @unlink($gu);
    $result[] = 'GURU CRU core: OK';

    out("\n=== HASIL ===");
    foreach ($result as $line) out($line);
    out("STATUS: PASS");
    exit(0);
} catch (Throwable $e) {
    out("STATUS: FAIL");
    out($e->getMessage());
    exit(1);
}
