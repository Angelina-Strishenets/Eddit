<?php
session_start();
include "../Blocks/header.php";
require '../functions/db.php';

if (isset($_GET['id_eddit']) && !empty($_GET['id_eddit'])) {
    $id_eddit = $_GET['id_eddit'];
} elseif (isset($_POST['id_eddit']) && !empty($_POST['id_eddit'])) {
    $id_eddit = $_POST['id_eddit'];
    header("Location: comments.php?id_eddit=" . urlencode($id_eddit));
    exit;
} else {
    echo "<p>Invalid request. No post ID provided.</p>";
    exit;
}

$sql = "SELECT name, description FROM Eddits WHERE id = ?";
$request = $pdo->prepare($sql);
$request->execute([$id_eddit]);
$post = $request->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "<p>Post not found</p>";
    exit;
}

$postName = htmlspecialchars($post['name']);
$postDescription = htmlspecialchars($post['description']);

echo "<div class='edit-item' style='position: relative;'>";
echo "<h3>" . $postName . "</h3>";
echo "<p>" . $postDescription . "</p>";

$sql = 'SELECT image_data, image_type FROM pictures WHERE id_eddit = ?';
$request = $pdo->prepare($sql);
$request->execute([$id_eddit]);
$pictures = $request->fetchAll(PDO::FETCH_ASSOC);

if ($pictures) {
    echo "<div class='image-gallery'>";
    foreach ($pictures as $picture) {
        $imageData = base64_encode($picture['image_data']);
        $imageType = htmlspecialchars($picture['image_type']);
        echo "<div class='image-item'>";
        echo "<img src='data:$imageType;base64,$imageData' alt='Post Image' />";
        echo "</div>";
    }
    echo "</div>";
}

$sql = "SELECT SUM(count) AS total_likes FROM likes WHERE id_eddit = ?";
$request = $pdo->prepare($sql);
$request->execute([$id_eddit]);
$likeCount = $request->fetchColumn() ?: 0;

$userLiked = false;
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    $sql = "SELECT id FROM users WHERE login = ?";
    $request = $pdo->prepare($sql);
    $request->execute([$login]);
    $id_user = $request->fetchColumn();

    if ($id_user) {
        $sql = "SELECT COUNT(*) FROM likes WHERE id_eddit = ? AND id_user = ?";
        $request = $pdo->prepare($sql);
        $request->execute([$id_eddit, $id_user]);
        $userLiked = $request->fetchColumn() > 0;
    }
}

$buttonClass = $userLiked ? 'button-like liked' : 'button-like';

echo "<form method='post' action='../functions/forEddits/likesForEdditsComments.php' class='like-form'>";
echo "<input type='hidden' name='id_eddit' value='" . htmlspecialchars($id_eddit) . "'/>";
echo "<button type='submit' class='$buttonClass'></button>";
echo "<span class='like-count'>{$likeCount}</span>";
echo "</form>";

$sql = "SELECT COUNT(*) AS total_comments FROM comments WHERE id_eddit = ?";
$request = $pdo->prepare($sql);
$request->execute([$id_eddit]);
$commentCount = $request->fetchColumn() ?: 0;

echo "<div class='comment-section'>";
echo "<button class='comment-btn' disabled></button>";
echo "<span class='comment-count'>{$commentCount}</span>";
echo "</div>";

echo "</div>";

if (isset($_SESSION['login'])) {
    echo "<div class='comments-section'>";
    echo "<div class='new-Comment'>";
    echo "<label for='title'>Add comment:</label>";
    echo "<div class='input-button-container'>"; 
    echo "<input type='text' id='comment-text' required>";
    echo "<input type='hidden' id='id_eddit' value='" . htmlspecialchars($id_eddit) . "'>";
    echo "<button onclick='addComment()' class='submit-button'>Submit</button>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

$sql = "SELECT users.login, comments.comment 
        FROM comments 
        JOIN users ON comments.id_user = users.id 
        WHERE comments.id_eddit = ?
        ORDER BY comments.id ASC";

$request = $pdo->prepare($sql);
$request->execute([$id_eddit]);
$comments = $request->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='comments-section'>";
if ($comments) {
    foreach ($comments as $comment) {
        $login = htmlspecialchars($comment['login']);
        $commentText = htmlspecialchars($comment['comment']);
        echo "<div class='comment'>";
        echo "<p class='comment-text'><strong>$login</strong>: $commentText</p>";
        echo "</div>";
    }
} else {
    echo "<p>No comments yet</p>";
}
echo "</div>";

?>

<script>
function addComment() {
    let commentText = document.getElementById("comment-text").value;
    let id_eddit = document.getElementById("id_eddit").value;

    if (commentText.trim() === "") {
        alert("Comment cannot be empty!");
        return;
    }

    let formData = new FormData();
    formData.append("id_eddit", id_eddit);
    formData.append("title", commentText);

    fetch("../functions/forEddits/newComment.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                let commentsContainer = document.querySelector(".comments-section");
                let newComment = document.createElement("p");
                newComment.innerHTML = `<strong>${data.user}:</strong> ${data.comment}`;
                commentsContainer.appendChild(newComment);
                document.getElementById("comment-text").value = "";
            }
        } else {
            alert(data.error);
        }
    })
    .catch(error => console.error("Error:", error));
}
</script>
