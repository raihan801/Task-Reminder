<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Username sudah terdaftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - TaskMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #0f172a, #1e293b);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            color: white;
        }
        .register-container {
            background: #1e293b;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        .register-container h2 {
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
        }
        .form-control {
            background: #334155;
            border: none;
            color: white;
        }
        .form-control:focus {
            background: #334155;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(34, 211, 238, 0.5);
        }
        .btn-custom {
            background-color: #0ea5e9;
            border: none;
            font-weight: 600;
        }
        .btn-custom:hover {
            background-color: #0284c7;
        }
        .error-message {
            background: #dc2626;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2>📝 Daftar TaskMate</h2>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input class="form-control" type="text" name="username" required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button class="btn btn-custom w-100" type="submit">Daftar</button>
    </form>
    <p class="mt-4 text-center">Sudah punya akun? <a href="login.php" class="text-info">Masuk</a></p>
</div>
</body>
</html>