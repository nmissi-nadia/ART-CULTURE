<?php
session_start();
    require_once ("../../config/db_connect.php");
    require_once ("../../classes/User.classe.php");
    require_once ("../../classes/Administrateur.php");

    
    if (!isset($_SESSION['id_user']) && !isset($_SESSION['role_id'])!==1) { 
        header('Location: ../login.php'); 
        exit();
    }
    
    if (!isset($_GET['id'])) {
        header('Location: dashboard.php');
        exit();
    }

    $articleId = (int)$_GET['id'];

    // Récupérer les commentaires
    $stmt = $pdo->prepare('SELECT c.*, u.nom AS utilisateur FROM commentaires c JOIN utilisateurs u ON c.utilisateur_id = u.id_user WHERE c.article_id = ? ORDER BY c.date_creation DESC');
    $stmt->execute([$articleId]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle comment deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
        $commentId = intval($_POST['comment_id']);
        $stmt = $pdo->prepare('DELETE FROM commentaires WHERE id = ?');
        $stmt->execute([$commentId]);
        header('Location: detailsarticle.php?id=' . $articleId);
        exit();
    }
    
    
    
    try {
        // Lire les données de l'article à partir de la base de données
        $query = "SELECT a.*, u.nom AS auteur, c.nom AS categorie FROM articles a
                  JOIN utilisateurs u ON a.auteur_id = u.id_user
                  JOIN categories c ON a.categorie_id = c.id
                  WHERE a.id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $articleId]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$article) {
            header('Location: dashboard.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $articleId = $data['id'];
            $status = $data['status'];
        
            try {
                $query = "UPDATE articles SET status = :status WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':status' => $status,
                    ':id' => $articleId
                ]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                error_log("Erreur lors de la mise à jour du statut de l'article : " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
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
        <title>Détails de l'article</title>
        
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
    <div class="container mx-auto p-4 w-1/2 justify-self-center">
            <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($article['titre']) ?></h1>
            <img src="<?= htmlspecialchars($article['image_couverture']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="w-full h-64 object-cover rounded-lg mb-4">
            <p class="text-gray-600 mb-4"><?= htmlspecialchars($article['contenu']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Auteur:</strong> <?= htmlspecialchars($article['auteur']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Catégorie:</strong> <?= htmlspecialchars($article['categorie']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Date de création:</strong> <?= htmlspecialchars($article['date_creation']) ?></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Statut:</strong> <?= htmlspecialchars($article['status']) ?></p>
            <div class="flex space-x-2 mt-4">
                <button class="px-2 py-1 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700" onclick="changeStatus(<?= $article['id'] ?>, 'publie')">Publier</button>
                <button class="px-2 py-1 bg-yellow-600 text-white rounded-lg text-xs hover:bg-yellow-700" onclick="changeStatus(<?= $article['id'] ?>, 'en_attente')">En attente</button>
                <button class="px-2 py-1 bg-red-600 text-white rounded-lg text-xs hover:bg-red-700" onclick="changeStatus(<?= $article['id'] ?>, 'rejete')">Rejeter</button>
            </div>

            <h2 class="text-xl font-bold mt-8 mb-4">Commentaires</h2>
        <?php foreach ($commentaires as $commentaire): ?>
            <div class="bg-white p-4 rounded shadow mb-4">
                <p class="font-bold"><?php echo htmlspecialchars($commentaire['utilisateur']); ?></p>
                <p><?php echo htmlspecialchars($commentaire['commentaire']); ?></p>
                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($commentaire['date_commentaire']); ?></p>
                <form method="POST" action="" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                    <input type="hidden" name="comment_id" value="<?php echo $commentaire['id']; ?>">
                    <button type="submit" name="delete_comment" class="text-red-600 hover:text-red-900">Supprimer</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    
        <div class="flex">
            <a href="./dashboard.php" class="mx-auto px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-blue-700">Retour à la page principale</a>
        </div>
        <script>
        
        </script>
    </body>
    </html>