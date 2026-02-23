<h3>Import Murid (Excel)</h3>

<form method="post" enctype="multipart/form-data"
      action="/admin/murid/import-preview">

    <input type="file" name="file_excel"
           accept=".xls,.xlsx" required>

    <br><br>
    <button class="btn btn-primary">
        Preview Data
    </button>
</form>

<?php if (session()->getFlashdata('success')): ?>
    <p style="color:green"><?= session('success') ?></p>
<?php endif; ?>
