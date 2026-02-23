<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="<?= csrf_token() ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pendaftaran Guru</title>

<link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">

<style>
:root {
  --bg-a: #f4f7ff;
  --bg-b: #f8fffb;
  --text-main: #10223d;
  --text-soft: #5f6b82;
  --line: #d6deee;
  --brand: #1668e3;
  --brand-dark: #0d4bb3;
  --danger: #d93025;
}
* {
  font-family: 'Manrope', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
body {
  min-height: 100vh;
  background:
    radial-gradient(circle at 15% 20%, #dce9ff 0%, transparent 35%),
    radial-gradient(circle at 86% 12%, #d8f4e7 0%, transparent 28%),
    linear-gradient(150deg, var(--bg-a) 0%, var(--bg-b) 100%);
  color: var(--text-main);
}
.page-wrap {
  padding: 28px 14px;
}
.register-card {
  border: 0;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 16px 40px rgba(16, 34, 61, .12);
}
.register-head {
  padding: 18px 18px 16px;
  border-bottom: 1px solid #eef2fb;
  background: linear-gradient(160deg, #ffffff 0%, #f3f7ff 100%);
}
.step-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 10px;
}
.step {
  width: 30px;
  height: 30px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  color: #fff;
  background: #c9cfdb;
}
.step.active {
  background: linear-gradient(135deg, #1671f0 0%, #20a9ff 100%);
}
.title {
  font-size: 27px;
  line-height: 1.1;
  margin: 0;
  font-weight: 800;
  text-align: center;
  letter-spacing: -.3px;
}
.subtitle {
  font-size: 13px;
  color: var(--text-soft);
  margin: 6px 0 0;
  text-align: center;
}
.register-body {
  padding: 20px;
}
.form-group label {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 6px;
}
.form-control {
  border-radius: 12px;
  border: 1px solid var(--line);
  height: 46px;
}
textarea.form-control {
  min-height: 92px;
  height: auto;
}
.form-control:focus {
  border-color: #90b7f2;
  box-shadow: 0 0 0 0.2rem rgba(22, 104, 227, 0.16);
}
.input-group-text {
  border-color: var(--line);
  border-radius: 0 12px 12px 0;
}
.help-text {
  color: var(--text-soft);
  font-size: 12px;
}
.preview-wrap {
  border: 1px dashed #bed1f4;
  background: #f9fbff;
  border-radius: 12px;
  padding: 10px;
}
#preview {
  display: none;
  width: 100%;
  border-radius: 10px;
}
.btn-register {
  border: 0;
  border-radius: 12px;
  height: 48px;
  font-weight: 800;
  letter-spacing: .2px;
  background: linear-gradient(135deg, var(--brand) 0%, #2194f3 100%);
}
.btn-register:hover,
.btn-register:focus {
  background: linear-gradient(135deg, var(--brand-dark) 0%, #0b78d8 100%);
}
.back-login {
  color: #3663a7;
  font-weight: 700;
}
.field-error {
  color: var(--danger);
  font-size: 12px;
  display: none;
}
.field-error.show {
  display: block;
}
@media (max-width: 576px) {
  .page-wrap {
    padding: 14px 8px;
  }
  .title {
    font-size: 23px;
  }
  .register-body {
    padding: 14px;
  }
}
</style>
</head>

<body>

<div class="container page-wrap">
<div class="row justify-content-center">
<div class="col-12 col-md-9 col-lg-6 col-xl-5">

<div class="card register-card">
<div class="register-head">
  <div class="step-wrap">
    <div class="step active">1</div>
    <div class="step">2</div>
  </div>
  <h5 class="title">Pendaftaran Guru</h5>
  <p class="subtitle">Isi data dengan benar untuk proses verifikasi akun.</p>
</div>

<div class="register-body">

<?php if (session()->get('errors')): ?>
<div class="alert alert-danger">
<ul class="mb-0">
<?php foreach (session()->get('errors') as $e): ?>
<li><?= esc($e) ?></li>
<?php endforeach ?>
</ul>
</div>
<?php endif ?>

<?php if (session()->get('error')): ?>
<div class="alert alert-danger">
<?= esc(session()->get('error')) ?>
</div>
<?php endif ?>

<form action="<?= site_url('register-guru') ?>" method="post" enctype="multipart/form-data">
<?= csrf_field() ?>

<!-- NAMA -->
<div class="form-group">
<label>Nama Depan</label>
<input type="text" name="nama_depan" value="<?= esc(old('nama_depan')) ?>" class="form-control" required>
</div>

<div class="form-group">
<label>Nama Belakang</label>
<input type="text" name="nama_belakang" value="<?= esc(old('nama_belakang')) ?>" class="form-control" required>
</div>

<!-- USERNAME -->
<div class="form-group">
  <label>Username</label>
  <div class="input-group">
    <input type="text"
           name="username"
           id="username"
           class="form-control"
           value="<?= esc(old('username')) ?>"
           placeholder="username untuk login"
           required
           onkeyup="checkRealtime('username', this)">
    <div class="input-group-append">
      <span class="input-group-text bg-white">
        <i class="fas fa-check text-success d-none"></i>
        <i class="fas fa-times text-danger d-none"></i>
      </span>
    </div>
  </div>
  <small class="field-error"></small>
</div>

<!-- PASSWORD -->
<div class="form-group">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<!-- EMAIL -->
<div class="form-group">
  <label>Email</label>
  <div class="input-group">
    <input type="email"
           name="email"
           id="email"
           class="form-control"
           value="<?= esc(old('email')) ?>"
           placeholder="contoh@email.com"
           required
           onkeyup="checkRealtime('email', this)">
    <div class="input-group-append">
      <span class="input-group-text bg-white">
        <i class="fas fa-check text-success d-none"></i>
        <i class="fas fa-times text-danger d-none"></i>
      </span>
    </div>
  </div>
  <small class="field-error"></small>
</div>

<!-- NO HP -->
<div class="form-group">
  <label>No WhatsApp</label>
  <div class="input-group">
    <input type="text"
           name="no_hp"
           id="no_hp"
           class="form-control"
           value="<?= esc(old('no_hp')) ?>"
           placeholder="08xxxxxxxxxx"
           required
           onkeyup="checkRealtime('no_hp', this)">
    <div class="input-group-append">
      <span class="input-group-text bg-white">
        <i class="fas fa-check text-success d-none"></i>
        <i class="fas fa-times text-danger d-none"></i>
      </span>
    </div>
  </div>
  <small class="field-error"></small>
</div>

<!-- ALAMAT -->
<div class="form-group">
<label>Alamat</label>
<textarea name="alamat" class="form-control" required><?= esc(old('alamat')) ?></textarea>
</div>

<div class="form-group">
<label>Tanggal Lahir</label>
<input
  type="text"
  name="tanggal_lahir"
  id="tanggal_lahir"
  class="form-control"
  placeholder="Contoh: 17-08-1975"
  inputmode="numeric"
  maxlength="10"
  autocomplete="bday"
  value="<?= esc(old('tanggal_lahir')) ?>"
  required>
<small class="help-text">Bisa ketik `18101985`, nanti otomatis jadi `18-10-1985`.</small>
</div>

<!-- FOTO -->
<div class="form-group">
  <label>Foto Profil</label>

  <input type="file"
         name="foto"
         id="foto"
         class="form-control"
         accept="image/*,.heic,.HEIC"
         required>

  <div class="preview-wrap mt-2">
    <img id="preview">
  </div>
</div>

<button type="submit" id="btnSubmit" class="btn btn-primary btn-block mt-3 btn-register">
Lanjutkan Pendaftaran
</button>

</form>

<a href="<?= base_url('login') ?>" class="btn btn-link btn-block back-login">Kembali ke Login</a>

</div>
</div>

</div>
</div>
</div>

<script>
const fieldErrors = {};
const pendingTimers = {};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function setFieldState(field, inputEl, msg, ok, bad, valid = false, message = '') {
  if (message) {
    msg.textContent = message;
    msg.classList.add('show');
  } else {
    msg.classList.remove('show');
  }

  inputEl.classList.remove('is-valid', 'is-invalid');
  ok.classList.add('d-none');
  bad.classList.add('d-none');

  if (valid) {
    inputEl.classList.add('is-valid');
    ok.classList.remove('d-none');
    fieldErrors[field] = false;
    return;
  }

  if (message) {
    inputEl.classList.add('is-invalid');
    bad.classList.remove('d-none');
    fieldErrors[field] = true;
    return;
  }

  fieldErrors[field] = false;
}

function checkRealtime(field, inputEl) {
  const value = inputEl.value.trim();
  const group = inputEl.closest('.form-group');
  const msg   = group.querySelector('small');
  const ok    = group.querySelector('.fa-check');
  const bad   = group.querySelector('.fa-times');

  if (!value) {
    setFieldState(field, inputEl, msg, ok, bad, false, '');
    toggleSubmit();
    return;
  }

  clearTimeout(pendingTimers[field]);
  pendingTimers[field] = setTimeout(() => runCheck(field, value, inputEl, msg, ok, bad), 280);
}

function runCheck(field, value, inputEl, msg, ok, bad) {
  const body = new URLSearchParams({
    field: field,
    value: value,
    _token: csrfToken
  });

  fetch("<?= base_url('ajax/check-user') ?>", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: body
  })
  .then(r => {
    if (!r.ok) {
      throw new Error('HTTP ' + r.status);
    }
    return r.json();
  })
  .then(res => {
    if (!res.valid) {
      setFieldState(field, inputEl, msg, ok, bad, false, res.message || 'Data tidak valid.');
    } else {
      setFieldState(field, inputEl, msg, ok, bad, true, '');
    }
    toggleSubmit();
  })
  .catch(() => {
    setFieldState(field, inputEl, msg, ok, bad, false, 'Tidak bisa cek otomatis. Validasi final saat submit.');
    fieldErrors[field] = false;
    toggleSubmit();
  });
}

function toggleSubmit() {
  const btn = document.querySelector('button[type=submit]');
  btn.disabled = Object.values(fieldErrors).some(v => v === true);
}

const tanggalInput = document.getElementById('tanggal_lahir');
if (tanggalInput) {
  tanggalInput.addEventListener('input', function () {
    const digits = this.value.replace(/\D/g, '').slice(0, 8);
    let formatted = digits;

    if (digits.length > 2) {
      formatted = digits.slice(0, 2) + '-' + digits.slice(2);
    }
    if (digits.length > 4) {
      formatted = digits.slice(0, 2) + '-' + digits.slice(2, 4) + '-' + digits.slice(4);
    }

    this.value = formatted;
  });
}
</script>
<script>
const fotoInput = document.getElementById('foto');
if (fotoInput) {
  fotoInput.addEventListener('change', function() {
    if (!this.files.length) return;

    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(this.files[0]);
    img.style.display = 'block';
  });
}
</script>

</body>
</html>
