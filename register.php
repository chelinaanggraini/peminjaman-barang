<?php
session_start();
include 'db.php';

if (isset($_POST['register'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $checkUser = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        echo '<script>alert("Username sudah terdaftar, gunakan username lain.");</script>';
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if (mysqli_query($conn, $sql)) {
            echo '<script>alert("Pendaftaran berhasil! Silakan login."); window.location="login.php";</script>';
        } else {
            echo '<script>alert("Pendaftaran gagal, silakan coba lagi.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .register-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #35424a;
            margin-bottom: 25px;
        }
        input[type="text"],
        input[type="password"] {
            width: 70%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        button {
            background: #35424a;
            color: white;
            padding: 10px;
            width: 70%;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #2c383e;
        }
        p {
            margin-top: 20px;
            font-size: 14px;
        }
        a {
            color: #4d9b8c;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>
