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
    $nom = htmlspecialchars(trim($_POST['nom']));
    $description = htmlspecialchars(trim($_POST['description_cat']));

    if (empty($nom) || empty($description)) {
        die('Tous les champs sont requis.');
    }

    try {
        $query = "UPDATE categories SET nom = :nom, description_cat = :description WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':id' => $categoryId
        ]);

        header('Location: dashboard.php');
        exit();
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de la catégorie : " . $e->getMessage());
        die('Une erreur est survenue lors de la mise à jour de la catégorie.');
    }
} else {
    header('Location: dashboard.php');
    exit();
}
?>