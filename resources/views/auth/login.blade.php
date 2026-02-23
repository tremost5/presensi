<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Sistem Presensi</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="/assets/adminlte/css/adminlte.min.css">

<style>
body {
    background: linear-gradient(135deg, #f857a6, #5b86e5);
}
.login-box {
    margin-top: 10vh;
}
.login-logo b {
    color: #fff;
}
.card {
    border-radius: 12px;
}
</style>
</head>

<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-logo">
    <b>DSCM</b> Presensi
  </div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success">
    <?= session()->getFlashdata('success') ?>
  </div>
<?php endif; ?>

  <div class="card">
    <div class="card-body login-card-body">

      <p class="login-box-msg">Silakan login</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <form action="/login" method="post">
        <?= csrf_field() ?>

        <!-- USERNAME -->
        <div class="input-group mb-3">
          <input type="text"
                 name="username"
                 class="form-control"
                 placeholder="Username"
                 required
                 autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <!-- PASSWORD -->
        <div class="form-group">
  <label>Password</label>

  <div class="input-group">
  <input type="password"
         name="password"
         id="loginPassword"
         class="form-control"
         placeholder="Password"
         required>

  <div class="input-group-append">
    <span class="input-group-text" id="toggleLoginPassword" style="cursor:pointer">
      <i class="fas fa-eye"></i>
    </span>
  </div>
</div>
</div>



        <!-- CAPTCHA -->
        <div class="input-group mb-3">
          <input type="number"
                 name="captcha"
                 class="form-control"
                 placeholder="<?= esc(session()->get('captcha_q')) ?>"
                 required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-shield-alt"></span>
            </div>
          </div>
        </div>

        <!-- SUBMIT -->
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">
              🔐 Login
            </button>
          </div>
        </div>
      </form>

      <hr>

      <p class="mb-1 text-center">
        <a href="/forgot">Lupa Password?</a>
      </p>

      <p class="mb-0 text-center">
        <a href="/register-guru" class="text-center">
          Daftar sebagai Guru
        </a>
      </p>

    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('toggleLoginPassword');
  const input  = document.getElementById('loginPassword');
  const icon   = toggle.querySelector('i');

  toggle.addEventListener('click', () => {
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
  });
});
</script>


<script src="/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/adminlte/js/adminlte.min.js"></script>

</body>
</html>
