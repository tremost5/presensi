<!doctype html>
<html>
<head>
<meta charset="utf-8">

<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    margin: 0;
    padding: 0;
}

/* WATERMARK */
.watermark {
    position: fixed;
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-size: 70px;
    color: #000;
    opacity: 0.07;
    z-index: -1000;
    font-weight: bold;
    letter-spacing: 6px;
}

/* HEADER */
.header {
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 12px;
}

.header-table {
    width: 100%;
}

.header-table td {
    vertical-align: middle;
}

.header-title {
    text-align: center;
}

.header-title h2 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.header-title p {
    margin: 2px 0 0;
    font-size: 11px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}

th, td {
    border: 1px solid #000;
    padding: 6px;
    font-size: 11px;
}

th {
    background: #f0f0f0;
    text-align: center;
}
</style>
</head>

<body>

<div class="watermark">DSCM KIDS</div>

<div class="header">
<table class="header-table">
<tr>
<td width="15%" align="center">
<?php
$logo = FCPATH.'assets/logo/dscmkids.png';
if (file_exists($logo)):
?>
<img src="<?= $logo ?>" width="70">
<?php endif; ?>
</td>

<td class="header-title">
<h2>HISTORI NAIK / MUNDUR KELAS</h2>
<p>Tahun Ajaran: <?= esc($tahun) ?></p>
</td>
</tr>
</table>
</div>

<table>
<thead>
<tr>
    <th width="5%">No</th>
    <th width="10%">ID</th>
    <th width="15%">Mode</th>
    <th width="25%">Waktu</th>
    <th>Dieksekusi Oleh</th>
</tr>
</thead>

<tbody>
<?php if (empty($rows)): ?>
<tr>
<td colspan="5" align="center">Tidak ada histori</td>
</tr>
<?php endif; ?>

<?php $no=1; foreach ($rows as $r): ?>
<tr>
<td align="center"><?= $no++ ?></td>
<td align="center"><?= $r['id'] ?></td>
<td align="center"><?= strtoupper($r['mode']) ?></td>
<td align="center"><?= $r['executed_at'] ?></td>
<td>
<?= esc(trim(($r['nama_depan'] ?? '').' '.($r['nama_belakang'] ?? ''))) ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>
