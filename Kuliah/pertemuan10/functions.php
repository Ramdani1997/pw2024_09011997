<?php
// 25-03-2024
function koneksi()
{
  return  mysqli_connect('localhost', 'root', '', 'pw_20240325');
}

function query($query)
{
  $conn = koneksi();

  $result = mysqli_query($conn, $query);

  // Jika Hasil hanya 1 data 

  if (mysqli_num_rows($result) == 1) {
    return mysqli_fetch_assoc($result);
  }

  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }

  return $rows;
}

//26-03-2024

function tambah($data)
{
  $conn = koneksi();

  $nama = htmlspecialchars($data['nama']);
  $nim = htmlspecialchars($data['nim']);
  $email = htmlspecialchars($data['email']);
  $jurusan = htmlspecialchars($data['jurusan']);
  $gambar = htmlspecialchars($data['gambar']);


  $query = "INSERT INTO
              mahasiswa
            VALUES
            (null, '$nama', '$nim', '$email', '$jurusan', '$gambar');
            ";
  mysqli_query($conn, $query);
  echo mysqli_error($conn);
  return mysqli_affected_rows($conn);
}
