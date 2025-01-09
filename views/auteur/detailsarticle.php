<?php
session_start();
require_once ("../../config/db_connect.php");
require_once ("../../classes/User.classe.php");
require_once ("../../classes/Utilisateur.php");

if (!isset($_SESSION['id_user']) && !isset($_SESSION['role_id'])!==2) {
    header('Location: ./login.php'); 
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: home.php');
    exit();
}

$articleId = (int)$_GET['id'];

try {
    // Lire les données de l'article à partir de la base de données
    $query = "SELECT a.*, u.nom AS auteur, c.nom AS categorie FROM articles a
              JOIN utilisateurs u ON a.auteur_id = u.id_user
              JOIN categories c ON a.categorie_id = c.id
              WHERE a.id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$articleId]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        header('Location: ./home.php');
        exit();
    }

    // Ajouter un commentaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $commentaire = htmlspecialchars(trim($_POST['commentaire']));
        if (!empty($commentaire)) {
            $stmt = $pdo->prepare('INSERT INTO commentaires (article_id, utilisateur_id, contenu, date_creation) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$articleId, $_SESSION['id_user'], $commentaire]);
        }
    }

       // Ajouter un like et aux favoris
       if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
        $stmt = $pdo->prepare('INSERT INTO favoris (article_id, utilisateur_id) VALUES (?, ?)');
        $stmt->execute([$articleId, $_SESSION['id_user']]);
        // Refresh the page to update the like count
        header("Location: detailsarticle.php?id=$articleId");
        exit();
    }

    // Récupérer les commentaires
    $stmt = $pdo->prepare('SELECT c.*, u.nom AS utilisateur FROM commentaires c JOIN utilisateurs u ON c.utilisateur_id = u.id_user WHERE c.article_id = ? ORDER BY c.date_creation DESC');
    $stmt->execute([$articleId]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'article</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($article['titre']) ?></h1>
            <img src="<?= htmlspecialchars($article['image_couverture']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="w-full h-64 object-cover rounded-lg mb-4">
            <p class="text-gray-600 mb-4"><?= htmlspecialchars($article['contenu']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Auteur:</strong> <?= htmlspecialchars($article['auteur']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Catégorie:</strong> <?= htmlspecialchars($article['categorie']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Date de création:</strong> <?= htmlspecialchars($article['date_creation']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Statut:</strong> <?= htmlspecialchars($article['status']) ?></p>

        <form method="POST" action="">
            <button type="submit" name="like" class="bg-blue-500 text-white px-4 py-2 rounded">Like</button>
        </form>

        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg mt-4" onclick="generatePDF()">Télécharger en PDF</button>

        

                <?php foreach ($commentaires as $commentaire): ?>
                    <div class="bg-white p-4 rounded-lg shadow mb-4">
                        <div class="flex items-center mb-2">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" src="path/to/user/profile/image.jpg" alt="<?php echo htmlspecialchars($commentaire['utilisateur']); ?>">
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($commentaire['utilisateur']); ?></p>
                                <p class="text-xs text-gray-600"><?php echo htmlspecialchars($commentaire['date_creation']); ?></p>
                            </div>
                        </div>
                        <p class="text-gray-700"><?php echo htmlspecialchars($commentaire['contenu']); ?></p>
                    </div>
                <?php endforeach; ?>

                <a href="./dashboard.php" class="block mb-0 mt-4 px-4 py-2 text-blue-700 bg-purple-500 rounded-lg">Home</a>
    </div>
    <script>
                    function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Add title
            doc.setFontSize(22);
            doc.text("<?php echo "ART & CULTURE"; ?>", 10, 10);
            doc.text("<?php echo addslashes($article['titre']); ?>", 10, 20);

            // Add cover image
            doc.addImage("<?php echo $article['image_couverture']; ?>", "JPEG", 10, 30, 180, 100);

          
            doc.setFontSize(16);
            doc.text("Auteur: <?php echo addslashes($article['auteur']); ?>", 10, 140);

            doc.text("Catégorie: <?php echo addslashes($article['categorie']); ?>", 10, 150);

            doc.text("Date de création: <?php echo addslashes($article['date_creation']); ?>", 10, 160);

            
            doc.setFontSize(12);
            doc.text("<?php echo addslashes($article['contenu']); ?>", 10, 170, { maxWidth: 190 });

            doc.save("<?php echo addslashes($article['titre']); ?>.pdf");
        }
    </script>
</body>
</html>