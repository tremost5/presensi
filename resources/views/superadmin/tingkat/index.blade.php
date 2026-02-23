@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>🏫 Struktur Tingkat Kelas</h1>
</section>

<section class="content">
<div class="container-fluid">

<div class="card shadow-sm">
  <div class="card-header bg-info text-white">
    Tambah Tingkat
  </div>
  <div class="card-body">

    <form method="post" action="<?= base_url('superadmin/tingkat/store') ?>">
      <div class="row">
        <div class="col-md-3">
          <input type="text" name="kode" class="form-control" placeholder="PG / 1 / LULUS">
        </div>
        <div class="col-md-3">
          <input type="text" name="nama" class="form-control" placeholder="Nama Tingkat">
        </div>
        <div class="col-md-2">
          <input type="number" name="urutan" class="form-control" placeholder="Urutan">
        </div>
        <div class="col-md-2">
          <div class="form-check mt-2">
            <input type="checkbox" name="is_lulus" class="form-check-input">
            <label class="form-check-label">Tingkat Lulus</label>
          </div>
        </div>
        <div class="col-md-2">
          <button class="btn btn-info btn-block">
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
