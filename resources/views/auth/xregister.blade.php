<h2>Daftar Guru</h2>

<form method="post" action="/register" enctype="multipart/form-data">
    <input name="nama_depan" placeholder="Nama Depan" required>
    <input name="nama_belakang" placeholder="Nama Belakang" required>
    <input name="username" placeholder="Username" required>
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <input name="phone" placeholder="No HP" required>
    <input name="alamat" placeholder="Alamat" required>
    <input name="tanggal_lahir" type="date" required>

    <input type="file" name="foto" accept="image/*" capture="camera" required>

    <button type="submit">Daftar</button>
</form>
