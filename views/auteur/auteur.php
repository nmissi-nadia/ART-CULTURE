<?php
   require_once ("../../config/db_connect.php");
   require_once ("../../classes/User.classe.php");
   require_once ("../../classes/Utilisateur.php");
   require_once ("../../classes/Auteur.php");
session_start();

if (!isset($_SESSION['id_user']) && !isset($_SESSION['role_id'])!==2) { 
    header('Location: ../login.php'); 
    exit();
}

$message = '';

try {
    // Instancier l'auteur avec les données de la session
    $auteur = new Auteur($_SESSION['nom'], $_SESSION['email'], '', $_SESSION['role_id']);
    $auteur->setId($_SESSION['id_user']);
    // $auteur->id_user = $_SESSION['id_user']; 
    $categories = $pdo->query("SELECT id, nom FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    
    // echo "<script>alert('hellowords1');</script>";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<script>alert('hellowords2');</script>";
        $titre = htmlspecialchars(trim($_POST['title'] ?? ''));
        $contenu = htmlspecialchars(trim($_POST['contenu'] ?? ''));
        $categorie_id = intval($_POST['categorie_id'] ?? 0);
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            echo "<script>alert('hellowords3');</script>";
            $uploadDir = '../uploads/';
            $fileName = basename($_FILES['cover_image']['name']);
            $filePath = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $filePath)) {
            
                if ($auteur->creerArticle($pdo, $titre, $contenu, $filePath, $categorie_id)) {
                    $message = 'Article créé avec succès. En attente de validation.';
                    header("Location:./dashboard.php");
                } else {
                    $message = 'Erreur lors de la création de l\'article.';
                }
            } else {
                $message = 'Erreur lors du téléchargement de l\'image.';
            }
        } else {
            $message = 'Veuillez sélectionner une image valide.';
        }
    }
} catch (Exception $e) {
    $message = 'Erreur : ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Article</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-8">Créer un nouvel article</h1>

        <form id="articleForm" class="space-y-6" method="POST" action="#" enctype="multipart/form-data">
            <!-- Champ pour le titre -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Titre de l'article</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       required 
                       minlength="5"
                       maxlength="150"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Entrez le titre de votre article">
            </div>

            <!-- Sélection de la catégorie -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Catégorie</label>
                <select id="category" 
                    name="categorie_id" 
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Sélectionnez une catégorie</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie['id']) ?>">
                        <?= htmlspecialchars($categorie['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            </div>

            <!-- Image de couverture -->
            <div>
                <label for="cover_image" class="block text-sm font-medium text-gray-700">Image de couverture</label>
                <input type="file" 
                       id="cover_image" 
                       name="cover_image"
                       accept="image/*"
                       required
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-sm text-gray-500">Format accepté : JPG, PNG, GIF (Max. 2MB)</p>
            </div>

            <!-- Contenu de l'article -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Contenu de l'article</label>
                <textarea id="content" 
                          name="contenu" 
                          
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 "
                          rows="15"></textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4">
                <button type="button" 
                        onclick="window.location.href='./dashboard.php'"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" name="publi"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                    Publier l'article
                </button>
            </div>
        </form>
    </div>

    <script>
       

        // Validation côté client
        document.getElementById('articleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validation du titre
            const title = document.getElementById('title').value.trim();
            if (title.length < 5) {
                alert('Le titre doit contenir au moins 5 caractères.');
                return;
            }

            // Validation de la catégorie
            const category = document.getElementById('category').value;
            if (!category) {
                alert('Veuillez sélectionner une catégorie.');
                return;
            }

            // Validation de l'image
            const image = document.getElementById('cover_image').files[0];
            if (image && image.size > 2 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 2MB.');
                return;
            }

            // Si tout est valide, on soumet le formulaire
            this.submit();
        });
    </script>
</body>
</html>