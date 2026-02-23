@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>Profil Saya</h1>
</section>

<section class="content">
<div class="card shadow-sm border-0">
  <div class="card-body">

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/profil/update') ?>" method="post">
      <?= csrf_field() ?>

      <!-- FOTO PROFIL -->
      <div class="text-center mb-3">
        <img id="previewFoto"
             src="<?= base_url('uploads/admin/'.($admin['foto'] ?? 'default.png')) ?>"
             width="120" height="120"
             class="rounded-circle mb-2"
             style="object-fit:cover">

        <input type="file"
               id="inputFoto"
               class="form-control mt-2"
               accept="image/*">

        <input type="hidden" name="foto_crop" id="fotoCrop">

        <small class="text-muted">
          Foto akan otomatis dipotong kotak (tengah)
        </small>
      </div>

      <hr>

      <!-- DATA PROFIL -->
      <div class="form-group">
        <label>Nama Depan</label>
        <input type="text" name="nama_depan" class="form-control"
               value="<?= esc($admin['nama_depan']) ?>" required>
      </div>

      <div class="form-group">
        <label>Nama Belakang</label>
        <input type="text" name="nama_belakang" class="form-control"
               value="<?= esc($admin['nama_belakang']) ?>">
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= esc($admin['email']) ?>" required>
      </div>

      <div class="form-group">
        <label>No WhatsApp</label>
        <input type="text" name="no_hp" class="form-control"
               value="<?= esc($admin['no_hp']) ?>">
      </div>

      <div class="mt-3">
        <button class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?= base_url('dashboard/admin') ?>" class="btn btn-secondary">
          Kembali
        </a>
      </div>
    </form>

  </div>
</div>
</section>

<script>
const inputFoto   = document.getElementById('inputFoto');
const previewFoto = document.getElementById('previewFoto');
const fotoCrop    = document.getElementById('fotoCrop');

inputFoto.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    const img = new Image();
    img.onload = function () {
      const size = Math.min(img.width, img.height);
      const sx = (img.width - size) / 2;
      const sy = (img.height - size) / 2;

      const canvas = document.createElement('canvas');
      canvas.width = canvas.height = 300;

      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, sx, sy, size, size, 0, 0, 300, 300);

      const cropped = canvas.toDataURL('image/jpeg', 0.9);
      previewFoto.src = cropped;
      fotoCrop.value = cropped;
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
});
</script>

@endsection
