<?php
session_start();

require '../functions/db.php'; 

if (!$pdo) {
    die("Database connection failed.");
}

$sql = "SELECT * FROM Eddits WHERE id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$editsResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

session_start(); 

if (!isset($_SESSION['login'])) {
    header("Location: /Pages/login.php");
    exit();
}

$login = $_SESSION['login'];
$sql = "SELECT id FROM users WHERE login = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$login]);
$userId = $stmt->fetchColumn();

if (!$userId) {
    echo "User not found";
    exit();
}
?>
