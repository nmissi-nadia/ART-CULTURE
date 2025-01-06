<?php
session_start();
require_once ("../../config/db_connect.php");
require_once ("../../classes/User.classe.php");
require_once ("../../classes/Admin.php");

if (!isset($_SESSION['id_user']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];

    try {
        $query = "DELETE FROM utilisateurs WHERE id_user = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $userId]);

        header('Location: ./dashboard.php');
        exit();
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
        die('Une erreur est survenue lors de la suppression de l\'utilisateur.');
    }
} else {
    header('Location: ./dashboard.php');
    exit();
}
?>