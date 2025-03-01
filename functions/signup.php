<?php
    session_start(); 

    $login = trim(filter_var($_POST['login'], FILTER_SANITIZE_SPECIAL_CHARS));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim(filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS));

    $errors = array();

    if (empty($login)) {
        $errors[] = 'Поле "Логін" не може бути порожнім.';
        exit;
    }

    if (empty($email)) {
        $errors[] = 'Поле "Email" не може бути порожнім.';
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некоректний формат email.';
        exit;
    }

    if (empty($password)) {
        $errors[] = 'Поле "Пароль" не може бути порожнім.';
        exit;
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль повинен містити не менше 6 символів.';
        exit;
    }

    $salt = 'wef42r4in_df';
    $password = md5($salt.$password);

    require "../functions/db.php";
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $checkUser = $pdo->prepare('SELECT * FROM users WHERE login = ? OR email = ?');
    $checkUser->execute([$login, $email]);
    
    if ($checkUser->rowCount() > 0) {
        echo "Користувач з таким логіном або email вже існує.";
        exit;
    }

    $sql = 'INSERT INTO users(login, email, password) VALUES(?, ?, ?)';
    $query = $pdo->prepare($sql);
    $query->execute([$login, $email, $password]);

    $_SESSION['loggedin'] = true;
    $_SESSION['login'] = $login;

    header('Location: /Pages/main.php');
    exit;
?>
