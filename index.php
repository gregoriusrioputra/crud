<?php
// Mulai session untuk menyimpan data tugas
session_start();

// Handle POST request (untuk menambah atau mengedit tugas)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data tugas dari form
    $tugasBaru = trim($_POST['tugas']);
    $waktuBaru = trim($_POST['waktu']); // Menginput waktu
  
    if (!empty($tugasBaru)  && !empty($waktuBaru)) {
        $dataBaru = [
            'nama' => $tugasBaru,
            'waktu' => $waktuBaru
        ];
        // Cek apakah sedang dalam mode edit
        if (isset($_SESSION['edit_index'])) {
            $index = $_SESSION['edit_index'];  // Perbaikan: Menggunakan $_SESSION['edit_index']
            $_SESSION['tugas'][$index] = $dataBaru; // Update tugas yang ada
            unset($_SESSION['edit_index']); // Keluar dari mode edit
        } else {
            // Jika tidak dalam mode edit, tambahkan tugas baru
            if (isset($_SESSION['tugas'])) {
                array_push($_SESSION['tugas'], $dataBaru);
            } else {
                $_SESSION['tugas'] = [$dataBaru]; // Perbaikan: Menyimpan data array tugas baru
            }
        }
    }
    // Redirect untuk menghindari pengiriman form ganda
    header('Location: ' . $_SERVER['SCRIPT_NAME']);
    exit();
}

// Handle GET request (untuk menghapus tugas atau semua tugas)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $method = isset($_GET['method']) ? $_GET['method'] : false;
    $index = isset($_GET['index']) ? $_GET['index'] : null;

    // Jika method adalah hapus_semua
    if ($method == 'hapus_semua') {
        session_destroy(); // Hapus semua data session
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit();
    } 
    // Jika method adalah hapus dan index tersedia
    elseif ($method == 'hapus' && $index !== null) {
        if (isset($_SESSION['tugas'][$index])) {
            array_splice($_SESSION['tugas'], $index, 1); // Hapus tugas berdasarkan index
        }
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit();
    } 
    // Jika method adalah edit dan index tersedia
    elseif ($method == 'edit' && $index !== null) {
        if (isset($_SESSION['tugas'][$index])) {
            $_SESSION['edit_index'] = $index; // Simpan index tugas yang akan diedit
        }
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Geisbert</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Gaya dasar */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #white;
            color: #FFFFFF;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #555;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgb(0, 0, 1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #FFFFFF;
        }

        form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.2rem;
        }

        input[type="text"] {
            flex: 1;
            padding: 0.20rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 0.75rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #6200ea;
        }

        button {
            background-color: #6200ea;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            margin-left: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3700b3;
        }

        ol {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            background: #f9f9f9;
            padding: 0.40rem;
            border: 1px solid #ddd;
            border-radius: 9px;
            margin-bottom: 0.90rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        li:hover {
            background-color: #f1f1f1;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .actions a {
            color: #888;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .actions a:hover {
            color: #6200ea;
        }

        .empty-state {
            color: #86200ea;
            font-style: Comic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Aktivitas 🚀</h1>
        <form name="Aktivitas" action="#" method="post">
            <input type="text" placeholder="Buat Kegiatanmu Geisbert..." name="tugas" value="<?php echo isset($_SESSION['edit_index']) ? htmlspecialchars($_SESSION['tugas'][$_SESSION['edit_index']]['nama']) : ''; ?>" required>
            <input type="text" placeholder="Waktu (misal: 06.00-12.00)" name="waktu" value="<?php echo isset($_SESSION['edit_index']) ? htmlspecialchars($_SESSION['tugas'][$_SESSION['edit_index']]['waktu']) : ''; ?>" required>
            <button type="submit"><?php echo isset($_SESSION['edit_index']) ? 'Update' : 'Simpan'; ?></button>
        </form>

        <?php if (isset($_SESSION['tugas']) && !empty($_SESSION['tugas'])) : ?>
            <ol>
                <?php foreach ($_SESSION['tugas'] as $index => $tugas) : ?>
                    <li>
                    <span><?php echo htmlspecialchars($tugas['nama']) . " (" .htmlspecialchars($tugas['waktu']) . ")"; ?></span>
                        <div class="actions">
                            <a href="?method=edit&index=<?php echo $index; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="?method=hapus&index=<?php echo $index; ?>" title="Hapus"><i class="fas fa-trash"></i></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php else : ?>
            <p class="empty-state">Wah belum ada aktivitas yang dibuat. Ayo buatkan aktivitas mu</p>
        <?php endif; ?>

        <hr>
        <!-- Ganti link hapus semua dengan JavaScript -->
        <a href="#" id="hapusSemua">Hapus Semua Kegiatan</a>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Tangani klik pada link "Hapus Semua Tugas"
        document.getElementById('hapusSemua').addEventListener('click', function(e) {
            e.preventDefault(); // Hentikan perilaku default link

            // Cek apakah ada tugas yang tersimpan
            <?php if (isset($_SESSION['tugas']) && !empty($_SESSION['tugas'])) : ?>
                // Jika ada tugas, tampilkan konfirmasi hapus semua
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menghapus semua tugas!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6200ea',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika dikonfirmasi, arahkan ke URL hapus semua
                        window.location.href = '?method=hapus_semua';
                    }
                });
            <?php else : ?>
                // Jika tidak ada tugas, tampilkan pesan bahwa belum ada tugas
                Swal.fire({
                    title: 'Belum ada tugas!',
                    text: "Silahkan tambahkan tugas terlebih dahulu.",
                    icon: 'info',
                    confirmButtonColor: '#6200ea',
                    confirmButtonText: 'Oke'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
