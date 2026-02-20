<?php
session_start();
require_once '../config/database.php';
require_once '../lib/functions.php';

requireRoleAccess(['admin']); 

$pesan = "";
if (isset($_POST['do_backup'])) {
    $nama_file_input = $_POST['nama_identitas'] ?: 'backup_perpus'; // Default jika kosong
    $hasil = backupDatabasePerpus($nama_file_input);
    if ($hasil['success']) {
        $pesan = "<div class='alert alert-success'>✅ Berhasil! File disimpan di folder 'backups' dan 'mysql/bin'. <br> Nama: " . htmlspecialchars($hasil['file']) . "</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>❌ Gagal: " . htmlspecialchars($hasil['error']) . "</div>";
    }
}

// Ambil daftar file backup
$dir = "../backups/";
$files = is_dir($dir) ? array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), array('.', '..')) : [];

include '../views/'.$THEME.'/header.php'; // Sesuaikan folder tema kamu
include '../views/'.$THEME.'/topnav.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../views/'.$THEME.'/sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">🛡️ Backup Database</h2>
                    <?= $pesan ?>
                    
                    
                    <div class="bg-light p-4 rounded-3 mb-5">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Identitas File Backup:</label>
                                <input type="text" name="nama_identitas" class="form-control" placeholder="masukkan nama file backup" style="max-width: 500px;">
                                
                            </div>
                            <button type="submit" name="do_backup" class="btn btn-dark px-4 py-2">
                                🚀 Mulai Backup database
                            </button>
                        </form>
                    </div>

                    <h4 class="mb-3">📂 Riwayat File Cadangan </h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nama File SQL</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($files as $file): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($file) ?></code></td>
                                    <td class="text-center">
                                        <a href="../backups/<?= $file ?>" class="btn btn-sm btn-success" download>📥 Download</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($files)): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">Belum ada data backup yang tersedia.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../views/'.$THEME.'/footer.php'; ?>