@extends('layouts/guru')
@section('content')

<div class="container-fluid">

<div class="card shadow-sm">
    <div class="card-body">

        <h4 class="fw-bold mb-2">
            <?= esc($materi['judul']) ?>
        </h4>

        <div class="text-muted mb-3">
            Kelas: <?= esc($materi['kode_kelas'] ?? '-') ?> ·
            <?= date('d M Y', strtotime($materi['created_at'])) ?>
        </div>

        <p>
            <?= nl2br(esc($materi['deskripsi'] ?? '-')) ?>
        </p>

        <?php if (!empty($materi['file'])): ?>
            <a href="<?= base_url('uploads/materi/'.$materi['file']) ?>"
               class="btn btn-success mt-3"
               download>
                ⬇️ Download Materi
            </a>
        <?php else: ?>
            <div class="alert alert-warning mt-3">
                File materi belum tersedia
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= base_url('dashboard/guru') ?>" class="btn btn-secondary btn-sm">
                ← Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>

</div>

@endsection
