<?php
require '../functions/db.php';

$sql = 'SELECT * FROM Eddits';
$request = $pdo->query($sql);
$editsResult = $request->fetchAll(PDO::FETCH_ASSOC);

if (!$editsResult) {
    die("Failed to fetch edits.");
}

$sql = 'SELECT id_eddit, image_data, image_type FROM pictures';
$request = $pdo->query($sql);
$pictures = $request->fetchAll(PDO::FETCH_ASSOC);

$images = [];
foreach ($pictures as $picture) {
    if (!isset($images[$picture['id_eddit']])) {
        $images[$picture['id_eddit']] = [];
    }
    $images[$picture['id_eddit']][] = [
        'data' => base64_encode($picture['image_data']),
        'type' => htmlspecialchars($picture['image_type'])
    ];
}

$sql = 'SELECT id_eddit, SUM(count) AS total_likes FROM likes GROUP BY id_eddit';
$request = $pdo->query($sql);
$likes = $request->fetchAll(PDO::FETCH_ASSOC);

$likesCount = [];
foreach ($likes as $like) {
    $likesCount[$like['id_eddit']] = $like['total_likes'];
}

$sql = 'SELECT id_eddit, COUNT(*) AS total_comments FROM comments GROUP BY id_eddit';
$request = $pdo->query($sql);
$comments = $request->fetchAll(PDO::FETCH_ASSOC);

$commentsCount = [];
foreach ($comments as $comment) {
    $commentsCount[$comment['id_eddit']] = $comment['total_comments'];
}

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    $sql = "SELECT id FROM users WHERE login = ?";
    $request = $pdo->prepare($sql);
    $request->execute([$login]);
    $id_user = $request->fetchColumn();

    if ($id_user) {
        $userLikes = [];
        $sql = 'SELECT id_eddit FROM likes WHERE id_user = ?';
        $request = $pdo->prepare($sql);
        $request->execute([$id_user]);
        $userLikes = $request->fetchAll(PDO::FETCH_COLUMN, 0);
    }
} else {
    $userLikes = [];
}

if (!empty($editsResult)) {
    foreach ($editsResult as $edit) {
        echo "<div class='edit-item' style='position: relative;'>";
        echo "<h3>" . htmlspecialchars($edit['name']) . "</h3>";
        echo "<p>" . htmlspecialchars($edit['description']) . "</p>";

        if (isset($images[$edit['id']])) {
            foreach ($images[$edit['id']] as $image) {
                $imageData = $image['data'];
                $imageType = $image['type'];
                echo "<div class='image-item'>";
                echo "<img src='data:$imageType;base64,$imageData' alt='Image' />";
                echo "</div>";
            }
        }

        $likeCount = isset($likesCount[$edit['id']]) ? $likesCount[$edit['id']] : 0;
        $likedByUser = in_array($edit['id'], $userLikes);

        $buttonClass = $likedByUser ? 'button-like liked' : 'button-like';
        
        echo "<form method='post' action='../functions/forEddits/likesForEddits.php' class='like-form'>";
        echo "<input type='hidden' name='id_eddit' value='".htmlspecialchars($edit['id'])."'/>";
        echo "<button type='submit' class='$buttonClass'></button>";
        echo "<span class='like-count'>{$likeCount}</span>";
        echo "</form>";

        $commentCount = isset($commentsCount[$edit['id']]) ? $commentsCount[$edit['id']] : 0;
        echo "<form method='post' action='../../Pages/comments.php' class='comment-form'>";
        echo "<input type='hidden' name='id_eddit' value='".htmlspecialchars($edit['id'])."'/>";
        echo "<button type='submit' class='comment-btn'></button>";
        echo "<span class='comment-count'>{$commentCount}</span>";
        echo "</form>";

        echo "</div>";
    }
} else {
    echo "<p>No edits found.</p>";
}
?>
