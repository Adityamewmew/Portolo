<?php
require_once "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data dari form
    $nama = $_POST["nama"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email tidak valid.";
        exit(); // Berhenti eksekusi script
    }

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk menyimpan data ke database
    $query = "INSERT INTO user (nama, email, password) VALUES ('$nama', '$email', '$hashed_password')";
    if (mysqli_query($conn, $query)) {
        header("Location:beranda/beranda.html");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Signur</title>
    <link rel="stylesheet" href="css/style1.css" />
</head>
<body>
    <section class="wrapper">
        <div class="form signup">
            <header>Signup</header>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" name="nama" placeholder="Nama lengkap" required />
                <input type="email" name="email" placeholder="Email lengkap" required />
                <input type="password" name="password" placeholder="Kata sandi" required />
                <div class="checkbox">
                    <input type="checkbox" id="signupCheck" required />
                    <label for="signupCheck">Saya Menerima Syarat Dan Ketentuan</label>
                </div>
                <input type="submit" name="signup" value="Signup" />
            </form>
        </div>
    </section>
</body>
</html>
