@extends('layouts/adminlte')
@section('content')

<h4 class="mb-3">Kegiatan Guru</h4>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-header"><strong>Input Kegiatan</strong></div>
  <div class="card-body">
    <form method="post" action="<?= base_url('guru/kegiatan/store') ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="form-group">
        <label>Tanggal</label>
        <input type="date" class="form-control" value="<?= esc($today) ?>" readonly>
      </div>

      <div class="form-group">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control" maxlength="150" required value="<?= esc(old('judul')) ?>">
      </div>

      <div class="form-group">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="3" placeholder="Isi deskripsi kegiatan"><?= esc(old('keterangan')) ?></textarea>
      </div>

      <div class="form-group">
        <label>Foto (Kamera HP)</label>
        <input
          type="file"
          name="foto"
          class="form-control-file"
          accept="image/*"
          capture="environment"
          required>
        <small class="text-muted">Di HP akan langsung membuka kamera belakang.</small>
      </div>

      <div class="mt-2 mb-3">
        <img id="previewFoto" src="" alt="" style="display:none;max-width:220px;border-radius:8px">
      </div>

      <button class="btn btn-primary">Simpan Kegiatan</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><strong>Riwayat Kegiatan</strong></div>
  <div class="card-body p-0">
    <?php if (empty($rows)): ?>
      <div class="p-3 text-muted">Belum ada kegiatan tersimpan.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered mb-0">
          <thead class="thead-light">
            <tr>
              <th style="width:130px">Tanggal</th>
              <th style="width:140px">Foto</th>
              <th>Judul</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= esc($r['tanggal']) ?></td>
                <td>
                  <a href="<?= base_url('uploads/kegiatan/' . $r['foto']) ?>" target="_blank" rel="noopener">
                    <img src="<?= base_url('uploads/kegiatan/' . $r['foto']) ?>" alt="foto kegiatan" style="width:110px;height:80px;object-fit:cover;border-radius:6px">
                  </a>
                </td>
                <td><?= esc($r['judul']) ?></td>
                <td><?= esc($r['keterangan'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
(() => {
  const input = document.querySelector('input[name="foto"]');
  const preview = document.getElementById('previewFoto');
  if (!input || !preview) return;

  input.addEventListener('change', (e) => {
    const file = e.target.files && e.target.files[0];
    if (!file) {
      preview.style.display = 'none';
      preview.src = '';
      return;
    }

    const url = URL.createObjectURL(file);
    preview.src = url;
    preview.style.display = 'block';
  });
})();
</script>

@endsection
