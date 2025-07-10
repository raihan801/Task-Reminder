<?php
include("config.php");
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"];
$user_id = $_SESSION["user_id"];

$conn->query("UPDATE tasks SET status = 'completed' WHERE id = $id AND user_id = $user_id");

header("Location: dashboard.php");
exit;
?>