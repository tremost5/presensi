@extends('layouts/adminlte')
@section('content')

<div class="card mb-3">
  <div class="card-header">
    <h5 class="mb-0">WA Template</h5>
  </div>
  <div class="card-body">
    <p class="text-muted mb-3">Atur template pesan WhatsApp dan pilih user admin/superadmin penerima notifikasi.</p>

    <form method="post" action="<?= base_url('superadmin/wa-template/save') ?>">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="register_admin"><strong>Template Pendaftaran Berhasil (ke Admin/Superadmin)</strong></label>
        <textarea class="form-control" id="register_admin" name="register_admin" rows="4" required><?= esc($templates['register_admin'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="register_user"><strong>Template Pendaftaran Berhasil (ke Guru Pendaftar)</strong></label>
        <textarea class="form-control" id="register_user" name="register_user" rows="4" required><?= esc($templates['register_user'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="guru_status_active"><strong>Template Status Guru Aktif</strong></label>
        <textarea class="form-control" id="guru_status_active" name="guru_status_active" rows="4" required><?= esc($templates['guru_status_active'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label for="guru_status_inactive"><strong>Template Status Guru Nonaktif</strong></label>
        <textarea class="form-control" id="guru_status_inactive" name="guru_status_inactive" rows="4" required><?= esc($templates['guru_status_inactive'] ?? '') ?></textarea>
      </div>

      <div class="alert alert-light border">
        <strong>Placeholder yang bisa dipakai:</strong>
        <div class="mt-2">
          <?php foreach (($placeholders ?? []) as $ph): ?>
            <span class="badge badge-secondary mr-1 mb-1"><?= esc($ph) ?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <h6 class="mt-4">Penerima Notifikasi WA (Role Superadmin/Admin)</h6>
      <div class="table-responsive">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th width="40">Pilih</th>
              <th>Nama</th>
              <th width="100">Role</th>
              <th width="180">No WA</th>
              <th width="120">Status User</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada user role superadmin/admin.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <?php
                  $uid = (int) ($u['id'] ?? 0);
                  $isChecked = !empty($recipientMap[$uid]);
                  $fullName = trim(($u['nama_depan'] ?? '') . ' ' . ($u['nama_belakang'] ?? ''));
                  $roleLabel = ((int) ($u['role_id'] ?? 0) === 1) ? 'Superadmin' : 'Admin';
                ?>
                <tr>
                  <td class="text-center">
                    <input type="checkbox" name="recipient_ids[]" value="<?= $uid ?>" <?= $isChecked ? 'checked' : '' ?>>
                  </td>
                  <td><?= esc($fullName !== '' ? $fullName : '-') ?></td>
                  <td><?= esc($roleLabel) ?></td>
                  <td><?= esc($u['no_hp'] ?? '-') ?></td>
                  <td>
                    <?php if (($u['status'] ?? '') === 'aktif'): ?>
                      <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge badge-secondary">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <button type="submit" class="btn btn-primary">Simpan Template & Penerima</button>
    </form>
  </div>
</div>

@endsection
