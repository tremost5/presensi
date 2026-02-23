<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=presensi-dscmkids;charset=utf8mb4','root','',[
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$deletedTa = $pdo->exec("DELETE FROM tahun_ajaran WHERE nama LIKE 'TA TEST %'");
$deletedMurid = $pdo->exec("DELETE FROM murid WHERE nama_depan LIKE 'Uji20%' AND nama_belakang IN ('QA','QA-UPDATED')");

echo "deleted_tahun_ajaran={$deletedTa}\n";
echo "deleted_murid={$deletedMurid}\n";
