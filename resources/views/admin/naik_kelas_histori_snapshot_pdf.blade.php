<!doctype html>
<html><head>
<style>
body{font-family:Arial;font-size:12px}
table{width:100%;border-collapse:collapse}
td,th{border:1px solid #000;padding:5px}
</style>
</head><body>

<h3>Snapshot Detail Murid</h3>
<p>Tahun Ajaran: <?= $row['tahun_ajaran'] ?></p>

<table>
<tr><th>Nama</th><th>Kelas</th></tr>
<?php foreach ($data as $d): ?>
<tr>
<td><?= $d['nama_depan'].' '.$d['nama_belakang'] ?></td>
<td><?= $d['kode_kelas'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</body></html>
