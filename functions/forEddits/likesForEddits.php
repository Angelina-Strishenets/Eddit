<?php
session_start();
require '../db.php';

if (!isset($_SESSION['login'])) {
    header("Location: /Pages/login.php");
    exit();
}

$login = $_SESSION['login'];
$sql = "SELECT id FROM users WHERE login = ?";
$request = $pdo->prepare($sql);
$request->execute([$login]);
$id_user = $request->fetchColumn();

if (!$id_user) {
    echo "User not found";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_eddit'])) {
    $id_eddit = $_POST['id_eddit'];
    
    try {
        $sql = "SELECT COUNT(*) FROM likes WHERE id_eddit = ? AND id_user = ?";
        $request = $pdo->prepare($sql);
        $request->execute([$id_eddit, $id_user]);
        $userLiked = $request->fetchColumn() > 0;

        if ($userLiked) {
            $sql = "DELETE FROM likes WHERE id_eddit = ? AND id_user = ?";
            $request = $pdo->prepare($sql);
            $request->execute([$id_eddit, $id_user]);

            $sql = "UPDATE likes SET count - 1 WHERE id_eddit = ?";
            $request = $pdo->prepare($sql);
            $request->execute([$id_eddit]);
        } else {
            $sql = "INSERT INTO likes (id_eddit, id_user, count) VALUES (?, ?, 1)";
            $request = $pdo->prepare($sql);
            $request->execute([$id_eddit, $id_user]);

            $sql = "UPDATE likes SET count + 1 WHERE id_eddit = ?";
            $request = $pdo->prepare($sql);
            $request->execute([$id_eddit]);
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }

    header('Location: ../../Pages/main.php');
    exit();
}
?>
