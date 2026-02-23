@extends('layouts/guru')
@section('content')

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-md-12">
            <h4>Halo, <strong><?= esc($user['name']) ?></strong> 👋</h4>
        </div>
    </div>

    <div class="row">

        <!-- STATUS -->
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-body">
                    <h6>Status</h6>
                    <h4 id="status-text">...</h4>
                </div>
            </div>
        </div>

        <!-- LAST LOGIN -->
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-body">
                    <h6>Login Terakhir</h6>
                    <h4 id="last-login">...</h4>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function loadStatus() {
    fetch("<?= base_url('/guru/status') ?>")
        .then(res => res.json())
        .then(data => {
            document.getElementById('status-text').innerText = data.status;
            document.getElementById('last-login').innerText = data.last_login;
        });
}

loadStatus();
setInterval(loadStatus, 60000); // refresh tiap 1 menit
</script>

@endsection
