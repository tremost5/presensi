<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
body{font-family:Arial;font-size:12px}
table{width:100%;border-collapse:collapse}
th,td{border:1px solid #000;padding:5px}
</style>
</head>
<body>

<h3>Audit Log Sistem</h3>
<small><?= esc($start) ?> s/d <?= esc($end) ?></small>

<table>
<tr>
<th>No</th><th>Waktu</th><th>User</th><th>Aksi</th><th>Target</th>
</tr>

<?php foreach ($logs as $i => $l): ?>
<tr>
  <td><?= $i + 1 ?></td>
  <td><?= esc($l['created_at'] ?? '-') ?></td>
  <td>
    <?= esc(trim(
        ($l['nama_depan'] ?? '') . ' ' . ($l['nama_belakang'] ?? '')
    )) ?: '-' ?>
    <br>
    <small><?= esc($l['role']) ?></small>
</td>
<td><?= esc($l['action'] ?? '-') ?></td>

  <?php
  $target = $l['target'] ?? null;
  if (!$target) {
      if (!empty($l['murid_id'])) {
          $target = 'murid#' . $l['murid_id'];
      } elseif (!empty($l['absensi_id'])) {
          $target = 'absensi#' . $l['absensi_id'];
      } else {
          $target = '-';
      }
  }
  ?>
  <td><?= esc($target) ?></td>
</tr>
<?php endforeach; ?>


</table>
</body>
</html>
