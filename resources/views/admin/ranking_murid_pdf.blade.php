<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Ranking Murid Rajin</title>
<style>
body { font-family: Arial; font-size: 12px; }
h2 { text-align: center; margin-bottom: 5px; }
p { text-align: center; margin-top: 0; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { border: 1px solid #000; padding: 6px; }
th { background: #eee; }
.text-center { text-align: center; }
</style>
</head>
<body>

<h2>Laporan Ranking Murid Rajin</h2>
<p><?= esc($start) ?> s/d <?= esc($end) ?></p>

<table>
<thead>
<tr>
  <th width="50">Rank</th>
  <th>Nama Murid</th>
  <th>Kelas</th>
  <th width="80">Point</th>
</tr>
</thead>
<tbody>

<?php
$rank = 1;
foreach ($rows as $r):
  $nama = trim(($r['nama_depan'] ?? '').' '.($r['nama_belakang'] ?? ''));
?>
<tr>
  <td class="text-center"><?= $rank ?></td>
  <td><?= esc($nama) ?></td>
  <td><?= esc($r['nama_kelas'] ?? '-') ?></td>
  <td class="text-center"><?= (int)$r['total_point'] ?></td>
</tr>
<?php $rank++; endforeach ?>

</tbody>
</table>

</body>
</html>
