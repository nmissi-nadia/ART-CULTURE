<?php
// class User {
//     protected int $id_user;
//     protected string $nom;
//     protected string $email;
//     protected string $motDePasse;
//     protected int $role_id;

//     public function __construct( string $nom, string $email, string $motDePasse, int $role_id) {
//         $this->nom = $nom;
//         $this->email = $email;
//         $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
//         $this->role_id = $role_id;
//     }


//     public function seConnecter(PDO $pdo, string $email, string $password): bool {
//         try {
//             $query = "SELECT * FROM utilisateurs WHERE email = :email";
//             $stmt = $pdo->prepare($query);
//             $stmt->execute(['email' => $email]);
//             $user = $stmt->fetch(PDO::FETCH_ASSOC);

//             if ($user && password_verify($password, $user['mot_de_passe'])) {
//                 $this->id_user = $user['id_user'];
//                 $this->nom = $user['nom'];
//                 $this->email = $user['email'];
//                 $this->role_id = $user['role_id'];
//                 $_SESSION['id_user'] = $this->id_user;
//                 $_SESSION['nom'] = $this->nom;
//                 $_SESSION['email'] = $this->email;
//                 $_SESSION['role_id'] = $this->role_id;
//                 return true;
//             }
//             return false;
//         } catch (PDOException $e) {
//             error_log("Erreur de connexion : " . $e->getMessage());
//             return false;
//         }
//     }
//     public function Infos_User(PDO $pdo, int $id): ?array {
//         try {
//             $query = "SELECT * FROM utilisateurs WHERE id_user = :id";
//             $stmt = $pdo->prepare($query);
//             $stmt->bindParam(':id', $id, PDO::PARAM_INT);
//             $stmt->execute();

//             return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
//         } catch (PDOException $e) {
//             error_log("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
//             return null;
//         }
//     }

//     public function modifierutili(PDO $pdo, int $id, array $data): bool {
//         try {
//             $query = "UPDATE utilisateurs 
//                       SET nom = :nom, email = :email, bio = :bio, photo_profil = :photo_profil 
//                       WHERE id_user = :id";

//             $stmt = $pdo->prepare($query);
//             return $stmt->execute([
//                 ':id' => $id,
//                 ':nom' => $data['nom'],
//                 ':email' => $data['email'],
//                 ':bio' => $data['bio'],
//                 ':photo_profil' => $data['photo_profil'] ?? null,
//             ]);
//         } catch (PDOException $e) {
//             error_log("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
//             return false;
//         }
//     }

//     public function setId(int $id): void {
//         $this->id_user = $id;
//     }
//     public function getId(): string {
//         return $this->id_user;
//     }
//     public function getNom(): string {
//         return $this->nom;
//     }
//     public function getRole(): string {
//         return $this->role_id;
//     }

//     public function getEmail(): string {
//         return $this->email;
//     }
// }



// Classe de base Utilisateur
class User {
    protected int $id_user;
    protected string $nom;
    protected string $email;
    protected string $motDePasse;
    protected string $role;
    protected ?string $photoProfil;
    protected ?string $bio;
    protected ?DateTime $dateInscription;
    protected ?DateTime $derniereConnexion;

    public function __construct(string $nom, string $email, string $motDePasse, string $role, ?string $photoProfil = null, ?string $bio = null) {
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
        $this->role = $role;
        $this->photoProfil = $photoProfil;
        $this->bio = $bio;
        $this->dateInscription = null;
        $this->derniereConnexion = null;
    }
    // Getteurs et Setteurs
    public function getIdUser(): int {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): void {
        $this->id_user = $id_user;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): void {
        $this->motDePasse = $motDePasse;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function getPhotoProfil(): string {
        return $this->photoProfil;
    }

    public function setPhotoProfil(string $photoProfil): void {
        $this->photoProfil = $photoProfil;
    }

    public function getBio(): string {
        return $this->bio;
    }

    public function setBio(string $bio): void {
        $this->bio = $bio;
    }

    public function getDateInscription(): DateTime {
        return $this->dateInscription;
    }

    public function setDateInscription(DateTime $dateInscription): void {
        $this->dateInscription = $dateInscription;
    }

    public function getDerniereConnexion(): DateTime {
        return $this->derniereConnexion;
    }

    public function setDerniereConnexion(DateTime $derniereConnexion): void {
        $this->derniereConnexion = $derniereConnexion;
    }

    public function seConnecter(PDO $pdo, string $email, string $motDePasse): bool {
        try {
            $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
                $this->id_user = $user['id_user'];
                $this->nom = $user['nom'];
                $this->email = $user['email'];
                $this->motDePasse = $user['mot_de_passe'];
                $this->role = $user['role_id'];
                
    
                // Mettre à jour la dernière connexion
                $stmt = $pdo->prepare('UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id_user = ?');
                $stmt->execute([$this->id_user]);
    
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la connexion: ' . $e->getMessage());
        }
    }

    public function mettreAJourProfil(PDO $pdo): void {
        try {
            $stmt = $pdo->prepare('UPDATE utilisateurs SET nom = ?, email = ?, mot_de_passe = ?, photo_profil = ?, bio = ? WHERE id_user = ?');
            $stmt->execute([$this->nom, $this->email, $this->motDePasse, $this->photoProfil, $this->bio, $this->id_user]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
        }
    }
}

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
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, photo_profil, bio, date_inscription, derniere_connexion) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
            return $stmt->execute([$this->nom, $this->email, $this->motDePasse, $this->role, $this->photoProfil, $this->bio]);
        } catch (Exception $e) {
            throw new Exception('Erreur lors de l\'inscription: ' . $e->getMessage());
        }
    }
}

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

// Classe Article
class Article {
    public int $id_article;
    public string $titre;
    public string $contenu;
    public DateTime $datePublication;
    public string $imageCouverture;
    public array $tags;
    public string $status;
    public DateTime $dateCreation;
    public DateTime $dateModification;
    public int $vues;
}

// Classe Categorie
class Categorie {
    public int $categorieId;
    public string $nom;
    public string $description;
}

// Classe Tag
class Tag {
    public int $id;
    public string $nom;
}

// Classe Commentaire
class Commentaire {
    public int $id;
    public string $contenu;
    public DateTime $dateCreation;
}

// Classe Favorie
class Favorie {
    public int $id;
    public DateTime $dateAjout;
}






?>