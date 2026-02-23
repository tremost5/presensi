@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>Superadmin Log</h1>
  <p class="text-muted">Aktivitas strategis & keamanan sistem</p>
</section>

<section class="content">
<div class="card shadow-sm">
<div class="card-body p-0">

<table class="table table-striped mb-0">
<thead>
<tr>
  <th>Waktu</th>
  <th>Aksi</th>
  <th>Detail</th>
  <th>IP</th>
</tr>
</thead>
<tbody>
<?php if(empty($logs)): ?>
<tr>
  <td colspan="4" class="text-center text-muted">Belum ada log</td>
</tr>
<?php else: foreach($logs as $l): ?>
<tr>
  <td><?= date('d M H:i',strtotime($l['created_at'])) ?></td>
  <td><span class="badge badge-dark"><?= esc($l['action']) ?></span></td>
  <td><?= esc($l['detail']) ?></td>
  <td><?= esc($l['ip_address']) ?></td>
</tr>
<?php endforeach; endif ?>
</tbody>
</table>

</div>
</div>
</section>

@endsection
