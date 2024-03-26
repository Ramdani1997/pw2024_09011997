<?php
require 'functions.php';

// ambil id dari url

$id = $_GET['id'];

//query mahasiswa by id
$m = query("select * from mahasiswa where id = $id");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Mahasiswa</title>
</head>

<body>
  <h3>Detail Mahasiswa</h3>
  <ul>
    <li><img src="img/<?= $m['gambar']; ?>"></li>
    <li>NIM :<?= $m['nim']; ?></li>
    <li>Nama : <?= $m['nama']; ?></li>
    <li>Email : <?= $m['email']; ?></li>
    <li>Jurusan : <?= $m['jurusan']; ?></li>
    <li><a href="ubah.php?id=<?= $m['id']; ?>">Ubah</a> | <a href="hapus.php?id=<?= $m['id']; ?>" onclick="return confirm('Apakah Anda Yakin Akan Mengahpus Data ini?');">Hapus</a></li>
    <li><a href="index.php">Kembali ke Daftar Mahasiswa</a></li>
  </ul>
</body>

</html>