<?php
include 'config.php';

$errors = [];
$nisn = "";
$nama = "";
$alamat = "";
$jurusan = "";
$id_to_update = isset($_GET['id']) ? $_GET['id'] : "";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Validasi ID harus angka
    if (!ctype_digit($id_to_update)) {
        echo "ID tidak valid.";
        exit();
    }

    $sql = "SELECT id, nisn, nama, alamat, jurusan FROM siswa WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_to_update);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $nisn = $row['nisn'];
        $nama = $row['nama'];
        $alamat = $row['alamat'];
        $jurusan = $row['jurusan'];
    } else {
        echo "Data siswa tidak ditemukan.";
        exit();
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ambil data POST
    $id_to_update = $_POST['id_to_update'];
    $nisn = trim($_POST['nisn']);
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $jurusan = $_POST['jurusan'];

    // Validasi
    if (empty($nisn) || !ctype_digit($nisn)) {
        $errors[] = "NISN harus berupa angka saja.";
    }

    if (empty($nama) || !preg_match("/^[a-zA-Z\s]+$/", $nama)) {
        $errors[] = "Nama hanya boleh berisi huruf dan spasi.";
    }

    if (empty($alamat) || strlen($alamat) < 5) {
        $errors[] = "Alamat tidak boleh kosong dan minimal 5 karakter.";
    }

    $daftarJurusan = ["RPL", "DKV", "TITL", "TSM", "KR"];
    if (!in_array($jurusan, $daftarJurusan)) {
        $errors[] = "Jurusan tidak valid.";
    }

    if (empty($errors)) {
        $sql = "UPDATE siswa SET nisn=?, nama=?, alamat=?, jurusan=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nisn, $nama, $alamat, $jurusan, $id_to_update);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Siswa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Data Siswa</h2>
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

            <form method="post" action="update.php">
                <input type="hidden" name="id_to_update" value="<?= htmlspecialchars($id_to_update) ?>">
                
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN:</label>
                    <input type="text" id="nisn" name="nisn" class="form-control" required pattern="[0-9]+" title="Hanya angka" inputmode="numeric" value="<?= htmlspecialchars($nisn) ?>">
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama:</label>
                    <input type="text" id="nama" name="nama" class="form-control" required pattern="[A-Za-z\s]+" title="Hanya huruf dan spasi" value="<?= htmlspecialchars($nama) ?>">
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat:</label>
                    <textarea id="alamat" name="alamat" class="form-control" required><?= htmlspecialchars($alamat) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="jurusan" class="form-label">Jurusan:</label>
                    <select id="jurusan" name="jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php
                        $jurusanList = ["RPL", "DKV", "TITL", "TSM", "KR"];
                        foreach ($jurusanList as $j) {
                            $selected = ($jurusan == $j) ? 'selected' : '';
                            echo "<option value=\"$j\" $selected>$j</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
