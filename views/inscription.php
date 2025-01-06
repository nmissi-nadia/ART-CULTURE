<?php

require_once '../config/db_connect.php'; 
require_once '../classes/User.classe.php';
require_once '../classes/Utilisateur.php';
require_once '../classes/Auteur.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscri'])) {
    
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $motDePasse = $_POST['password'] ?? '';
    $confirmMotDePasse = $_POST['copass'] ?? '';
    $role_id = $_POST['role'] ?? ''; // Par défaut, rôle utilisateur

    
    if (empty($nom) || empty($email) || empty($motDePasse) || empty($confirmMotDePasse)) {
        die('Tous les champs sont requis.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('L\'adresse email n\'est pas valide.');
    }

    if ($motDePasse !== $confirmMotDePasse) {
        die('Les mots de passe ne correspondent pas.');
    }

    if (strlen($motDePasse) < 8) {
        die('Le mot de passe doit contenir au moins 8 caractères.');
    }

    try {
       
        $user = null;
        if ($role_id === 2) { // Rôle Auteur
            $user = new Auteur($nom, $email, $motDePasse, $role_id);
        } else { 
            $user = new Utilisateur($nom, $email, $motDePasse, $role_id);
        }
    
        // Utilisation de la méthode sInscrire
        if ($user->sInscrire($pdo)) {
            echo 'Inscription réussie. Vous pouvez maintenant vous connecter.';
            header("Location:./login.php"); 
        } else {
            die('Une erreur est survenue lors de l\'inscription.');
        }
    } catch (Exception $e) {
        error_log('Erreur lors de l\'inscription : ' . $e->getMessage());
        die('Une erreur est survenue. Veuillez réessayer plus tard.');
    }
    
} else {
    die('Méthode de requête non autorisée.');
}
?>
