@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <div class="container-fluid">
    <h1>Data Guru</h1>
  </div>
</section>

<section class="content">
  <div class="container-fluid">

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Status</th>
              <th>Online</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>

          <?php foreach ($guru as $g): 
              $online = ($g['last_login'] && strtotime($g['last_login']) >= strtotime('-10 minutes'));
          ?>
            <tr>
              <td><?= esc($g['nama_depan'].' '.$g['nama_belakang']) ?></td>
              <td><?= esc($g['email']) ?></td>

              <td>
                <?php if ($g['status'] === 'aktif'): ?>
                  <span class="badge badge-success">Aktif</span>
                <?php else: ?>
                  <span class="badge badge-secondary">Nonaktif</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($online): ?>
                  <span class="badge badge-info">Online</span>
                <?php else: ?>
                  <span class="badge badge-light">Offline</span>
                <?php endif; ?>
              </td>

              <td>
                <form method="post" action="/dashboard/admin/guru/toggle/<?= $g['id'] ?>" style="display:inline">
                  <button class="btn btn-sm <?= ($g['status']==='aktif')?'btn-danger':'btn-success' ?>">
                    <?= ($g['status']==='aktif')?'Nonaktifkan':'Aktifkan' ?>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>

          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>

@endsection
