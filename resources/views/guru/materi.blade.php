@extends('layouts/adminlte')
@section('content')

<style>
@media(max-width:768px){
  .materi-card{
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:12px;
    margin-bottom:12px;
    background:#fff;
  }
}
.badge-new{
  background:#22c55e;
  color:#fff;
  font-size:11px;
}
</style>

<section class="content-header">
  <h1>📚 Materi Ajar</h1>
</section>

<section class="content">

<!-- FILTER KELAS -->
<form method="get" class="mb-3">
  <select name="kelas_id"
          class="form-control"
          onchange="this.form.submit()">
    <option value="">📂 Semua Kelas</option>
    <?php foreach($kelas as $k): ?>
      <option value="<?= $k['id'] ?>"
        <?= ($kelasAktif==$k['id'])?'selected':'' ?>>
        <?= esc($k['nama_kelas']) ?>
      </option>
    <?php endforeach ?>
  </select>
</form>

<?php if(empty($materi)): ?>
  <div class="alert alert-warning">
    Belum ada materi ajar.
  </div>
<?php endif ?>

<!-- LIST MATERI -->
<?php foreach($materi as $m): ?>
<div class="materi-card">
  <strong><?= esc($m['judul']) ?></strong>

  <div class="text-muted small">
    Kelas <?= esc($m['nama_kelas'] ?? '-') ?>
    • <?= date('d M Y', strtotime($m['created_at'])) ?>

    <?php
      $isNew = strtotime($m['created_at']) >= strtotime('-3 days');
      if($isNew):
    ?>
      <span class="badge badge-new ml-1">BARU</span>
    <?php endif ?>
  </div>

  <?php if($m['catatan']): ?>
    <div class="mt-1 small">
      <?= esc($m['catatan']) ?>
    </div>
  <?php endif ?>

  <div class="mt-2">
    <?php if($m['kategori']=='pdf' && $m['file']): ?>
      <a href="<?= base_url('uploads/materi/'.$m['file']) ?>"
         target="_blank"
         class="btn btn-sm btn-primary">
        📄 Download PDF
      </a>

    <?php elseif($m['kategori']=='video' && $m['file']): ?>
      <a href="<?= base_url('uploads/materi/'.$m['file']) ?>"
         target="_blank"
         class="btn btn-sm btn-success">
        🎥 Tonton Video
      </a>

    <?php elseif($m['kategori']=='link' && $m['link']): ?>
      <a href="<?= esc($m['link']) ?>"
         target="_blank"
         class="btn btn-sm btn-info">
        🔗 Buka Link
      </a>
    <?php endif ?>
  </div>
</div>
<?php endforeach ?>

</section>

@endsection
