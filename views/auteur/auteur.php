<?php
   require_once ("../../config/db_connect.php");
   require_once ("../../classes/User.classe.php");
   require_once ("../../classes/Utilisateur.php");
   require_once ("../../classes/Auteur.php");
   session_start();

   
   // Vérification si l'utilisateur est connecté et a le rôle "Auteur"
   if (!isset($_SESSION['id_user']) && !isset($_SESSION['role_id'])!==2) { 
    header('Location: ../login.php'); 
    exit();
}
   
   $message = '';
   
   try {
       // Instancier l'auteur avec les données de la session
       $auteur = new Auteur($_SESSION['nom'], $_SESSION['email'], '', $_SESSION['role_id'], $_SESSION['photo_profil']);
       $auteur->setIdUser($_SESSION['id_user']);
       $categories = $pdo->query("SELECT id, nom FROM categories")->fetchAll(PDO::FETCH_ASSOC);
   
       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           $titre = htmlspecialchars(trim($_POST['title'] ?? ''));
           $contenu = htmlspecialchars(trim($_POST['contenu'] ?? ''));
           $categorie_id = intval($_POST['categorie_id'] ?? 0);
           $image_couverture = '';
   
           if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
               $uploadDir = '../uploads/';
               $fileName = basename($_FILES['cover_image']['name']);
               $filePath = $uploadDir . $fileName;
               if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $filePath)) {
                   $image_couverture = $filePath;
               } else {
                   $message = 'Erreur lors du téléchargement de l\'image.';
               }
           }
   
           try {
               if ($auteur->creerArticle($pdo, $titre, $contenu, $image_couverture, $categorie_id)) {
                   $message = 'Article créé avec succès.';
               } else {
                   $message = 'Erreur lors de la création de l\'article.';
               }
           } catch (Exception $e) {
               $message = 'Erreur : ' . $e->getMessage();
           }
       }
   } catch (PDOException $e) {
       die('Erreur lors de la récupération des catégories : ' . $e->getMessage());
   }
   ?>
   
   <!DOCTYPE html>
   <html lang="fr">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Créer un Article</title>
       <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
   </head>
   <body class="bg-gray-100">
       <div class="container mx-auto p-4">
           <h1 class="text-2xl font-bold mb-4">Créer un Article</h1>
   
           <?php if ($message): ?>
               <div class="bg-<?php echo strpos($message, 'Erreur') === false ? 'green' : 'red'; ?>-500 text-white p-2 rounded mb-4">
                   <?php echo $message; ?>
               </div>
           <?php endif; ?>
   
           <form method="POST" action="" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
               <div class="mb-4">
                   <label for="title" class="block text-gray-700">Titre</label>
                   <input type="text" name="title" id="title" class="w-full px-3 py-2 border rounded" required>
               </div>
               <div class="mb-4">
                   <label for="contenu" class="block text-gray-700">Contenu</label>
                   <textarea name="contenu" id="contenu" class="w-full px-3 py-2 border rounded" required></textarea>
               </div>
               <div class="mb-4">
                   <label for="categorie_id" class="block text-gray-700">Catégorie</label>
                   <select name="categorie_id" id="categorie_id" class="w-full px-3 py-2 border rounded" required>
                       <?php foreach ($categories as $categorie): ?>
                           <option value="<?php echo $categorie['id']; ?>"><?php echo htmlspecialchars($categorie['nom']); ?></option>
                       <?php endforeach; ?>
                   </select>
               </div>
               <div class="mb-4">
                   <label for="cover_image" class="block text-gray-700">Image de couverture</label>
                   <input type="file" name="cover_image" id="cover_image" class="w-full px-3 py-2 border rounded">
               </div>
               <div class="flex justify-end">
                   <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Créer</button>
               </div>
           </form>
       </div>
   </body>
   </html>