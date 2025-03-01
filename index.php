<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: /Pages/main.php');
    exit;
}
?>
    <?php include "Blocks/header.php"; ?>
    
    <div class="main-part">
        <h2>Hello Guest, if you would like to add a new Eddit, please login or register</h2>
        <?php
            include "functions/forEddits/allEdditsNotAut.php"; 
        ?>
    </div>

    <?php include "Blocks/footer.php"; ?>

    <!-- Модальне вікно Log In -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Log In</h2>
            <form method="post" action="/functions/logIn.php">
                <label for="login">Username:</label>
                <input type="text" id="login" name="login" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <div class="button-container">
                    <button type="submit">Log In</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальне вікно Sign Up -->
    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Sign Up</h2>
            <form method="post" action="/functions/signup.php">
                <label for="login">Username:</label>
                <input type="text" id="login" name="login" value="<?= isset($login) ? htmlspecialchars($login) : '' ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <div class="button-container">
                    <button type="submit">Sign Up</button>
                </div>
            </form>
        </div>
    </div>

    <script src="JS/LogIn.js"></script>
    <script src="JS/SignUp.js"></script>
</body>
</html>
