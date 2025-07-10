<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $due_datetime = $_POST["due_datetime"];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $description, $due_datetime);
    $stmt->execute();
}

$tasks = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Task Reminder Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            background-color: #1e293b;
            color: #ffffff;
            width: 250px;
            padding: 20px;
            height: 100vh;
        }
        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: #ffffff;
            margin: 15px 0;
            text-decoration: none;
            font-weight: 500;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .main {
            flex: 1;
            padding: 40px;
        }
        .task-card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .task-card h5 {
            margin-bottom: 5px;
        }
        .btn-outline {
            border: 1px solid #0d9488;
            color: #0d9488;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📚 TaskMate</h2>
    <a href="dashboard.php">📋 Dashboard</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h3 class="mb-4">Selamat Datang di TaskMate!</h3>

    <div class="card p-4 mb-5 shadow-sm">
        <h5 class="mb-3">Tambah Tugas Baru</h5>
        <form method="POST">
            <input class="form-control mb-2" type="text" name="title" placeholder="Judul Tugas" required>
            <textarea class="form-control mb-2" name="description" placeholder="Deskripsi (opsional)"></textarea>
            <input class="form-control mb-2" type="datetime-local" name="due_datetime" required>
            <button class="btn btn-primary" type="submit">Tambah</button>
        </form>
    </div>

    <h5 class="mb-3">Daftar Tugas Anda</h5>
    <?php while ($task = $tasks->fetch_assoc()) {
        $due = new DateTime($task["due_date"]);
        $now = new DateTime();
        $interval = $now->diff($due);
        $deadline_soon = $interval->days <= 1 && $now < $due;
    ?>
    <div class="task-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5><?= htmlspecialchars($task["title"]) ?></h5>
                <p class="mb-1"><?= nl2br(htmlspecialchars($task["description"])) ?></p>
                <small class="<?= $deadline_soon ? 'text-danger fw-bold' : '' ?>">
                    Deadline: <?= $due->format('d-m-Y H:i') ?>
                    <?php if ($deadline_soon): ?>⚠️ Deadline tinggal 1 hari!
    <?php elseif ($now > $due): ?>
    <div class="overdue-alert" data-title="<?= htmlspecialchars($task["title"]) ?>"></div>⏰ Terlewat deadline!
    <?php endif; ?>
                </small>
            </div>
            <div>
                <?php if ($task["status"] == "pending") { ?>
                <a href="edit_task.php?id=<?= $task["id"] ?>" class="btn btn-warning btn-sm me-2">Edit</a>
                <a href="delete_task.php?id=<?= $task["id"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus tugas ini?')">Hapus</a><br><br>
                    <a href="complete_task.php?id=<?= $task["id"] ?>" class="btn btn-outline btn-sm">Tandai Selesai</a>
                <?php } else { ?>
                    <span class="badge bg-success">Selesai</span>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const alerts = document.querySelectorAll('.overdue-alert');
        alerts.forEach(alert => {
            Swal.fire({
                icon: 'error',
                title: 'Tugas Terlambat!',
                text: alert.dataset.title + ' sudah melewati batas waktu!',
                confirmButtonColor: '#ef4444'
            });
        });
    });
</script>

</body>
</html>