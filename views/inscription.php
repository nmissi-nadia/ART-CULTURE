<?php

require_once '../config/db_connect.php';
require_once '../classes/User.classe.php';
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscri'])) {
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $motDePasse = $_POST['password'] ?? '';
    $confirmMotDePasse = $_POST['copass'] ?? '';
    $role_id = $_POST['role'] ?? ''; // Par défaut, rôle utilisateur
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
        $user = new Utilisateur($nom, $email, $motDePasse, $role_id);

        if ($photoProfil && $photoProfil['error'] === UPLOAD_ERR_OK) {
            $photoPath = '../uploads/' . basename($photoProfil['name']);
            move_uploaded_file($photoProfil['tmp_name'], $photoPath);
            $user->setPhotoProfil($photoPath);
        }

        if ($user->sInscrire($pdo)) {
            // Send welcome email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true;
                $mail->Username = 'ahmed.benkrara12@gmail.com'; // SMTP username
                $mail->Password = 'cgidqganvckpgtch'; // SMTP password (use App Password if 2-Step Verification is enabled)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('nmissinadia@gmail.com', 'Art & Culture');
                $mail->addAddress($email, $nom);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Bienvenue sur L\'Art & La Culture';
                $mail->Body = ($role_id === 2) ? 
                    'Bienvenue, Auteur! Nous vous invitons à publier vos articles.' : 
                    'Bienvenue! Explorez, commentez et ajoutez des articles à vos favoris.';

                $mail->send();

                echo 'Inscription réussie. Vous pouvez maintenant vous connecter.';
                header("Location:./login.php");
                exit; // Ensure the script stops executing after redirection
            } catch (Exception $e) {
                error_log('Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo);
                die('Une erreur est survenue lors de l\'envoi de l\'email : ' . $mail->ErrorInfo);
            }
        } else {
            die('Une erreur est survenue lors de l\'inscription.');
        }
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'L\'adresse email est déjà utilisée.') !== false) {
            die('Erreur : L\'adresse email est déjà utilisée.');
        } else {
            error_log('Erreur lors de l\'inscription : ' . $e->getMessage());
            die('Une erreur est survenue. Veuillez réessayer plus tard.');
        }
    }
} 
?>