<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eddit</title>
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>

<div class="header">
<div class="logo">
    <a href="/Pages/main.php">
        <img src="../Images/logo.png" alt="Eddit Logo">
        <b>Eddit</b>
    </a>
</div>

<div class="search-container">
    <form id="searchForm" action="../Pages/search.php" method="GET">
        <div class="search">
            <img src="../Images/loupe.png" alt="Search Icon" id="searchIcon">
            <input type="text" name="query" placeholder="Search Eddit" id="searchInput">
        </div>
    </form>
</div>


    <div class="buttons">
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a href="../Pages/newEddit.php"><button class="newEddit">New Eddit</button></a>
        
        <div class="profile">
            <button type="submit" class="userMenuBtn" id="userMenuBtn">
                <img src="../Images/Profile.png" alt="Profile">
            </button>
        </div>

        <?php
            include "../Blocks/profileMenu.php";
        ?>
        

    <?php else: ?>
        <button class="LogIn" id="loginBtn">Log in</button>
        <button class="SignUp" id="signupBtn">Sign up</button>
    <?php endif; ?>

    <script src="../JS/userMenu.js"></script>
    
</div>
</div>
