<?php

require_once '../config/db_connect.php'; 
require_once '../classes/User.classe.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscri'])) {
    
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $motDePasse = $_POST['password'] ?? '';
    $confirmMotDePasse = $_POST['copass'] ?? '';
    $role_id = $_POST['role'] ?? '';
    $photoProfil = $_FILES['photoProfil'] ?? null;

    
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

        if ($photoProfil && $photoProfil['error'] === UPLOAD_ERR_OK) {
            $photoPath = './uploads/' . basename($photoProfil['name']);
            move_uploaded_file($photoProfil['tmp_name'], $photoPath);
            $user->setPhotoProfil($photoPath);
        }

    
        // Utilisation de la méthode sInscrire
        if ($user->sInscrire($pdo)) {
            // Send welcome email
            $subject = 'Bienvenue sur L\'Art & La Culture';
            $message = ($role_id === 2) ? 
                'Bienvenue, Auteur! Nous vous invitons à publier vos articles.' : 
                'Bienvenue! Explorez, commentez et ajoutez des articles à vos favoris.';
            mail($email, $subject, $message);
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
