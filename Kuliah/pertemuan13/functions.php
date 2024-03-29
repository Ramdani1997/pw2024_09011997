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

function upload()
{
  $nama_file = $_FILES['gambar']['name'];
  $tipe_file = $_FILES['gambar']['type'];
  $ukuran_file = $_FILES['gambar']['size'];
  $error = $_FILES['gambar']['error'];
  $tmp_file = $_FILES['gambar']['tmp_name'];

  //ketika tidak ada gambar yang dipilih
  if ($error == 4) {
    // echo "<script>
    //         alert('Pilih Gambar Terlebih Dahulu!')
    //       </script>";
    return 'test.png';
  }

  //cek ektensi file 
  $daftar_gambar = ['jpg', 'jpeg', 'png'];
  $ektensi_file = explode('.', $nama_file);
  $ektensi_file = strtolower(end($ektensi_file));

  if (!in_array($ektensi_file, $daftar_gambar)) {
    echo "<script>
            alert('File yang anda pilih bukan gambar !')
          </script>";
    return false;
  }

  // cek type file
  if ($tipe_file != 'image/jpeg' && $tipe_file != 'image/png') {
    echo "<script>
            alert('File yang anda pilih bukan gambar !')
          </script>";
    return false;
  }

  //CEK UKURAN FILE
  // max 5 mb = 5000000byte

  if ($ukuran_file > 5000000) {
    echo "<script>
            alert('File yang anda pilih bukan gambar !');
          </script>";
    return false;
  }

  //lolos Pengecekan
  // siap upload file
  //generate nama file baru

  $nama_file_baru = uniqid();
  $nama_file_baru .= '.';
  $nama_file_baru .= $ektensi_file;

  move_uploaded_file($tmp_file, 'img/' . $nama_file_baru);

  return $nama_file_baru;
}

//26-03-2024

function tambah($data)
{
  $conn = koneksi();

  $nama = htmlspecialchars($data['nama']);
  $nim = htmlspecialchars($data['nim']);
  $email = htmlspecialchars($data['email']);
  $jurusan = htmlspecialchars($data['jurusan']);
  //$gambar = htmlspecialchars($data['gambar']);

  //uplaod gambar

  $gambar = upload();
  if (!$gambar) {
    return false;
  }


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
  // mneghapus gambar difolder
  $mhs = query("SELECT * FROM mahasiswa where id = $id");
  if ($mhs['gambar'] != 'test.png') {
    unlink('img/' . $mhs['gambar']);
  }

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
  $gambar_lama = htmlspecialchars($data['gambar_lama']);

  $gambar = upload();
  if (!$gambar) {
    return false;
  }

  if ($gambar == 'test.png') {
    $gambar = $gambar_lama;
  }


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
