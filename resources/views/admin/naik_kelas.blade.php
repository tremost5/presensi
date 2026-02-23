<h3>Preview Kelas</h3>

<table border="1" cellpadding="6">
<tr>
  <th>Kelas</th>
  <th>Jumlah</th>
</tr>

<?php if (empty($data)): ?>
<tr>
  <td colspan="2" align="center">Tidak ada data</td>
</tr>
<?php endif; ?>

<?php foreach ($data as $d): ?>
<tr>
  <td><?= esc($d['kode_kelas']) ?></td>
  <td><?= esc($d['total']) ?></td>
</tr>
<?php endforeach; ?>
</table>

action="<?= base_url('admin/naik-kelas/execute') ?>"
      onsubmit="return confirm('YAKIN? Ini akan mengubah SEMUA murid!')">

  <button type="submit" name="mode" value="naik">
    ⬆️ NAIK KELAS
  </button>

  <button type="submit" name="mode" value="mundur">
    ⬇️ MUNDUR KELAS
  </button>

</form>
