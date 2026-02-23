@extends('layouts/pwa')
@section('content')

<div class="card">
  <h2>👋 Halo, <?= esc(session('nama')) ?></h2>
  <p>Siap melakukan absensi hari ini</p>
</div>

<div class="card">
  <button class="btn">📍 Absen Sekarang</button>
</div>

@endsection
