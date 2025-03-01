<?php
session_start();
require '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login'])) {
    echo json_encode(["success" => false, "error" => "You must be logged in to add a comment."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title']) && isset($_POST['id_eddit'])) {
        $user_login = $_SESSION['login'];

        $sql = 'SELECT id FROM users WHERE login = :login';
        $request = $pdo->prepare($sql);
        $request->execute(['login' => $user_login]);
        $user = $request->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(["success" => false, "error" => "User not found."]);
            exit;
        }

        $id_user = $user['id'];
        $comment = trim($_POST['title']);
        $id_eddit = $_POST['id_eddit'];

        if (empty($comment)) {
            echo json_encode(["success" => false, "error" => "Comment cannot be empty."]);
            exit;
        }

        if (empty($id_eddit)) {
            echo json_encode(["success" => false, "error" => "Post ID is missing."]);
            exit;
        }

        $sql = 'INSERT INTO comments(id_eddit, id_user, comment) VALUES(:id_eddit, :id_user, :comment)';
        $request = $pdo->prepare($sql);
        $request->bindParam(':id_eddit', $id_eddit);
        $request->bindParam(':id_user', $id_user);
        $request->bindParam(':comment', $comment);

        if ($request->execute()) {
            echo json_encode([
                "success" => true, 
                "comment" => htmlspecialchars($comment),
                "user" => htmlspecialchars($user_login),
                "redirect" => "../../Pages/comments.php?id_eddit=" . urlencode($id_eddit)
            ]);
            exit;
        } else {
            echo json_encode(["success" => false, "error" => "Failed to add comment."]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing required data."]);
        exit;
    }
}
?>
