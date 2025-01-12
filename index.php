<?php
session_start();
include 'db.php';

// Pastikan hanya pengguna yang sudah login dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Query untuk menghitung total peminjam barang yang belum mengembalikan
$query_total_peminjam = "
    SELECT COUNT(DISTINCT peminjaman.email_peminjam) AS total_peminjam
    FROM peminjaman
    WHERE peminjaman.tanggal_kembali IS NULL
";
$result_total_peminjam = mysqli_query($conn, $query_total_peminjam);

// Ambil hasil query
$total_peminjam = 0;
if ($result_total_peminjam) {
    $row = mysqli_fetch_assoc($result_total_peminjam);
    $total_peminjam = $row['total_peminjam'];
}

// Query untuk menghitung total peminjam barang yang sudah mengembalikan
$query_total_kembalian = "
    SELECT COUNT(DISTINCT peminjaman.email_peminjam) AS total_kembalian
    FROM peminjaman
    WHERE peminjaman.tanggal_kembali IS NOT NULL
";
$result_total_kembalian = mysqli_query($conn, $query_total_kembalian);

// Ambil hasil query pengembalian
$total_kembalian = 0;
if ($result_total_kembalian) {
    $row_kembalian = mysqli_fetch_assoc($result_total_kembalian);
    $total_kembalian = $row_kembalian['total_kembalian'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penyimpanan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            background-color: #35424a;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h1 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            margin: 10px 15px;
            border: 1px solid white;
            border-radius: 5px;
            text-align: center;
            background-color: transparent;
        }

        .sidebar a:hover {
            background-color: #2c3e50;
            color: #f4f4f4;
        }

        .container {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #ffffff;
            padding: 50px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            text-align: center;
        }

        h1, h2 {
            color: #35424a;
        }

        h2 {
            font-size: 30px;
        }

        p {
            font-size: 18px;
        }

        .total-box {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .total-box span {
            font-weight: bold;
            color: #35424a;
            font-size: 20px;
        }

        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #35424a;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h1>Dashboard</h1>
        <a href="tambah_barang.php">Tambah Barang</a>
        <a href="pinjam_barang.php">Pinjam Barang</a>
        <a href="kembalikan_barang.php">Kembalikan Barang</a>
        <a href="history_peminjaman.php">History Peminjaman</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main content -->
    <div class="container">
        <div class="card">
            <h2>Selamat Datang di Aplikasi Peminjaman Barang</h2>
            <p>Gunakan menu di samping untuk mengelola barang.</p>

            <div class="total-box">
                <p>Total Peminjam Barang: <span><?= $total_peminjam; ?></span></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Manajemen Peminjaman Barang - Hak Cipta Dilindungi Undang-Undang</p>
    </footer>

</body>
</html>
