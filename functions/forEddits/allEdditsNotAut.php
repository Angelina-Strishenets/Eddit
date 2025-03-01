<?php

require 'functions/db.php';

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

if (!empty($editsResult)) {
    foreach ($editsResult as $edit) {
        echo "<div class='edit-item'>";
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

        echo "</div>";
    }
} else {
    echo "<p>No edits found.</p>";
}
?>
