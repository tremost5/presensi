@extends('layouts/adminlte')
@section('content')

<h3>Tambah Guru</h3>

<form method="post" action="/dashboard/admin/guru/store">
<div class="form-group">
<label>Nama Depan</label>
<input name="nama_depan" class="form-control" required>
</div>

<div class="form-group">
<label>No HP (WA)</label>
<input name="no_hp" class="form-control" placeholder="08xxx" required>
</div>

<button class="btn btn-success btn-block">
Simpan & Kirim OTP WA
</button>
</form>

@endsection
