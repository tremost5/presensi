@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>📅 Manajemen Tahun Ajaran</h1>
</section>

<section class="content">
<div class="container-fluid">

<div class="card shadow-sm">
  <div class="card-header bg-success text-white">
    Tambah Tahun Ajaran
  </div>
  <div class="card-body">

    <form method="post" action="<?= base_url('superadmin/tahun-ajaran/store') ?>">
      <div class="row">
        <div class="col-md-4">
          <input type="text" name="nama" class="form-control"
                 placeholder="Contoh: 2024/2025" required>
        </div>
        <div class="col-md-3">
          <input type="date" name="tanggal_mulai" class="form-control" required>
        </div>
        <div class="col-md-3">
          <input type="date" name="tanggal_selesai" class="form-control" required>
        </div>
        <div class="col-md-2">
          <button class="btn btn-success btn-block">
            ➕ Tambah
          </button>
        </div>
      </div>
    </form>

  </div>
</div>

</div>
</section>

@endsection
