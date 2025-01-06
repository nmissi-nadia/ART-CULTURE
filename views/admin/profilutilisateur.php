<?php
session_start();
require_once ("../../config/db_connect.php");   
require_once ("../../classes/User.classe.php");
require_once ("../../classes/Admin.php");

if (!isset($_SESSION['id_user']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$userId = (int)$_GET['id'];

try {
    // Lire les données de l'utilisateur à partir de la base de données
    $query = "SELECT * FROM utilisateurs WHERE id_user = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: dashboard.php');
        exit();
    }

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Artistique</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/fr.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- En-tête du profil -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row items-center">
                <div class="relative">
                    <img id="profileImage" src="../<?= htmlspecialchars($user['photo_profil']) ?>" alt="Photo de profil" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-purple-500">
                    <button id="editPhotoBtn" class="absolute bottom-0 right-0 bg-purple-500 text-white p-2 rounded-full hover:bg-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                </div>
                <div class="md:ml-6 mt-4 md:mt-0 text-center md:text-left">
                    <h1 id="userName" class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['nom']) ?></h1>
                    <p id="userEmail" class="text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
                    <p id="userStatus" class="mt-2">
                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800"><?= htmlspecialchars($user['status']) ?></span>
                    </p>
                </div>
                <div class="mt-6 ml-auto bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                    <a href="dashboard.php" class="flex items-center text-blue-600 hover:text-blue-900">
                        <i class="fas fa-arrow-left mr-2"></i> Retour à la page principale
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Bio -->
            <div class="md:col-span-2 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Biographie</h2>
                <p id="userBio" class="text-gray-700"><?= $user['bio'] ?></p>
            </div>

            <!-- Statistiques -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Statistiques</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Membre depuis</span>
                        <span id="joinDate" class="font-medium"><?= htmlspecialchars($user['date_inscription']) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Dernière connexion</span>
                        <span id="lastLogin" class="font-medium"><?= $user['derniere_connexion'] ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Articles publiés</span>
                        <span id="articleCount" class="font-medium">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>