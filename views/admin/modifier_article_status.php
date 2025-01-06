<?php
session_start();
require_once ("../../config/db_connect.php");
require_once ("../../classes/User.classe.php");
require_once ("../../classes/Admin.php");

if (!isset($_SESSION['id_user']) || $_SESSION['role_id'] !== 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $articleId = $data['id'];
    $status = $data['status'];

    try {
        $query = "UPDATE articles SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':status' => $status,
            ':id' => $articleId
        ]);
        echo json_encode(['success' => true]);
        header("Location:./dashboard.php");
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour du statut de l'article : " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>