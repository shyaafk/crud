<?php
include 'config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dan bersihkan
    $nisn = trim($_POST['nisn']);
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $jurusan = $_POST['jurusan'];

    // Validasi NISN: hanya angka
    if (empty($nisn) || !ctype_digit($nisn)) {
        $errors[] = "NISN harus berupa angka saja.";
    }

    // Validasi Nama: hanya huruf dan spasi
    if (empty($nama) || !preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        $errors[] = "Nama hanya boleh berisi huruf dan spasi.";
    }

    // Validasi Alamat: boleh huruf dan angka (tidak dibatasi)
    if (empty($alamat) || strlen($alamat) < 5) {
        $errors[] = "Alamat tidak boleh kosong dan minimal 5 karakter.";
    }

    // Validasi Jurusan
    $daftarJurusan = ["RPL", "DKV", "TITL", "TSM", "KR"];
    if (!in_array($jurusan, $daftarJurusan)) {
        $errors[] = "Jurusan tidak valid.";
    }

    // --- Perbaikan dimulai di sini ---
    // Cek apakah NISN sudah ada di database
    if (empty($errors)) {
        $checkSql = "SELECT nisn FROM siswa WHERE nisn = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $nisn);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $errors[] = "NISN '$nisn' sudah terdaftar. Gunakan NISN lain.";
        }
        $checkStmt->close();
    }
    // --- Perbaikan selesai di sini ---

    // Simpan jika tidak ada error
    if (empty($errors)) {
        $sql = "INSERT INTO siswa (nisn, nama, alamat, jurusan) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nisn, $nama, $alamat, $jurusan);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            // Ini adalah cara untuk menangani error jika ada, meskipun validasi di atas sudah mencegahnya
            $errors[] = "Gagal menambahkan data: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Tambah Siswa Baru</h2>
        <div class="card p-4 mx-auto shadow" style="max-width: 500px;">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="create.php">
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN:</label>
                    <input type="text" id="nisn" name="nisn" class="form-control" required pattern="[0-9]+" title="Hanya angka" inputmode="numeric" value="<?= isset($nisn) ? htmlspecialchars($nisn) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama:</label>
                    <input type="text" id="nama" name="nama" class="form-control" required pattern="[A-Za-z\s]+" title="Hanya huruf dan spasi" value="<?= isset($nama) ? htmlspecialchars($nama) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat:</label>
                    <textarea id="alamat" name="alamat" class="form-control" required><?= isset($alamat) ? htmlspecialchars($alamat) : '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="jurusan" class="form-label">Jurusan:</label>
                    <select id="jurusan" name="jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php
                        $jurusanList = ["RPL", "DKV", "TITL", "TSM", "KR"];
                        foreach ($jurusanList as $j) {
                            $selected = (isset($jurusan) && $jurusan === $j) ? 'selected' : '';
                            echo "<option value=\"$j\" $selected>$j</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Tambah</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>