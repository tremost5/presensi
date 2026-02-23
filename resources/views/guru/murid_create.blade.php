@extends('layouts/adminlte')
@section('content')

<h3>Tambah Murid</h3>
<form method="post" enctype="multipart/form-data" action="/guru/murid/store">

<input name="nama_depan" class="form-control mb-2" placeholder="Nama Depan" required>
<input name="nama_belakang" class="form-control mb-2" placeholder="Nama Belakang" required>

<select name="kelas_id" class="form-control mb-2" required>
<option value="">Pilih Kelas</option>
<option value="1">PG</option><option value="2">TKA</option><option value="3">TKB</option>
<option value="4">1</option><option value="5">2</option><option value="6">3</option>
<option value="7">4</option><option value="8">5</option><option value="9">6</option>
</select>

<input name="alamat" class="form-control mb-2" placeholder="Alamat" required>
<input name="no_hp" class="form-control mb-2" placeholder="No HP Orang Tua" required>
<input type="file" name="foto" accept="image/*" capture="environment" class="form-control mb-2">

<button class="btn btn-success btn-block">Simpan</button>
</form>

@endsection
