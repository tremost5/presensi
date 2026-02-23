<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pendaftaran Berhasil</title>

<link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">

<style>
body { background:#f4f6f9; }
.card { border-radius:14px; animation: fade .5s ease; }
@keyframes fade { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:none} }
</style>
</head>

<body>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6">

      <div class="card shadow-sm">
        <div class="card-body text-center">

          <h4 class="font-weight-bold text-success mb-2">
            ✅ Pendaftaran Berhasil
          </h4>

          <p class="text-muted">
            Data Anda sudah kami terima.<br>
            Akun guru saat ini <b>MENUNGGU AKTIVASI ADMIN</b>.
          </p>

          <div class="my-3">
            <span class="badge badge-warning px-3 py-2">
              Status: Pending Aktivasi
            </span>
          </div>

          <p class="small text-muted">
            Anda akan dihubungi setelah akun diaktifkan.
          </p>

          <a href="<?= base_url('login') ?>" class="btn btn-primary btn-block mt-3">
            Kembali ke Login
          </a>

        </div>
      </div>

    </div>
  </div>
</div>

</body>
</html>
