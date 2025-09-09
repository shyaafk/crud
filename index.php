<?php
include 'config.php';

$sql = "SELECT id, nisn, nama, alamat, jurusan FROM siswa";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>CRUD Data Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Daftar Siswa</h2>
        <a href="create.php" class="btn btn-success mb-3">Tambah Siswa Baru</a>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr class="table-dark">
                        <th>ID</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["nisn"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["nama"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["alamat"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["jurusan"]) . "</td>";
                            echo "<td>";
                            echo "<a href='update.php?id=" . htmlspecialchars($row["id"]) . "' class='btn btn-primary btn-sm me-2'>Edit</a>";
                            echo "<a href='delete.php?id=" . htmlspecialchars($row["id"]) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?');\">Hapus</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted'>Tidak ada data siswa ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>