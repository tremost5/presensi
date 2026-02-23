@extends('layouts/adminlte')
@section('content')

<section class="content-header">
  <h1>Manajemen Guru</h1>
</section>

<style>
#guruBackdrop{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.35);
  z-index:1049;
  opacity:0;
  pointer-events:none;
  transition:.2s;
}
#guruBackdrop.show{
  opacity:1;
  pointer-events:auto;
}

#guruCard{
  position:fixed;
  top:50%;
  left:50%;
  transform:translate(-50%,-45%) scale(.95);
  z-index:1050;
  width:92%;
  max-width:420px;
  opacity:0;
  pointer-events:none;
  transition:.25s ease;
}
#guruCard.show{
  opacity:1;
  pointer-events:auto;
  transform:translate(-50%,-50%) scale(1);
}
</style>

<section class="content">
<div class="card">
<div class="card-body table-responsive">

<?php $prefixGuru = session('role_id') == 1 ? 'dashboard/superadmin/guru' : 'admin/guru'; ?>
<a href="<?= base_url($prefixGuru . '/create') ?>"
   class="btn btn-primary mb-3">
  ➕ Tambah Guru
</a>

<table class="table table-bordered table-striped">
<thead>
<tr>
  <th>Nama</th>
  <th>Username</th>
  <th>Status</th>
  <th width="180">Aksi</th>
</tr>
</thead>
<tbody>

<?php if (empty($guru)): ?>
<tr>
  <td colspan="4" class="text-center text-muted">
    Belum ada data guru
  </td>
</tr>
<?php endif; ?>

<?php foreach ($guru as $g): ?>
<?php
  $nama  = trim($g['nama_depan'].' '.($g['nama_belakang'] ?? ''));
  $aktif = ($g['status'] === 'aktif');
?>
<tr id="row<?= $g['id'] ?>">
  <td>
    <a href="#"
       class="font-weight-bold text-primary"
       onclick="openGuru(<?= $g['id'] ?>);return false;">
      <?= esc($nama) ?>
    </a>
  </td>

  <td><?= esc($g['username']) ?></td>

  <td>
    <span id="badge<?= $g['id'] ?>"
          class="badge <?= $aktif?'badge-success':'badge-secondary' ?>">
      <?= $aktif?'Aktif':'Nonaktif' ?>
    </span>
  </td>

  <td class="text-nowrap">

    <!-- TOGGLE TANPA RELOAD -->
    <button
    class="btn btn-sm <?= $aktif?'btn-danger':'btn-success' ?>"
  onclick="toggleGuru(<?= $g['id'] ?>, this)">
      <?= $aktif?'Nonaktifkan':'Aktifkan' ?>
    </button>

    <form action="<?= base_url($prefixGuru . '/delete/'.$g['id']) ?>"
          method="post"
          style="display:inline-block"
          onsubmit="return confirm('Yakin ingin menghapus guru ini?')">
      <?= csrf_field() ?>
      <button type="submit" class="btn btn-sm btn-outline-danger">
        Hapus
      </button>
    </form>

  </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>
</div>
</section>

<!-- BACKDROP -->
<div id="guruBackdrop" onclick="closeGuru()"></div>

<!-- CARD PREVIEW (READ ONLY) -->
<div class="card shadow" id="guruCard">
<div class="card-body text-center">

<img id="gFoto"
     class="img-circle mb-3"
     width="90" height="90"
     style="object-fit:cover">

<h5 id="gNama" class="mb-1"></h5>
<div class="text-muted mb-2" id="gUsername"></div>

<hr>

<div class="text-left">
<p><b>Alamat:</b><br><span id="gAlamat">-</span></p>
<p><b>No WhatsApp:</b><br><span id="gHp">-</span></p>
</div>

<button class="btn btn-secondary btn-block" onclick="closeGuru()">
  Tutup
</button>

</div>
</div>

<script>
const card=document.getElementById('guruCard');
const back=document.getElementById('guruBackdrop');
const elNama = document.getElementById('gNama');
const elUsername = document.getElementById('gUsername');
const elAlamat = document.getElementById('gAlamat');
const elHp = document.getElementById('gHp');
const elFoto = document.getElementById('gFoto');

/* OPEN PREVIEW */
function openGuru(id){
fetch(`<?= base_url($prefixGuru . '/detail') ?>/${id}`,{
 headers:{'X-Requested-With':'XMLHttpRequest'}
})
.then(r=>r.json()).then(res=>{
 if(!res || res.status !== 'ok' || !res.data){
   alert('Detail guru tidak ditemukan.');
   return;
 }
 const g=res.data;

 elNama.innerText =
   (g.nama_depan||'')+' '+(g.nama_belakang||'');

 elUsername.innerText = '@'+g.username;
 elAlamat.innerText   = g.alamat || '-';
 elHp.innerText       = g.no_hp || '-';

 const fotoBase = '<?= rtrim(base_url('uploads/guru'), '/') ?>';
 const defaultFoto = '<?= base_url('uploads/guru/default.png') ?>';
 elFoto.src = g.foto
   ? `${fotoBase}/${g.foto}`
   : defaultFoto;
 elFoto.onerror = function () {
   this.onerror = null;
   this.src = defaultFoto;
 };

 back.classList.add('show');
 card.classList.add('show');
}).catch(() => {
 alert('Gagal memuat detail guru.');
});
}

function closeGuru(){
 back.classList.remove('show');
 card.classList.remove('show');
}

/* TOGGLE STATUS – TANPA RELOAD */
function toggleGuru(id, btn){
fetch(`<?= base_url($prefixGuru . '/toggle') ?>/${id}`,{
 headers:{'X-Requested-With':'XMLHttpRequest'}
})
.then(r=>r.json())
.then(res=>{
  const badge=document.getElementById('badge'+id);

  if(res.status==='nonaktif'){
    badge.className='badge badge-secondary';
    badge.innerText='Nonaktif';
    btn.className='btn btn-sm btn-success';
    btn.innerText='Aktifkan';
  }else{
    badge.className='badge badge-success';
    badge.innerText='Aktif';
    btn.className='btn btn-sm btn-danger';
    btn.innerText='Nonaktifkan';
  }
});
}

</script>

@endsection
