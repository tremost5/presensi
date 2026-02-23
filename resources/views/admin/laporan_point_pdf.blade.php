<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size:12px }
table { width:100%; border-collapse:collapse }
th, td { border:1px solid #000; padding:6px; text-align:left }
th { background:#eee }
</style>
</head>
<body>

<h3><?= esc($judul) ?></h3>

<table>
<thead>
<tr>
  <th>No</th>
  <th>Nama</th>
  <th>Kelas</th>
  <th>Point</th>
</tr>
</thead>
<tbody>
<?php $no=1; foreach ($rows as $r): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= esc($r['nama_depan'].' '.$r['nama_belakang']) ?></td>
  <td><?= esc($r['kelas_id']) ?></td>
  <td><?= esc($r['point']) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>
