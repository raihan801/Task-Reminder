<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"];
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $due_date = $_POST["due_datetime"];

    $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, due_date=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sssii", $title, $description, $due_date, $id, $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <h3>Edit Tugas</h3>
    <form method="POST">
        <input class="form-control mb-2" type="text" name="title" value="<?= htmlspecialchars($task["title"]) ?>" required>
        <textarea class="form-control mb-2" name="description"><?= htmlspecialchars($task["description"]) ?></textarea>
        <input class="form-control mb-2" type="datetime-local" name="due_datetime" value="<?= date('Y-m-d\TH:i', strtotime($task["due_date"])) ?>" required>
        <button class="btn btn-success" type="submit">Update</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
    </form>
</body>
</html>