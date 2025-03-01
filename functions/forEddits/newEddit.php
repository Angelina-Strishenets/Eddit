<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title']) && isset($_POST['description'])) {
        $userLogin = $_SESSION['login'];
        
        $sql = 'SELECT id FROM users WHERE login = :login';
        $queryId = $pdo->prepare($sql);
        $queryId->execute(['login' => $userLogin]);
        $user = $queryId->fetch(PDO::FETCH_ASSOC);
        $id_user = $user['id'];

        $name = $_POST['title'] ? $_POST['title'] : 'No title';
        $description = $_POST['description'] ? $_POST['description'] : 'No description';

        $sql = 'INSERT INTO Eddits(id_user, name, description) VALUES(:id_user, :name, :description)';
        $query = $pdo->prepare($sql);
        $query->bindParam(':id_user', $id_user);
        $query->bindParam(':name', $name);
        $query->bindParam(':description', $description);
        $query->execute();

        $id_eddit = $pdo->lastInsertId();

        if (isset($_FILES['image']) && !empty($_FILES['image']['name'][0])) {
            $targetDirectory = "../images/";
            if (!file_exists($targetDirectory)) {
                mkdir($targetDirectory, 0777, true);
            }

            foreach ($_FILES['image']['name'] as $key => $name) {
                $tmp_name = $_FILES['image']['tmp_name'][$key];
                $targetPath = $targetDirectory . basename($name);

                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $image_data = file_get_contents($targetPath);
                    $image_type = mime_content_type($targetPath);

                    $sql_image = 'INSERT INTO pictures(id_eddit, name_image, image_data, image_type) VALUES (:id_eddit, :name_image, :image_data, :image_type)';
                    $query_image = $pdo->prepare($sql_image);
                    $query_image->bindParam(':id_eddit', $id_eddit);
                    $query_image->bindParam(':name_image', $name);
                    $query_image->bindParam(':image_data', $image_data, PDO::PARAM_LOB);
                    $query_image->bindParam(':image_type', $image_type);
                    $query_image->execute();
                }
            }
        }

        header('Location: ../../Pages/userEddits.php');
    }
}

