<h3>Preview Import Murid</h3>

<form method="post" action="/admin/murid/import-execute">
<table border="1" cellpadding="5" cellspacing="0">
<thead>
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Kelas</th>
    <th>JK</th>
    <th>Telp Ortu</th>
</tr>
</thead>
<tbody>

<?php foreach ($data as $i => $d): ?>
<tr>
    <td><?= $i+1 ?></td>
    <td><?= esc($d['nama_depan'].' '.$d['nama_belakang']) ?></td>
    <td><?= esc($d['kelas_id']) ?></td>
    <td><?= esc($d['jenis_kelamin']) ?></td>
    <td><?= esc($d['no_telp_ortu']) ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

<br>
<button class="btn btn-success">
    ✅ Konfirmasi Import
</button>
<a href="/admin/murid/import" class="btn btn-secondary">
    ❌ Batal
</a>
</form>
