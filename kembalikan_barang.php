<?php
session_start();
include 'db.php';

// Pastikan hanya pengguna yang sudah login dapat mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Mendapatkan daftar barang yang dipinjam oleh pengguna
$query_peminjaman = "
    SELECT 
        peminjaman.id AS peminjaman_id, 
        barang.nama_barang, 
        peminjaman.jumlah, 
        DATE(peminjaman.tanggal_pinjam) AS tanggal_pinjam,
        peminjaman.nama_pegawai,
        peminjaman.bidang,
        peminjaman.email_peminjam
    FROM peminjaman
    INNER JOIN barang ON peminjaman.id_barang = barang.id
    WHERE peminjaman.username = ? AND peminjaman.tanggal_kembali IS NULL
";

$stmt = mysqli_prepare($conn, $query_peminjaman);
mysqli_stmt_bind_param($stmt, "s", $username);  // Binding parameter untuk username
mysqli_stmt_execute($stmt);
$result_peminjaman = mysqli_stmt_get_result($stmt);

// Proses pengembalian barang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi dan sanitasi input peminjaman_id
    $peminjaman_id = mysqli_real_escape_string($conn, $_POST['peminjaman_id']); 

    // Update tanggal_kembali di tabel peminjaman
    $query_update = "UPDATE peminjaman SET tanggal_kembali = NOW() WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $query_update);
    mysqli_stmt_bind_param($stmt_update, "i", $peminjaman_id);

    if (mysqli_stmt_execute($stmt_update)) {
        // Mengembalikan stok barang
        $query_barang = "SELECT id_barang, jumlah FROM peminjaman WHERE id = ?";
        $stmt_barang = mysqli_prepare($conn, $query_barang);
        mysqli_stmt_bind_param($stmt_barang, "i", $peminjaman_id);
        mysqli_stmt_execute($stmt_barang);
        $result_barang = mysqli_stmt_get_result($stmt_barang);
        $row_barang = mysqli_fetch_assoc($result_barang);

        $id_barang = $row_barang['id_barang'];
        $jumlah_dikembalikan = $row_barang['jumlah'];

        // Update stok barang
        $query_update_stok = "UPDATE barang SET stok = stok + ? WHERE id = ?";
        $stmt_update_stok = mysqli_prepare($conn, $query_update_stok);
        mysqli_stmt_bind_param($stmt_update_stok, "ii", $jumlah_dikembalikan, $id_barang);
        mysqli_stmt_execute($stmt_update_stok);

        $_SESSION['success_message'] = "Barang berhasil dikembalikan!";
        header("Location: kembalikan_barang.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan: " . mysqli_error($conn);
        header("Location: kembalikan_barang.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kembalikan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #35424a;
            color: white;
        }
        form {
            text-align: center;
            margin: 20px;
        }
        button {
            padding: 10px 20px;
            background: #35424a;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .notification {
            width: 80%;
            margin: 10px auto;
            padding: 10px;
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
    <h2 style="text-align: center;">Daftar Barang yang Dipinjam</h2>

    <!-- Notifikasi -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="notification success">
            <?= $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="notification error">
            <?= $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Nama Pegawai</th>
                <th>Bidang</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result_peminjaman) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_peminjaman)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['jumlah']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pinjam']); ?></td>
                        <td><?= htmlspecialchars($row['nama_pegawai']); ?></td>
                        <td><?= htmlspecialchars($row['bidang']); ?></td>
                        <td><?= htmlspecialchars($row['email_peminjam']); ?></td>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="peminjaman_id" value="<?= htmlspecialchars($row['peminjaman_id']); ?>">
                                <button type="submit">Kembalikan barang</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada barang yang sedang dipinjam</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
