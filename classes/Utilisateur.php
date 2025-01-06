<?php

class Utilisateur extends User {
    public function sInscrire(PDO $pdo): bool {
        try {
            $query = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id) VALUES (:nom, :email, :mot_de_passe, :role_id)";
            $stmt = $pdo->prepare($query);
            return $stmt->execute([
                'nom' => $this->nom,
                'email' => $this->email,
                'mot_de_passe' => $this->motDePasse,
                'role_id' => $this->role_id,
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de l'inscription : " . $e->getMessage());
            return false;
        }
    }

    public function AfficherArticles(PDO $pdo, int $page = 1, int $limit = 6): array {
        $offset = ($page - 1) * $limit;

        try {
            // Requête SQL pour récupérer les articles paginés
            $query = "SELECT a.id, a.titre AS title, c.nom AS category, a.contenu AS excerpt, u.nom AS author, 
                             a.date_creation AS date, a.image_couverture AS image
                      FROM articles a
                      JOIN categories c ON a.categorie_id = c.id
                      JOIN utilisateurs u ON a.auteur_id = u.id_user
                      WHERE a.status = 'publie'
                      ORDER BY a.date_creation DESC
                      LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            // Récupérer les articles
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer le nombre total d'articles publiés
            $totalQuery = "SELECT COUNT(*) AS total FROM articles WHERE status = 'publie'";
            $totalStmt = $pdo->query($totalQuery);
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