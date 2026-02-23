@extends('layouts/adminlte')
@section('content')

<h4>System Log (Audit)</h4>

<div class="alert alert-info">
    Menampilkan 200 aktivitas terakhir level sistem.
</div>

<table class="table table-bordered table-sm">
<thead>
<tr>
    <th>Waktu</th>
    <th>User</th>
    <th>Aksi</th>
    <th>Deskripsi</th>
    <th>IP</th>
</tr>
</thead>
<tbody>

<?php foreach($log as $l): ?>
<tr>
<td><?= $l['created_at'] ?></td>
<td>
    <?= $l['nama_depan']
        ? esc($l['nama_depan'].' '.$l['nama_belakang'])
        : '-' ?>
</td>
<td><code><?= esc($l['aksi']) ?></code></td>
<td><?= esc($l['deskripsi']) ?></td>
<td><?= $l['ip_address'] ?></td>
</tr>
<?php endforeach ?>

</tbody>
</table>

@endsection
