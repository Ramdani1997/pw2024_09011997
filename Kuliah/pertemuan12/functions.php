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
  mysqli_query($conn, $query) or die(mysqli_error($conn));
  echo mysqli_error($conn);
  return mysqli_affected_rows($conn);
}

function hapus($id)
{
  $conn = koneksi();
  mysqli_query($conn, "DELETE FROM mahasiswa WHERE id = $id") or die(mysqli_error($conn));
  return mysqli_affected_rows($conn);
}

function ubah($data)
{
  $conn = koneksi();

  $id = $data['id'];

  $nama = htmlspecialchars($data['nama']);
  $nim = htmlspecialchars($data['nim']);
  $email = htmlspecialchars($data['email']);
  $jurusan = htmlspecialchars($data['jurusan']);
  $gambar = htmlspecialchars($data['gambar']);


  $query = "UPDATE mahasiswa SET 
              nama = '$nama',
              nim  = '$nim',
              email = '$email',
              jurusan = '$jurusan',
              gambar = '$gambar'
            where id = $id";
  mysqli_query($conn, $query) or die(mysqli_error($conn));
  echo mysqli_error($conn);
  return mysqli_affected_rows($conn);
}

function cari($keyword)
{
  $conn = koneksi();

  $query = "SELECT * FROM mahasiswa
              WHERE 
            nama LIKE '%$keyword%' OR
            nim LIKE '%$keyword%'";
  $result = mysqli_query($conn, $query);

  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }

  return $rows;
}

function login($data)
{
  $conn = koneksi();

  $username = htmlspecialchars($data['username']);
  $password = htmlspecialchars($data['password']);


  //cek username
  if ($user = query("SELECT * FROM user WHERE username = '$username' ")) {
    // cek password
    if (password_verify($password, $user['password'])) {
      // set session
      $_SESSION['login'] = true;

      header("Location: index.php");
      exit;
    }
  }
  return [
    'error' => true,
    'pesan' => 'Username / Password Salah!'
  ];
}

function registrasi($data)
{
  $conn = koneksi();

  $username = htmlspecialchars(strtolower($data['username']));
  $password1 = mysqli_real_escape_string($conn, $data['password1']);
  $password2 = mysqli_real_escape_string($conn, $data['password2']);


  //Jika username atau password kosong
  if (empty($username) || empty($password1) || empty($password2)) {
    echo "<script>
          alert('Username / Password tidak boleh kosong!');
          document.location.href = 'registrasi.php';
        </script>";
    return false;
  }

  // Jika username sudah ada di database

  if (query("SELECT * FROM user WHERE username = '$username'")) {
    echo "<script>
    alert('Username / Password tidak boleh sama!');
    document.location.href = 'registrasi.php';
  </script>";
    return false;
  }

  //JIKA PASSWORD TIDAK SESUAI

  if ($password1 !== $password2) {
    echo "<script>
    alert('Konfirmasi Password Tidak Sesuai!');
    document.location.href = 'registrasi.php';
  </script>";
    return false;
  }

  if (strlen($password1) < 5) {
    echo "<script>
    alert('Password Terlalu Pendek!');
    document.location.href = 'registrasi.php';
  </script>";
    return false;
  }

  //Jika username dan password sudah sesuai
  //enkripsi password

  $password_baru = password_hash($password1, PASSWORD_DEFAULT);
  //insert ke table user

  $query = "INSERT INTO user
              VALUES
            (null, '$username', '$password_baru')
            ";
  mysqli_query($conn, $query) or die(mysqli_error($conn));
  return mysqli_affected_rows($conn);
}
