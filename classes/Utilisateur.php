<?php 
    // Classe Utilisateur héritée de User
class Utilisateur extends User {
    public function afficherArticles(PDO $pdo): array {
        try {
            $stmt = $pdo->prepare('SELECT * FROM articles WHERE auteur_id = ?');
            $stmt->execute([$this->id_user]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'affichage des articles: ' . $e->getMessage());
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
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, photo_profil, bio, date_inscription, derniere_connexion) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
            return $stmt->execute([$this->nom, $this->email, $this->motDePasse, $this->role, $this->photoProfil, $this->bio]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'inscription: ' . $e->getMessage());
        }
    }
}



?>