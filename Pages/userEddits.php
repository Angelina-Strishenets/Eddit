<?php
session_start(); 

if (!isset($_SESSION['login'])) {
    header("Location: /Pages/login.php");
    exit();
}

require '../functions/db.php'; 

if (!$pdo) {
    die("Database connection failed.");
}

$login = $_SESSION['login'];
$sql = "SELECT id FROM users WHERE login = ?";
$request = $pdo->prepare($sql);
$request->execute([$login]);
$userId = $request->fetchColumn();

if (!$userId) {
    echo "User not found";
    exit();
}

include "../functions/forEddits/userEddits.php";
?>

<?php include "../Blocks/header.php"; ?>
    
<div class="main-part">
    <h2>Your Eddits</h2>
    
    <?php
    if (!empty($editsResult)) {
        foreach ($editsResult as $edit) {
            echo "<div class='edit-item'>";
            echo "<h3>" . htmlspecialchars($edit['name']) . "</h3>";
            echo "<p>" . htmlspecialchars($edit['description']) . "</p>";

            if (!empty($edit['id'])) {
                $sql = "SELECT image_data, image_type FROM pictures WHERE id_eddit = ?";
                $request = $pdo->prepare($sql);
                $request->execute([$edit['id']]);
                $image = $request->fetch(PDO::FETCH_ASSOC);

                if ($image) {
                    $imageData = base64_encode($image['image_data']);
                    $imageType = htmlspecialchars($image['image_type']);
                    echo "<img src='data:$imageType;base64,$imageData' alt='Image' />";
                }
            }

            $sql = "SELECT SUM(count) AS total_likes FROM likes WHERE id_eddit = ?";
            $request = $pdo->prepare($sql);
            $request->execute([$edit['id']]);
            $likeCount = $request->fetchColumn() ?: 0;

            $sql = "SELECT COUNT(*) FROM likes WHERE id_eddit = ? AND id_user = ?";
            $request = $pdo->prepare($sql);
            $request->execute([$edit['id'], $userId]);
            $userLiked = $request->fetchColumn() > 0;
            $buttonClass = $userLiked ? 'button-like liked' : 'button-like';

            echo "<form method='post' action='../functions/forEddits/likesForEdditsProfile.php' class='like-form'>";
            echo "<input type='hidden' name='id_eddit' value='" . htmlspecialchars($edit['id']) . "'/>";
            echo "<button type='submit' class='$buttonClass'></button>";
            echo "<span class='like-count'>{$likeCount}</span>";
            echo "</form>";

            $sql = "SELECT COUNT(*) FROM comments WHERE id_eddit = ?";
            $request = $pdo->prepare($sql);
            $request->execute([$edit['id']]);
            $commentCount = $request->fetchColumn() ?: 0;

            echo "<form method='post' action='../../Pages/comments.php' class='comment-form'>";
            echo "<input type='hidden' name='id_eddit' value='" . htmlspecialchars($edit['id']) . "'/>";
            echo "<button type='submit' class='comment-btn'></button>";
            echo "<span class='comment-count'>{$commentCount}</span>";
            echo "</form>";

            echo "</div>";
        }
    } else {
        echo "<p>No edits found.</p>";
    }
    ?>
</div>

<?php include "../Blocks/profileMenu.php"; ?>
</body>
</html>