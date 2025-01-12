<?php
session_start();
include 'db.php'; 

// Pastikan hanya pengguna yang sudah login dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Mendapatkan daftar barang untuk dipinjam
$query_barang = "SELECT * FROM barang WHERE stok > 0";
$result_barang = mysqli_query($conn, $query_barang);

// Proses peminjaman barang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $nama_pegawai = $_POST['nama_pegawai'];
    $bidang = $_POST['Bidang'];
    $email = $_POST['email_peminjam']; // Input email baru
    $tanggal_pinjam = date('Y-m-d');

    // Pastikan jumlah barang yang dipinjam tersedia
    $query_stok = "SELECT stok FROM barang WHERE id = $id_barang";
    $result_stok = mysqli_query($conn, $query_stok);
    $row_stok = mysqli_fetch_assoc($result_stok);

    if ($row_stok['stok'] >= $jumlah) {
        // Insert data peminjaman
        $query_insert = "INSERT INTO peminjaman (username, id_barang, jumlah, tanggal_pinjam, nama_pegawai, bidang, email_peminjam) 
                         VALUES ('$username', '$id_barang', '$jumlah', '$tanggal_pinjam', '$nama_pegawai', '$bidang', '$email_peminjam')";
        if (mysqli_query($conn, $query_insert)) {
            // Update stok barang setelah dipinjam
            $new_stok = $row_stok['stok'] - $jumlah;
            $query_update_stok = "UPDATE barang SET stok = $new_stok WHERE id = $id_barang";
            mysqli_query($conn, $query_update_stok);

            // Menampilkan pesan berhasil
            echo "<script>alert('Data peminjaman barang berhasil disimpan.'); window.location.href='index.php';</script>";
            exit;
        } else {
            echo "Gagal menyimpan data peminjaman barang.";
        }
    } else {
        $_SESSION['error_message'] = "Stok tidak mencukupi untuk jumlah yang dipinjam.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pinjam Barang</title>
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
        input, select, button {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background: #35424a;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #2c3e50;
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

    <!-- Notifikasi jika ada -->
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
        <h3>Pinjam Barang</h3>

        <!-- Dropdown untuk memilih barang -->
        <select name="id_barang" required>
            <option value="">Pilih Barang</option>
            <?php while ($row = mysqli_fetch_assoc($result_barang)): ?>
                <option value="<?= $row['id']; ?>"><?= $row['nama_barang']; ?> (Stok: <?= $row['stok']; ?>)</option>
            <?php endwhile; ?>
        </select>

        <!-- Input untuk jumlah barang -->
        <input type="number" name="jumlah" placeholder="Jumlah Barang yang dipinjam" required min="1">

        <!-- Input untuk nama pegawai -->
        <input type="text" name="nama_pegawai" placeholder="Nama Pegawai" required>

        <!-- Input untuk email -->
        <input type="email_peminjam" name="email_peminjam" placeholder="Email Peminjam" required>

        <!-- Dropdown untuk bidang -->
        <div class="form-group">
            <label for="Bidang">Bidang</label>
            <select name="Bidang" id="Bidang" required>
                <option value="">--Pilih Bidang--</option>
                <option value="IT">IT</option>
                <option value="Data">Data</option>
                <option value="Umum">Umum</option>
            </select>
        </div>

        <button type="submit">Pinjam</button>
    </form>

</body>
</html>
