<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=presensi-dscmkids;charset=utf8mb4','root','');
$tables = ['absensi','absensi_detail'];
foreach($tables as $t){
  echo "\n== $t ==\n";
  $st = $pdo->query("DESCRIBE `$t`");
  foreach($st as $r){
    echo $r['Field']." | ".$r['Type']."\n";
  }
}
