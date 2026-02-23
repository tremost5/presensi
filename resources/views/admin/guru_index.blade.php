@extends('layouts/adminlte')
@section('content')

<h3>Data Guru</h3>

<a href="/dashboard/admin/guru/create" class="btn btn-primary mb-3">
➕ Tambah Guru
</a>

<table class="table table-bordered">
<tr>
<th>Nama</th>
<th>No HP</th>
<th>Status</th>
<th>Aksi</th>
</tr>

<?php foreach($guru as $g): ?>
<tr>
<td><?= esc($g['nama_depan']) ?></td>
<td><?= esc($g['no_hp']) ?></td>
<td>
<?= $g['is_active']
    ? '<span class="badge badge-success">Aktif</span>'
    : '<span class="badge badge-danger">Belum Aktif</span>' ?>
</td>
<td>
<form method="post" action="/dashboard/admin/guru/toggle/<?= $g['id'] ?>">
<button class="btn btn-sm btn-warning">Toggle</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

@endsection
