<?php 
    // Classe Utilisateur héritée de User
class Utilisateur extends User {

   
    public function AfficherArticles(PDO $pdo, int $page, int $limit): array {
        $offset = ($page - 1) * $limit;
    
        try {
            // Requête SQL pour récupérer les articles paginés
            $query = "SELECT a.id, a.titre AS title, c.nom AS category, a.contenu AS excerpt, u.nom AS author, 
                             a.date_creation AS date, a.image_couverture AS image
                      FROM articles a
                      JOIN categories c ON a.categorie_id = c.id
                      JOIN utilisateurs u ON a.auteur_id = u.id_user
      
                      ORDER BY a.date_creation DESC
                      LIMIT :limit OFFSET :offset";
    
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
    
            // Récupérer les articles
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Récupérer le nombre total d'articles publiés
            $totalQuery = "SELECT COUNT(*) AS total FROM articles";
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

    public function filtrerArticles(PDO $pdo, string $critere): array {
        try {
            $stmt = $pdo->prepare('SELECT * FROM articles WHERE titre LIKE ? AND auteur_id = ?');
            $stmt->execute(['%' . $critere . '%', $this->id_user]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Erreur lors du filtrage des articles: ' . $e->getMessage());
        }
    }

    public function sInscrire(PDO $pdo): bool {
        try {
            // Check if the email already exists
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM utilisateurs WHERE email = ?');
            $stmt->execute([$this->email]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                throw new Exception('L\'adresse email est déjà utilisée.');
            }

            // Insert the new user
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, photo_profil) VALUES (?, ?, ?, ?, ?)');
            return $stmt->execute([$this->nom, $this->email, $this->motDePasse, $this->role, $this->photoProfil]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'inscription: ' . $e->getMessage());
        }
    }
    public function Articlesfavoris(PDO $pdo, int $userId): array
    {
        try {
            // Fetch favorite articles
            $query = "SELECT a.*, u.nom AS auteur, c.nom AS categorie 
                      FROM favoris f
                      JOIN articles a ON f.article_id = a.id
                      JOIN utilisateurs u ON a.auteur_id = u.id_user
                      JOIN categories c ON a.categorie_id = c.id
                      WHERE f.utilisateur_id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la récupération des articles favoris: ' . $e->getMessage());
        }
    }
}



?>