<?php 
    // Classe Administrateur héritée de User
class Administrateur extends User {
    public function creeCategories(PDO $pdo, string $nom, string $description_cat): bool {
        try {
            $stmt = $pdo->prepare('INSERT INTO categories (nom, id_admin, description_cat, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
            return $stmt->execute([$nom, $this->id_user, $description_cat]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la création de la catégorie: ' . $e->getMessage());
        }
    }

    public function modifierCategories(PDO $pdo, int $categorieId, string $nom, string $description_cat): bool {
        try {
            $stmt = $pdo->prepare('UPDATE categories SET nom = ?, description_cat = ?, updated_at = NOW() WHERE id = ?');
            return $stmt->execute([$nom, $description_cat, $categorieId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la modification de la catégorie: ' . $e->getMessage());
        }
    }

    public function supprimerCategories(PDO $pdo, int $categorieId): bool {
        try {
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            return $stmt->execute([$categorieId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression de la catégorie: ' . $e->getMessage());
        }
    }

    public function creeTags(PDO $pdo, string $nom): bool {
        try {
            $stmt = $pdo->prepare('INSERT INTO tags (nom) VALUES (?)');
            return $stmt->execute([$nom]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la création du tag: ' . $e->getMessage());
        }
    }

    public function modifierTags(PDO $pdo, int $tagId, string $nom): bool {
        try {
            $stmt = $pdo->prepare('UPDATE tags SET nom = ? WHERE id = ?');
            return $stmt->execute([$nom, $tagId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la modification du tag: ' . $e->getMessage());
        }
    }

    public function supprimerTags(PDO $pdo, int $tagId): bool {
        try {
            $stmt = $pdo->prepare('DELETE FROM tags WHERE id = ?');
            return $stmt->execute([$tagId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression du tag: ' . $e->getMessage());
        }
    }

    public function consulterProfils(PDO $pdo): array {
        try {
            $stmt = $pdo->prepare('SELECT * FROM utilisateurs');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la consultation des profils: ' . $e->getMessage());
        }
    }

    public function validerArticles(PDO $pdo, int $articleId): bool {
        try {
            $stmt = $pdo->prepare('UPDATE articles SET status = "valide" WHERE id_article = ?');
            return $stmt->execute([$articleId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la validation de l\'article: ' . $e->getMessage());
        }
    }

    public function rejeterArticle(PDO $pdo, int $articleId): bool {
        try {
            $stmt = $pdo->prepare('UPDATE articles SET status = "rejete" WHERE id_article = ?');
            return $stmt->execute([$articleId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors du rejet de l\'article: ' . $e->getMessage());
        }
    }

    public function bannirUtilisateur(PDO $pdo, int $userId): bool {
        try {
            $stmt = $pdo->prepare('UPDATE utilisateurs SET status = "banni" WHERE id_user = ?');
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors du bannissement de l\'utilisateur: ' . $e->getMessage());
        }
    }

    public function supprimerCommentaire(PDO $pdo, int $commentaireId): bool {
        try {
            $stmt = $pdo->prepare('DELETE FROM commentaires WHERE id = ?');
            return $stmt->execute([$commentaireId]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la suppression du commentaire: ' . $e->getMessage());
        }
    }
}
?>