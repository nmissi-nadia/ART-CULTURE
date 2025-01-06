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
        header('Location: dashboard.php');
        exit();
    }
    
    $articleId = (int)$_GET['id'];
    
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
            header('Location: ./home.php');
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
            
        </div>
        <div class="mt-22">
            <a href="./home.php" class="-mt-10 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-blue-700">Retour à la page principale</a>
        </div>
        <script>
        function changeStatus(articleId, status) {
        // AJAX request to change the status of the article
        fetch('detailsarticle.php?id=<?= $articleId ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: articleId, status: status }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du changement de statut');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
        </script>
    </body>
    </html>