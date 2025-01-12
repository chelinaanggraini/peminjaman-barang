<?php
session_start();
include 'db.php';

// Pastikan hanya pengguna yang sudah login dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Menangani form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $stok = $_POST['stok'];

    $query = "INSERT INTO barang (nama_barang, stok) VALUES ('$nama_barang', '$stok')";
    if (mysqli_query($conn, $query)) {
        // Menampilkan pesan berhasil
        echo "<script>alert('Data tambah barang berhasil disimpan.'); window.location.href='index.php';</script>";
        exit; // Pastikan untuk menghentikan eksekusi skrip setelah pengalihan
    } else {
        echo "Gagal menyimpan data peminjaman buku.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
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
        }
        input, button {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            background: #35424a;
            color: #fff;
            border: none;
        }
        .notification {
            padding: 10px;
            margin: 20px auto;
            width: 90%;
            max-width: 400px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Menampilkan pesan sukses atau error jika ada -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="notification success" id="notification">
            <?= $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="notification error" id="notification">
            <?= $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="POST">
        <h3>Tambah Barang</h3>
        <input type="text" name="nama_barang" placeholder="Nama Barang" required>
        <input type="number" name="stok" placeholder="Stok Barang" required>
        <button type="submit">Tambah</button>
    </form>

</body>
</html>
