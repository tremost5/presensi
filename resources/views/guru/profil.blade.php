@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>Profil Saya</h1>
</section>

<section class="content">
<div class="card shadow-sm border-0">
  <div class="card-body">

    <form id="formProfil"
          action="<?= base_url('guru/profil/update') ?>"
          method="post"
          enctype="multipart/form-data">
      <?= csrf_field() ?>

      <!-- FOTO PROFIL -->
      <div class="text-center mb-4">
        <img id="previewFoto"
             src="<?= base_url('uploads/guru/'.($guru['foto'] ?? 'default.png')) ?>"
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
        <input type="text" name="nama_depan"
               class="form-control"
               required
               value="<?= esc($guru['nama_depan']) ?>">
      </div>

      <div class="form-group">
        <label>Nama Belakang</label>
        <input type="text" name="nama_belakang"
               class="form-control"
               value="<?= esc($guru['nama_belakang']) ?>">
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email"
               class="form-control"
               value="<?= esc($guru['email']) ?>"
               readonly>
        <small class="text-muted">Email tidak dapat diubah</small>
      </div>

      <div class="form-group">
        <label>No WhatsApp</label>
        <input type="text"
               name="no_hp"
               class="form-control"
               placeholder="Contoh: 08123456789"
               value="<?= esc($guru['no_hp']) ?>">
      </div>

      <hr>

      <div class="form-group">
        <label>Password Baru <small class="text-muted">(opsional)</small></label>
        <input type="password"
               name="password"
               class="form-control"
               placeholder="Kosongkan jika tidak ganti">
      </div>

      <div class="mt-3">
        <button class="btn btn-primary" id="btnSave">
          💾 Simpan Perubahan
        </button>
        <a href="<?= base_url('dashboard/guru') ?>" class="btn btn-secondary">
          Kembali
        </a>
      </div>
    </form>

  </div>
</div>
</section>

<!-- TOASTR -->
<?php if(session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  toastr.success("<?= session()->getFlashdata('success') ?>");
});
</script>
<?php endif ?>

<!-- AUTO CROP FOTO -->
<script>
const inputFoto   = document.getElementById('inputFoto');
const previewFoto = document.getElementById('previewFoto');
const fotoCrop    = document.getElementById('fotoCrop');

inputFoto.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = e => {
    const img = new Image();
    img.onload = () => {
      const size = Math.min(img.width, img.height);
      const sx = (img.width - size) / 2;
      const sy = (img.height - size) / 2;

      const canvas = document.createElement('canvas');
      canvas.width = 300;
      canvas.height = 300;

      canvas.getContext('2d')
        .drawImage(img, sx, sy, size, size, 0, 0, 300, 300);

      const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
      previewFoto.src = dataUrl;
      fotoCrop.value = dataUrl;
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
});

/* ANTI DOUBLE SUBMIT */
document.getElementById('formProfil').addEventListener('submit',()=>{
  document.getElementById('btnSave').disabled = true;
});
</script>

@endsection
