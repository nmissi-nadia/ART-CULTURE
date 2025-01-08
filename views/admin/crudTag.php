<?php

require_once '../../config/db_connect.php';
require_once '../../classes/User.classe.php';
require_once '../../classes/Administrateur.php';
session_start();

$administrateur = new Administrateur($_SESSION['nom'], $_SESSION['email'], '', $_SESSION['role_id'], $_SESSION['photo_profil']);
$administrateur->setIdUser($_SESSION['id_user']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['creerTag'])) {
        $nom = htmlspecialchars(trim($_POST['nom']));
        $administrateur->creeTags($pdo, $nom);
        header('Location: dashboard.php');
        exit();
    } elseif (isset($_POST['modifierTag'])) {
        $tagId = intval($_POST['tag_id']);
        $nom = htmlspecialchars(trim($_POST['nom']));
        $administrateur->modifierTags($pdo, $tagId, $nom);
        header('Location: dashboard.php');
        exit();
    } elseif (isset($_POST['supprimerTag'])) {
        $tagId = intval($_POST['tag_id']);
        $administrateur->supprimerTags($pdo, $tagId);
        header('Location: dashboard.php');
        exit();
    }
}
?>