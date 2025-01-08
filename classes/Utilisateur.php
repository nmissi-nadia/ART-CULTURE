<?php 
    // Classe Utilisateur héritée de User
class Utilisateur extends User {

    public function __construct(string $nom, string $email, string $motDePasse, int $role_id, ?string $photoProfil = null, ?string $bio = null) {
        parent::__construct($nom, $email, $motDePasse, $role_id, $photoProfil, $bio);
    }
    public function AfficherArticles(PDO $pdo, int $page, int $limit): array {
        $offset = ($page - 1) * $limit;
        $stmt = $pdo->prepare('SELECT * FROM articles WHERE auteur_id = ? LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $this->getIdUser(), PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE auteur_id = ?');
        $stmt->execute([$this->getIdUser()]);
        $total = $stmt->fetchColumn();

        return ['articles' => $articles, 'total' => $total];
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
}



?>