<?php
session_start();
require_once ("../../config/db_connect.php");
require_once ("../../classes/User.classe.php");
require_once ("../../classes/Admin.php");

if (!isset($_SESSION['id_user']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $categoryId = (int)$_POST['category_id'];

    try {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $categoryId]);

        header('Location: ./dashboard.php');
        exit();
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression de la catégorie : " . $e->getMessage());
        die('Une erreur est survenue lors de la suppression de la catégorie.');
    }
} else {
    header('Location: ./dashboard.php');
    exit();
}
?>