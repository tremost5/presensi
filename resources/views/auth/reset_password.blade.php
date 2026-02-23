<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset Password</title>

<link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">

<style>
body {
  background: linear-gradient(135deg, #5b86e5, #36d1dc);
  min-height: 100vh;
}
.card {
  border-radius: 16px;
}
</style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
<div class="row justify-content-center">
<div class="col-12 col-md-5">

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
  <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<div class="card shadow">
<div class="card-body">

<h4 class="text-center font-weight-bold mb-3">
  🔑 Password Baru
</h4>

<form method="post" action="<?= base_url('reset-password-wa') ?>">

<?= csrf_field() ?>

<input type="password"
       name="password"
       class="form-control mb-2"
       placeholder="Password baru"
       required>

<input type="password"
       name="password_confirm"
       class="form-control mb-3"
       placeholder="Ulangi password"
       required>

<button class="btn btn-primary btn-block">
  Simpan Password
</button>

</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>
