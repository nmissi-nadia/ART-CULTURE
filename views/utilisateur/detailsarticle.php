<?php
session_start();
require_once ("../../config/db_connect.php");
require_once ("../../classes/User.classe.php");
require_once ("../../classes/Utilisateur.php");

if (!isset($_SESSION['id_user']) || $_SESSION['role_id'] !== 3) {
    header('Location: ../login.php');
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
        header('Location: home.php');
        exit();
    }

    // Ajouter un commentaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $commentaire = htmlspecialchars(trim($_POST['commentaire']));
        if (!empty($commentaire)) {
            $stmt = $pdo->prepare('INSERT INTO commentaires (article_id, utilisateur_id, commentaire, date_commentaire) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$articleId, $_SESSION['id_user'], $commentaire]);
        }
    }

    // Ajouter un like et aux favoris
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
        $stmt = $pdo->prepare('INSERT INTO likes (article_id, utilisateur_id) VALUES (?, ?)');
        $stmt->execute([$articleId, $_SESSION['id_user']]);

        $stmt = $pdo->prepare('INSERT INTO favoris (article_id, utilisateur_id) VALUES (?, ?)');
        $stmt->execute([$articleId, $_SESSION['id_user']]);
    }

    // Récupérer les commentaires
    $stmt = $pdo->prepare('SELECT c.*, u.nom AS utilisateur FROM commentaires c JOIN utilisateurs u ON c.utilisateur_id = u.id_user WHERE c.article_id = ? ORDER BY c.date_commentaire DESC');
    $stmt->execute([$articleId]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// Fonction pour générer le PDF
function generatePDF($article) {
    require_once '../../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();
    $html = "<h1>{$article['titre']}</h1>";
    $html .= "<p>{$article['contenu']}</p>";
    $html .= "<p>Auteur: {$article['auteur']}</p>";
    $html .= "<p>Catégorie: {$article['categorie']}</p>";
    $mpdf->WriteHTML($html);
    $mpdf->Output();
}

if (isset($_GET['action']) && $_GET['action'] === 'download') {
    generatePDF($article);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'article</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($article['titre']); ?></h1>
        <p class="mb-4"><?php echo htmlspecialchars($article['contenu']); ?></p>
        <p class="mb-4">Auteur: <?php echo htmlspecialchars($article['auteur']); ?></p>
        <p class="mb-4">Catégorie: <?php echo htmlspecialchars($article['categorie']); ?></p>

        <form method="POST" action="">
            <button type="submit" name="like" class="bg-blue-500 text-white px-4 py-2 rounded">Like</button>
        </form>

        <a href="?id=<?php echo $articleId; ?>&action=download" class="bg-green-500 text-white px-4 py-2 rounded">Télécharger en PDF</a>

        <h2 class="text-xl font-bold mt-8 mb-4">Commentaires</h2>
        <form method="POST" action="">
            <textarea name="commentaire" class="w-full px-3 py-2 border rounded mb-4" placeholder="Ajouter un commentaire"></textarea>
            <button type="submit" name="comment" class="bg-blue-500 text-white px-4 py-2 rounded">Commenter</button>
        </form>

        <?php foreach ($commentaires as $commentaire): ?>
            <div class="bg-white p-4 rounded shadow mb-4">
                <p class="font-bold"><?php echo htmlspecialchars($commentaire['utilisateur']); ?></p>
                <p><?php echo htmlspecialchars($commentaire['commentaire']); ?></p>
                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($commentaire['date_commentaire']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>