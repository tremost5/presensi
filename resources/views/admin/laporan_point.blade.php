@extends('layouts/adminlte')
@section('content')

<?php
$mapKelas = [
    1=>'PG',2=>'TKA',3=>'TKB',
    4=>'1',5=>'2',6=>'3',
    7=>'4',8=>'5',9=>'6'
];
?>

<style>
.point-card {
  border:1px solid #e5e7eb;
  border-radius:10px;
  padding:12px;
  margin-bottom:10px;
}
.point-rank {
  font-size:20px;
  font-weight:700;
  color:#0d6efd;
}
@media (min-width:768px){
  .point-card{display:none;}
}
@media (max-width:767px){
  table{display:none;}
}
</style>

<section class="content-header">
  <div class="container-fluid">
    <h1>Laporan Point Kehadiran</h1>
    <p class="text-muted">1 Hadir = 1 Point (global)</p>
  </div>
</section>

<section class="content">
<div class="container-fluid">

<!-- FILTER TANGGAL -->
<form method="get" class="row mb-3">
  <div class="col-md-3 mb-2">
    <label>Dari</label>
    <input type="date" name="start" value="<?= esc($start) ?>" class="form-control">
  </div>
  <div class="col-md-3 mb-2">
    <label>Sampai</label>
    <input type="date" name="end" value="<?= esc($end) ?>" class="form-control">
  </div>
  <div class="col-md-2 mb-2 d-flex align-items-end">
    <button class="btn btn-primary btn-block">🔍</button>
  </div>
</form>

<?php if (empty($rows)): ?>
<div class="text-center text-muted py-4">
  Tidak ada data
</div>
<?php endif; ?>

<!-- MOBILE -->
<?php $no=1; foreach ($rows as $r): ?>
<div class="point-card d-md-none">
  <div class="d-flex justify-content-between">
    <strong><?= esc($r['nama_depan'].' '.$r['nama_belakang']) ?></strong>
    <span class="point-rank"><?= $no ?></span>
  </div>
  <div class="text-muted small">
    Kelas <?= esc($mapKelas[$r['kelas_id']] ?? '-') ?>
  </div>
  <div class="mt-1">
    <span class="badge badge-success"><?= esc($r['point']) ?> point</span>
  </div>
</div>
<?php $no++; endforeach; ?>

<a href="<?= base_url('dashboard/admin/laporan-point/export-excel') ?>?start=<?= esc($start) ?>&end=<?= esc($end) ?>"
   class="btn btn-success mb-2">
   📊 Export Excel
</a>

<a href="<?= base_url('dashboard/admin/laporan-point/export-pdf') ?>?start=<?= esc($start) ?>&end=<?= esc($end) ?>"
   class="btn btn-danger mb-2 ml-2">
   📄 Export PDF
</a>

<!-- DESKTOP -->
<div class="table-responsive d-none d-md-block">
<table class="table table-bordered table-sm">
<thead class="thead-light">
<tr>
  <th>#</th>
  <th>Nama</th>
  <th>Kelas</th>
  <th>Total Point</th>
</tr>
</thead>
<tbody>
<?php $no=1; foreach ($rows as $r): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= esc($r['nama_depan'].' '.$r['nama_belakang']) ?></td>
  <td><?= esc($mapKelas[$r['kelas_id']] ?? '-') ?></td>
  <td><span class="badge badge-success"><?= esc($r['point']) ?></span></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

</div>
</section>

@endsection
