<?php 
    // Classe Auteur héritée de User
class Auteur extends Utilisateur {
    public function creerArticle(PDO $pdo, string $titre, string $contenu, string $image_couverture, int $categorie_id): bool {
        try {
            $stmt = $pdo->prepare('INSERT INTO articles (titre, contenu, image_couverture, auteur_id, categorie_id, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            return $stmt->execute([$titre, $contenu, $image_couverture, $this->id_user, $categorie_id]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la publication de l\'article: ' . $e->getMessage());
        }
    }

    public function modifierArticle(PDO $pdo, int $articleId, string $titre, string $contenu, int $categorieId, array $tags, string $image): bool {
        try {
            $pdo->beginTransaction();

            // Mettre à jour l'article
            $stmt = $pdo->prepare('UPDATE articles SET titre = ?, contenu = ?, categorie_id = ?, image_couverture = ?, date_modification = NOW() WHERE id_article = ?');
            $stmt->execute([$titre, $contenu, $categorieId, $image, $articleId]);

            // Supprimer les tags existants
            $stmt = $pdo->prepare('DELETE FROM article_tags WHERE article_id = ?');
            $stmt->execute([$articleId]);

            // Ajouter les nouveaux tags
            foreach ($tags as $tagId) {
                $stmt = $pdo->prepare('INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)');
                $stmt->execute([$articleId, $tagId]);
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception('Erreur lors de la modification de l\'article: ' . $e->getMessage());
        }
    }

    public function supprimerArticle(PDO $pdo, int $articleId): bool {
        try {
            $stmt = $pdo->prepare('DELETE FROM articles WHERE id_article = ?');
            return $stmt->execute([$articleId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression de l\'article: ' . $e->getMessage());
        }
    }
    public function AfficherArticlesByAuteur($pdo, $iduser, $page, $limit): array {
        $offset = ($page - 1) * $limit;

        try {
            // Requête SQL pour récupérer les articles paginés
            $query = "SELECT a.id_article, a.titre AS title, c.nom AS category, a.contenu AS excerpt, u.nom AS author, 
                             a.date_creation AS date, a.image_couverture AS image
                      FROM articles a
                      JOIN categories c ON a.categorie_id = c.id
                      JOIN utilisateurs u ON a.auteur_id = u.id_user
                      WHERE a.auteur_id = :iduser
                      ORDER BY a.date_creation DESC
                      LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            // Récupérer les articles
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer le nombre total d'articles publiés
            $totalQuery = "SELECT COUNT(*) AS total FROM articles WHERE auteur_id = :iduser";
            $totalStmt = $pdo->prepare($totalQuery);
            $totalStmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
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
}

?>