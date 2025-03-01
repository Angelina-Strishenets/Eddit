<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim(filter_var($_POST['login'], FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim(filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS));

    try {
        require '../functions/db.php'; 
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }

    $salt = 'wef42r4in_df';
    $hashedPassword = md5($salt . $password);

    $request = $pdo->prepare('SELECT * FROM users WHERE login = ? AND password = ?');
    $request->execute([$login, $hashedPassword]);

    if ($request->rowCount() > 0) {
        $_SESSION['loggedin'] = true;
        $_SESSION['login'] = $login;

        header('Location: /Pages/main.php');
        exit;
    } else {
        echo 'Invalid username or password';
    }
} else {
    echo 'Invalid request method.';
}
?>
