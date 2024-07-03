<?php
// Proses Formulir Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Simpan data dari formulir
    $username = $_POST['login'];
    $password = $_POST['password'];

    // Contoh validasi sederhana (ganti dengan validasi sesuai kebutuhan)
    if ($username === 'user' && $password === 'password') {
        // Jika login sukses, redirect ke halaman beranda
        header('Location:beranda/beranda.html'); // Sesuaikan dengan path halaman beranda
        exit; // Pastikan untuk keluar dari script setelah melakukan redirect
    } else {
        // Jika login gagal, tampilkan pesan error
        echo "<p>Login failed. Please check your username and password.</p>";
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="css/style4.css" />
</head>
<body>
<div class="wrapper fadeInDown">
  <div id="formContent">
    <!-- Tabs Titles -->
    <h2 class="active"> Sign In </h2>
  

    <!-- Icon -->
    <div class="fadeIn first">
      <img src="https://i.imgur.com/8RKXAIV.jpg" id="icon" alt="User Icon" />
    </div>

    <!-- Login Form -->
    <form action="beranda/beranda.html" method="POST">
      <input type="text" id="login" class="fadeIn second" name="login" placeholder="login">
      <input type="text" id="password" class="fadeIn third" name="login" placeholder="password">
      <input type="submit" class="fadeIn fourth" value="Log In">
    </form>

    <!-- Remind Passowrd -->
    <div id="formFooter">
      <a class="underlineHover" href="#">Forgot Password?</a>
    </div>

  </div>
</div>



</body>
</html>
</body>
</html>