@extends('layouts/adminlte')
@section('content')

<h4>Log Aktivitas Admin & Guru</h4>

<form class="row mb-3">
  <div class="col-md-3">
    <input type="date" name="start" value="<?= esc($start) ?>" class="form-control">
  </div>
  <div class="col-md-3">
    <input type="date" name="end" value="<?= esc($end) ?>" class="form-control">
  </div>
  <div class="col-md-3">
    <select name="role" class="form-control">
      <option value="all" <?= $role === 'all' ? 'selected' : '' ?>>Admin + Guru</option>
      <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
      <option value="guru" <?= $role === 'guru' ? 'selected' : '' ?>>Guru</option>
    </select>
  </div>
  <div class="col-md-3">
    <button class="btn btn-primary w-100">Filter</button>
  </div>
</form>

<?php if (empty($logs)): ?>
  <div class="alert alert-info">Tidak ada log pada filter ini.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-sm table-bordered">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>User</th>
          <th>Role</th>
          <th>Aksi</th>
          <th>Severity</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
          <tr>
            <td><?= esc($l['created_at'] ?? '-') ?></td>
            <td><?= esc(trim(($l['nama_depan'] ?? '').' '.($l['nama_belakang'] ?? ''))) ?></td>
            <td><?= (int) ($l['role_id'] ?? 0) === 2 ? 'Admin' : 'Guru' ?></td>
            <td><?= esc($l['action'] ?? '-') ?></td>
            <td><?= esc($l['severity'] ?? '-') ?></td>
            <td><?= esc($l['ip_address'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

@endsection
