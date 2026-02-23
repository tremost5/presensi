@extends('layouts/adminlte')
@section('content')

<h3>Histori Naik / Mundur Kelas</h3>

<form method="get" class="mb-3">
<select name="tahun_ajaran" class="form-control w-25 d-inline">
<?php for ($y=date('Y')-2;$y<=date('Y')+1;$y++): 
$t=$y.'/'.($y+1); ?>
<option value="<?= $t ?>" <?= $t==$tahun?'selected':'' ?>><?= $t ?></option>
<?php endfor; ?>
</select>
<button class="btn btn-primary">Filter</button>
</form>

<table class="table table-bordered">
<tr>
<th>ID</th><th>Mode</th><th>Tahun</th><th>Waktu</th><th>Aksi</th>
</tr>
<?php foreach ($rows as $r): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= strtoupper($r['mode']) ?></td>
<td><?= $r['tahun_ajaran'] ?></td>
<td><?= $r['executed_at'] ?></td>
<td>
<a class="btn btn-info btn-sm"
   href="<?= base_url('admin/naik-kelas/histori/detail/'.$r['id']) ?>">
Detail
</a>
<a class="btn btn-danger btn-sm"
   href="<?= base_url('admin/naik-kelas/histori/export-pdf/'.$r['id']) ?>">
PDF
</a>
</td>
</tr>
<?php endforeach; ?>
</table>

@endsection
