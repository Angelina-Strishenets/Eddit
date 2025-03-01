<?php
include "../Blocks/header.php";
require '../functions/db.php';

if (isset($_GET['query'])) {
    $searchTerm = $_GET['query'];

    $sql = "
        SELECT eddits.id, eddits.name, eddits.description, 
               pictures.name_image, pictures.image_data, pictures.image_type,
               (SELECT SUM(count) FROM likes WHERE likes.id_eddit = eddits.id) AS total_likes,
               (SELECT COUNT(*) FROM comments WHERE comments.id_eddit = eddits.id) AS total_comments
        FROM eddits
        LEFT JOIN pictures ON eddits.id = pictures.id_eddit
        WHERE eddits.name LIKE :searchTerm OR eddits.description LIKE :searchTerm
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['searchTerm' => "%$searchTerm%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
        $currentEditId = null;
        foreach ($results as $result) {
            if ($currentEditId !== $result['id']) {
                if ($currentEditId !== null) {
                    echo "</div><br>";
                }
                $currentEditId = $result['id'];
                echo "<div class='edit-item'>";
                echo "<h3>" . htmlspecialchars($result['name']) . "</h3>";
                echo "<p>" . htmlspecialchars($result['description']) . "</p>";
            }
            
            if ($result['name_image'] && $result['image_data']) {
                $imageData = 'data:' . $result['image_type'] . ';base64,' . base64_encode($result['image_data']);
                echo "<img src='$imageData' alt='" . htmlspecialchars($result['name_image']) . "'>";
            }

            $likeCount = $result['total_likes'] ?: 0;
            $commentCount = $result['total_comments'] ?: 0;
            
            echo "<div class='post-actions'>";
            echo "<form method='post' action='../functions/forEddits/likesForEdditsSearch.php' class='like-form'>";
            echo "<input type='hidden' name='id_eddit' value='" . htmlspecialchars($result['id']) . "'/>";
            echo "<input type='hidden' name='redirect' value='" . htmlspecialchars($_SERVER['REQUEST_URI']) . "'/>";
            echo "<button type='submit' class='button-like'></button>";
            echo "<span class='like-count'>{$likeCount}</span>";
            echo "</form>";

            
            echo "<form method='post' action='../../Pages/comments.php' class='comment-form'>";
            echo "<input type='hidden' name='id_eddit' value='" . htmlspecialchars($result['id']) . "'/>";
            echo "<button type='submit' class='comment-btn'></button>";
            echo "<span class='comment-count'>{$commentCount}</span>";
            echo "</form>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "No results found.";
    }
}
?>
