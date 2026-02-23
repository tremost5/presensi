@extends('layouts/adminlte')
@section('content')

<h3>Snapshot Detail (<?= $row['tahun_ajaran'] ?>)</h3>

<table class="table table-bordered">
<tr><th>Nama</th><th>Kelas</th></tr>
<?php foreach ($detail as $d): ?>
<tr>
<td><?= $d['nama_depan'].' '.$d['nama_belakang'] ?></td>
<td><?= $d['kode_kelas'] ?></td>
</tr>
<?php endforeach; ?>
</table>

@endsection
