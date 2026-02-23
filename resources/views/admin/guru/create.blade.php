@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <div class="container-fluid">
    <h1>Tambah Guru</h1>
  </div>
</section>

<section class="content">
<div class="container-fluid">

<div class="card">
<div class="card-body">

<?php $isSuperadmin = session('role_id') == 1; ?>

<?php if (session('error')): ?>
  <div class="alert alert-danger">
    <?= esc((string) session('error')) ?>
  </div>
<?php endif; ?>

<form method="post"
      action="<?= base_url($isSuperadmin ? 'dashboard/superadmin/guru/store' : 'admin/guru/store') ?>"
      enctype="multipart/form-data">

  <?= csrf_field() ?>

  <div class="form-group">
    <label>Nama Depan</label>
    <input type="text"
           name="nama_depan"
           value="<?= esc(old('nama_depan')) ?>"
           class="form-control"
           required>
  </div>

  <div class="form-group">
    <label>Nama Belakang</label>
    <input type="text"
           name="nama_belakang"
           value="<?= esc(old('nama_belakang')) ?>"
           class="form-control"
           required>
  </div>

  <div class="form-group">
    <label>Username</label>
    <input type="text"
           name="username"
           value="<?= esc(old('username')) ?>"
           class="form-control"
           required>
  </div>

  <div class="form-group">
    <label>Password</label>
    <input type="password"
           name="password"
           class="form-control"
           required>
  </div>

  <div class="form-group">
    <label>Email</label>
    <input type="email"
           name="email"
           value="<?= esc(old('email')) ?>"
           class="form-control"
           required>
  </div>

  <div class="form-group">
    <label>No. HP / WhatsApp</label>
    <input type="text"
           name="no_hp"
           value="<?= esc(old('no_hp')) ?>"
           class="form-control"
           pattern="[0-9]+"
           placeholder="08xxxxxxxx"
           required>
  </div>

  <div class="form-group">
    <label>Alamat</label>
    <textarea name="alamat"
              class="form-control"
              rows="3"><?= esc(old('alamat')) ?></textarea>
  </div>

  <div class="form-group">
    <label>Tanggal Lahir</label>
    <input type="date"
           name="tanggal_lahir"
           value="<?= esc(old('tanggal_lahir')) ?>"
           class="form-control">
  </div>

  <div class="form-group">
    <label>Foto</label>
    <input type="file"
           name="foto"
           accept="image/*"
           class="form-control">
  </div>

  <hr>

  <button type="submit"
          class="btn btn-success btn-block btn-lg">
    💾 Simpan Guru
  </button>

  <a href="<?= base_url($isSuperadmin ? 'dashboard/superadmin/guru' : 'admin/guru') ?>"
     class="btn btn-secondary btn-block mt-2">
     ⬅️ Kembali
  </a>

</form>

</div>
</div>

</div>
</section>

@endsection
