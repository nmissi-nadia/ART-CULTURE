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
}

?>