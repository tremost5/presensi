@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>Kenaikan Kelas</h1>
  <p class="text-muted">Simulasi & kontrol kenaikan kelas</p>
</section>

<section class="content">

<?php if ($locked): ?>
<div class="alert alert-warning">
  🔒 Kenaikan kelas sudah diproses untuk tahun ajaran ini.<br>
  Silakan lakukan <b>UNDO</b> jika ingin membuka kembali.
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header bg-secondary">Kondisi Saat Ini</div>
  <div class="card-body p-0">
    <table class="table table-bordered table-sm mb-0">
      <?php foreach ($now as $r): ?>
      <tr>
        <td><?= esc($r['kode_kelas']) ?></td>
        <td><?= esc($r['total']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

<div class="card mt-3">
  <div class="card-header bg-success">Simulasi Setelah Proses</div>
  <div class="card-body p-0">
    <table class="table table-bordered table-sm mb-0">
      <?php foreach ($simulasiNaik as $r): ?>
      <tr>
        <td><?= esc($r['kelas']) ?></td>
        <td><?= esc($r['total']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

<div class="mt-4 d-grid gap-2">

<form method="post" action="<?= base_url('admin/naik-kelas/execute') ?>">
<?= csrf_field() ?>
<input type="hidden" name="mode" value="naik">
<button class="btn btn-success" <?= $locked ? 'disabled' : '' ?>>
⬆️ Proses Naik Kelas
</button>
</form>

<form method="post" action="<?= base_url('admin/naik-kelas/undo') ?>">
<?= csrf_field() ?>
<button class="btn btn-danger">
↩️ Undo Terakhir
</button>
</form>

</div>

</section>
@endsection
