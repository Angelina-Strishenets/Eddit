<?php
session_start();
require '../../functions/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_eddit'])) {
    $id_eddit = $_POST['id_eddit'];
    
    try {
        $sql = 'SELECT name_image FROM pictures WHERE id_eddit = :id_eddit';
        $query = $pdo->prepare($sql);
        $query->execute(['id_eddit' => $id_eddit]);
        $images = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as $image) {
            $filePath = '../images/' . $image['name_image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $sql = 'DELETE FROM pictures WHERE id_eddit = :id_eddit';
        $query = $pdo->prepare($sql);
        $query->execute(['id_eddit' => $id_eddit]);

        $sql = 'DELETE FROM Eddits WHERE id = :id_eddit';
        $query = $pdo->prepare($sql);
        $query->execute(['id_eddit' => $id_eddit]);
        
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }

    header('Location: ../../Pages/userEddits.php');
    exit();
}
?>
