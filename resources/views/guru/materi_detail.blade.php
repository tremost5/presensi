@extends('layouts/adminlte')
@section('content')

<div class="container-fluid" style="max-width:720px">

<div class="card shadow-sm">
  <div class="card-body">

    <h4 class="mb-1"><?= esc($materi['judul']) ?></h4>
    <small class="text-muted">
      Kelas <?= esc($materi['nama_kelas'] ?? '-') ?> •
      <?= date('d M Y', strtotime($materi['created_at'])) ?>
    </small>

    <hr>

    <?php if(!empty($materi['deskripsi'])): ?>
      <p><?= nl2br(esc($materi['deskripsi'])) ?></p>
      <hr>
    <?php endif ?>

    <?php if(!empty($materi['file'])): ?>
      <a href="<?= base_url('uploads/materi/'.$materi['file']) ?>"
         class="btn btn-success btn-block"
         download>
         ⬇️ Download Materi
      </a>
    <?php else: ?>
      <div class="alert alert-warning">
        File materi belum tersedia
      </div>
    <?php endif ?>

    <a href="<?= base_url('guru/materi') ?>"
       class="btn btn-light btn-block mt-2">
       ← Kembali
    </a>

  </div>
</div>

</div>

@endsection
