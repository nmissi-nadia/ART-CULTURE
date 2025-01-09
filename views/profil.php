<?php
session_start();
require_once ("../config/db_connect.php");
   require_once ("../classes/User.classe.php");

if (!isset($_SESSION['id_user'])) {
    header('Location: ./login.php');
    exit();
}

$id = $_SESSION['id_user']; 
$admin = new User($_SESSION['nom'], $_SESSION['email'], $_SESSION['role_id'], $id);
$admin->setIdUser($_SESSION['id_user']);
$user = $admin->Infos_User($pdo, $id);
if (!$user) {
    die("Utilisateur introuvable.");
}
$nombreArticlesPublies = 0;
if ($user['role_id'] == 2) {
    $nombreArticlesPublies = $admin->obtenirNombreArticlesPublies($pdo, $user['id_user']);
}
$message= '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $bio = htmlspecialchars($_POST['bio']);
    $photo_profil = null;

    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
        $dossier = 'uploads/';
        $nom_fich = basename($_FILES['photo_profil']['name']);
        $photo_profil = $dossier . $nom_fich;

        if (!is_dir($dossier)) {
            mkdir($dossier, 0777, true);
        }

        if (!move_uploaded_file($_FILES['photo_profil']['tmp_name'], $photo_profil)) {
            $message = "Erreur lors de l'upload de la photo.";
        }
    }

    // Mettre à jour les informations
    $admin->setNom($nom);
    $admin->setEmail($email);
    $admin->setBio($bio);
    $admin->setPhotoProfil($photo_profil);

    try {
        $admin->mettreAJourProfil($pdo);
        $message = 'Profil mis à jour avec succès!';
        $user = $admin->Infos_User($pdo, $id); // Refresh user data
    } catch (Exception $e) {
        $message = 'Erreur: ' . $e->getMessage();
    }
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
                    <img id="profileImage" src="<?= $user['photo_profil'] ?>" alt="Photo de profil" 
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
                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800"><?= $user['status'] ?></span>
                    </p>
                </div>
                <button id="editProfileBtn" type="button" class="ml-auto bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                    Modifier le profil
                </button>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Bio -->
            <div class="md:col-span-2 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Biographie</h2>
                <p id="userBio" class="text-gray-700"><?php echo htmlspecialchars($user['bio']); ?></p>
            </div>
            <!-- Statistiques -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Statistiques</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Membre depuis</span>
                        <span id="joinDate" class="font-medium"><?= $user['date_inscription'] ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Dernière connexion</span>
                        <span id="lastLogin" class="font-medium"><?= $user['derniere_connexion'] ?></span>
                    </div>
                    <?php if ($user['role_id'] == 2): ?>
                <div class="flex justify-between items-center mt-4">
                    <span class="text-gray-600">Articles publiés</span>
                    <span id="nombreArticlesPublies" class="font-medium"><?php echo $nombreArticlesPublies; ?></span>
                </div>
            <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center mt-8">
            <button id="homeBtn" class="bg-purple-500 text-white px-4 py-2 rounded">Retour à l'accueil</button>
        </div>

    <!-- Modal de modification -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Modifier le profil</h3>
                <form id="editForm" action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="editName" class="block text-gray-700">Nom</label>
                        <input type="text" name="nom" id="editName" class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label for="editEmail" class="block text-gray-700">Email</label>
                        <input type="email" name="email" id="editEmail" class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label for="editBio" class="block text-gray-700">Biographie</label>
                        <textarea name="bio" id="editBio" class="w-full px-3 py-2 border rounded" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="editPhoto" class="block text-gray-700">Photo de profil</label>
                        <input type="file" name="photo_profil" id="editPhoto" class="w-full px-3 py-2 border rounded">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="cancelEdit" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Annuler</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Données de l'utilisateur
    const userData = {
            nom: "<?= htmlspecialchars($user['nom']) ?>",
            email: "<?= htmlspecialchars($user['email']) ?>",
            bio: "<?= htmlspecialchars($user['bio']) ?>"
        };

        // Initialisation de la page
        document.addEventListener('DOMContentLoaded', () => {
            setupEventListeners();
        });

        function setupEventListeners() {
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');

            document.getElementById('editProfileBtn').addEventListener('click', () => {
                document.getElementById('editName').value = userData.nom;
                document.getElementById('editEmail').value = userData.email;
                document.getElementById('editBio').value = userData.bio;
                editModal.classList.remove('hidden');
            });

            document.getElementById('cancelEdit').addEventListener('click', () => {
                editModal.classList.add('hidden');
            });
            document.getElementById('homeBtn').addEventListener('click', () => {
                if (userData.id_user === 1) {
                    window.location.href = './admin/dashboard.php'; // Change 'admin_home.php' to the actual admin home page URL
                } else if (userData.id_user === 2) {
                    window.location.href = './auteur/dashboard.php'; // Change 'index.php' to the actual home page URL
                }else (userData.id_user === 3){
                    window.location.href = './utilisateur/home.php'; // Change 'index.php' to the actual home page URL
                }
            });
        }
    </script>
</body>
</html>