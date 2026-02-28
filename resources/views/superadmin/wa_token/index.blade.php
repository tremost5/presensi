@extends('layouts/adminlte')
@section('content')

<div class="card mb-3">
  <div class="card-header">
    <h5 class="mb-0">Token Fonnte</h5>
  </div>
  <div class="card-body">
    <p class="text-muted mb-3">Gunakan menu ini untuk mengganti token API Fonnte tanpa edit file <code>.env</code>.</p>

    <?php
      $token = (string) ($effectiveToken ?? '');
      $masked = $token === ''
        ? '-'
        : (strlen($token) <= 8 ? str_repeat('*', strlen($token)) : substr($token, 0, 4) . str_repeat('*', max(strlen($token) - 8, 4)) . substr($token, -4));
      $sourceLabel = (($source ?? 'env') === 'database') ? 'Database (system_settings)' : 'ENV (.env)';
    ?>

    <div class="alert alert-light border">
      <div><strong>Token aktif:</strong> <code><?= esc($masked) ?></code></div>
      <div class="small text-muted">Sumber saat ini: <?= esc($sourceLabel) ?></div>
    </div>

    <form method="post" action="<?= base_url('superadmin/wa-token/save') ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="fonnte_token"><strong>Token Fonnte Baru</strong></label>
        <input
          id="fonnte_token"
          name="fonnte_token"
          type="text"
          class="form-control"
          autocomplete="off"
          placeholder="Contoh: AzGQFoWt4AtBygkh5yCe"
          required
        >
      </div>

      <button type="submit" class="btn btn-primary">Simpan Token</button>
      <a href="<?= base_url('superadmin/wa-template') ?>" class="btn btn-success ml-1">
        <i class="fab fa-whatsapp mr-1"></i> Buka WA Template
      </a>
    </form>
  </div>
</div>

@endsection
