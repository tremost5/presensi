@extends('layouts/adminlte')
@section('content')

<h4>Audit Log Sistem</h4>

<form class="row mb-3">
  <div class="col-md-3">
    <input type="date" name="start" value="<?= esc($start ?? '') ?>" class="form-control">
  </div>
  <div class="col-md-3">
    <input type="date" name="end" value="<?= esc($end ?? '') ?>" class="form-control">
  </div>
  <div class="col-md-3 d-flex align-items-center">
    <label class="mb-0">
      <input type="checkbox" name="alert" value="1" <?= !empty($only) ? 'checked' : '' ?>>
      Hanya penting
    </label>
  </div>
  <div class="col-md-3">
    <button class="btn btn-primary w-100">🔍 Filter</button>
  </div>
</form>

<button id="btnPdf" class="btn btn-danger btn-sm mb-3">
  📄 Export PDF
</button>

<?php if (empty($logs)): ?>
  <div class="alert alert-info">
    Belum ada audit pada rentang ini
  </div>
<?php endif; ?>

<?php foreach ($logs as $l): ?>
<?php
  $namaUser = trim(
      ($l['nama_depan'] ?? '') . ' ' . ($l['nama_belakang'] ?? '')
  );

  $sev = $l['severity'] ?? 'info';
  $badge =
      $sev === 'critical' ? 'danger' :
      ($sev === 'warning' ? 'warning' : 'primary');
?>
<div class="card mb-2">
  <div class="card-body p-2">

    <div class="d-flex justify-content-between">
      <strong>
        <?= esc(strtoupper(str_replace('_',' ', $l['action'] ?? 'aksi'))) ?>
      </strong>
      <span class="badge badge-<?= $badge ?>">
        <?= esc($sev) ?>
      </span>
    </div>

    <div class="small text-muted">
      👤 <?= esc($namaUser ?: '-') ?>
      (<?= esc($l['role'] ?? '-') ?>)
      <br>
      🕒 <?= esc($l['created_at'] ?? '-') ?>
    </div>

    <a href="<?= base_url('admin/audit-log/detail/'.$l['id']) ?>"
       class="btn btn-sm btn-outline-secondary mt-2">
       Detail
    </a>

  </div>
</div>
<?php endforeach ?>

<script>
document.getElementById('btnPdf').onclick = () => {
  const s = document.querySelector('[name=start]').value;
  const e = document.querySelector('[name=end]').value;

  if (!s || !e) {
    alert('Pilih rentang tanggal terlebih dahulu');
    return;
  }

  location.href =
    `<?= base_url('admin/audit-log/export-pdf') ?>?start=${s}&end=${e}`;
};
</script>

@endsection
