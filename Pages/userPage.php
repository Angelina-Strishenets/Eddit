<?php
include "../Blocks/header.php";
?>
    
<div class="main-part">
    <h2>User profile</h2>
    <br><br>
    <?php 
       echo "<h2>Thank you for choosing us, ".$_SESSION['login']."</h2>";
    ?>

    <?php
    require '../functions/db.php'; 

    $userLogin = $_SESSION['login'];

    $sqlUserId = "SELECT id FROM users WHERE login = :login";
    $stmtUserId = $pdo->prepare($sqlUserId);
    $stmtUserId->execute(['login' => $userLogin]);
    $userIdResult = $stmtUserId->fetch(PDO::FETCH_ASSOC);

    if ($userIdResult) {
        $userId = $userIdResult['id'];

        $sqlCount = "SELECT COUNT(*) AS edit_count FROM eddits WHERE id_user = :id_user";
        $stmtCount = $pdo->prepare($sqlCount);
        $stmtCount->execute(['id_user' => $userId]);
        $countResult = $stmtCount->fetch(PDO::FETCH_ASSOC);

        echo "<br><br><h2>Count of your Eddits: " . htmlspecialchars($countResult['edit_count']) . "</h2>";
    } else {
        echo "User not found";
    }
    ?>

</div>
</body>
</html>
