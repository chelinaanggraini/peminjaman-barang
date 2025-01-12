<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Pastikan PHPMailer sudah diinstal

include 'db.php';

// Fungsi untuk mengirim email pengingat
function sendReminderEmail($email, $namaBarang, $tanggalPinjam, $tanggalKembali) {
    $mail = new PHPMailer(true);
    try {
        // Pengaturan SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Ganti dengan server SMTP yang Anda pakai
        $mail->SMTPAuth = true;
        $mail->Username = 'youremail@example.com'; // Ganti dengan alamat email Anda
        $mail->Password = 'yourpassword'; // Ganti dengan password atau app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 3306;

        // Pengaturan penerima dan pengirim
        $mail->setFrom('youremail@example.com', 'Admin Peminjaman');
        $mail->addAddress($email);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = 'Pengingat Peminjaman Barang';
        $mail->Body = "<h3>Pengingat Peminjaman Barang</h3>
                        <p>Barang: <strong>$namaBarang</strong></p>
                        <p>Tanggal Pinjam: <strong>$tanggalPinjam</strong></p>
                        <p>Harap segera mengembalikan barang sebelum tanggal: <strong>$tanggalKembali</strong></p>
                        <p>Terima kasih.</p>";

        $mail->send();
        echo "Email berhasil dikirim ke $email";
    } catch (Exception $e) {
        echo "Email gagal dikirim. Kesalahan: {$mail->ErrorInfo}";
    }
}

// Query untuk mendapatkan peminjaman yang mendekati batas waktu
$tanggalSekarang = date('Y-m-d');
$tanggalPeringatan = date('Y-m-d', strtotime('+1 day')); // Mengirim pengingat 1 hari sebelum batas waktu

$query = "SELECT p.*, b.nama_barang, u.email 
          FROM peminjaman p
          JOIN barang b ON p.id_barang = b.id
          JOIN users u ON p.username = u.username
          WHERE p.tanggal_kembali IS NULL AND p.tanggal_pinjam <= '$tanggalPeringatan'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // Kirim email untuk setiap peminjaman yang ditemukan
    while ($row = mysqli_fetch_assoc($result)) {
        $email = $row['email'];
        $namaBarang = $row['nama_barang'];
        $tanggalPinjam = $row['tanggal_pinjam'];
        $tanggalKembali = $row['tanggal_kembali'];

        sendReminderEmail($email, $namaBarang, $tanggalPinjam, $tanggalKembali); // Kirim email pengingat
    }
} else {
    echo "Tidak ada barang yang memerlukan pengingat saat ini.";
}
?>
