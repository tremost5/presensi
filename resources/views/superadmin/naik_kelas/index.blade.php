@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>⬆️ Naik Kelas Otomatis</h1>
</section>

<section class="content">
<div class="container-fluid">

<div class="card border-warning shadow-sm">
  <div class="card-header bg-warning">
    ⚠️ PERHATIAN
  </div>
  <div class="card-body">

    <p>
      Proses ini akan memindahkan <strong>SEMUA murid aktif</strong>
      ke tingkat berikutnya.
    </p>

    <form method="post"
          action="<?= base_url('superadmin/naik-kelas/proses') ?>"
          onsubmit="return confirm('Yakin menjalankan proses naik kelas?')">

      <div class="row">
        <div class="col-md-4">
          <select name="tahun_baru_id" class="form-control" required>
    <option value="">-- Pilih Tahun Ajaran Tujuan --</option>

    <?php foreach($tahunAjaran as $ta): ?>
        <option value="<?= $ta['id'] ?>">
            <?= esc($ta['nama']) ?>
            <?= $ta['is_active'] ? '(AKTIF)' : '' ?>
        </option>
    <?php endforeach ?>
</select>

        </div>
        <div class="col-md-3">
          <button class="btn btn-danger">
            🚀 Jalankan Naik Kelas
          </button>
        </div>
      </div>

    </form>

  </div>
</div>

</div>
</section>

@endsection
