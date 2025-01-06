<?php

// require_once ("./User.classe.php");
class Auteur extends Utilisateur {
    public function AfficherArticlesByAuteur(PDO $pdo, int $authorId, int $page = 1, int $limit = 6): array {
        $offset = ($page - 1) * $limit;

        try {
            // Requête SQL pour récupérer les articles de l'auteur
            $query = "SELECT a.id, a.titre AS title, c.nom AS category, a.contenu AS excerpt, 
                             a.date_creation AS date, a.image_couverture AS image
                      FROM articles a
                      JOIN categories c ON a.categorie_id = c.id
                      WHERE a.auteur_id = :authorId
                      ORDER BY a.date_creation DESC
                      LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':authorId', $authorId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            // Récupérer les articles
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculer le nombre total d'articles de l'auteur
            $totalQuery = "SELECT COUNT(*) AS total FROM articles WHERE auteur_id = :authorId";
            $totalStmt = $pdo->prepare($totalQuery);
            $totalStmt->bindParam(':authorId', $authorId, PDO::PARAM_INT);
            $totalStmt->execute();
            $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'articles' => $articles,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles : " . $e->getMessage());
            return [
                'articles' => [],
                'total' => 0,
                'page' => $page,
                'limit' => $limit
            ];
        }
    }
     

    public function creerArticle(PDO $pdo, string $titre, string $contenu, string $imagePath, int $categorieId): bool {
        try {
            $query = "INSERT INTO articles (titre, contenu, image_couverture, auteur_id, categorie_id, status, date_creation) 
                      VALUES (:titre, :contenu, :image_couverture, :auteur_id, :categorie_id, 'en_attente', NOW())";

            $stmt = $pdo->prepare($query);
            return $stmt->execute([
                ':titre' => $titre,
                ':contenu' => $contenu,
                ':image_couverture' => $imagePath,
                ':auteur_id' => $this->id_user,
                ':categorie_id' => $categorieId,
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'article : " . $e->getMessage());
            return false;
        }
    }

    public function filtrerArticles(PDO $pdo, int $categorieId): array {
        try {
            $query = "SELECT * FROM articles WHERE categorie_id = :categorieId AND status = 'publie'";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['categorieId' => $categorieId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur filtrage articles : " . $e->getMessage());
            return [];
        }
    }
}


?>