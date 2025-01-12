<?php
session_start();
include 'db.php';

$error_message = "";

// Menangani form login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Menggunakan prepared statement untuk keamanan
    $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    // Jika username ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password terenkripsi
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;  // Simpan username di session
            header("Location: index.php");      // Redirect ke dashboard
            exit;
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        form {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        input {
            width: 70%;
            padding: 10px;
            margin: 10px 0;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #35424a;
            color: #fff;
            border: none;
            width: 70%;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #2c383e;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .show-password {
            cursor: pointer;
            background: none;
            border: none;
            color: #35424a;
            font-size: 14px;
            text-decoration: underline;
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
    <form method="POST">
        <h3>Login</h3>
        
        <!-- Tampilkan pesan error jika ada -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Input untuk username -->
        <input type="text" name="username" placeholder="Username" required>

        <!-- Input untuk password -->
        <input type="password" id="password" name="password" placeholder="Password" required>
        <button type="button" class="show-password" onclick="togglePassword()">Tampilkan Password</button>

        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>

        <button type="submit">Login</button>
    </form>

    <script>
        // Fungsi untuk toggle tampilan password
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            var button = document.querySelector('.show-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                button.textContent = 'Sembunyikan Password';
            } else {
                passwordInput.type = 'password';
                button.textContent = 'Tampilkan Password';
            }
        }
    </script>

</body>
</html>
