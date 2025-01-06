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
    protected string $photoProfil;
    protected string $bio;
    protected DateTime $dateInscription;
    protected DateTime $derniereConnexion;


    public function __construct(string $nom, string $email, string $motDePasse, string $role) {
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
        $this->role = $role;
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

    public function seConnecter(): bool {
        // Implémentation de la connexion
        return true;
    }

    public function mettreAJourProfil(): void {
        // Implémentation de la mise à jour du profil
    }
}

// Classe Utilisateur héritée de User
class Utilisateur extends User {
    public function afficherArticles(): void {
        // Implémentation pour afficher les articles
    }

    public function filtrerArticles(): void {
        // Implémentation pour filtrer les articles
    }

    public function sInscrire(): void {
        // Implémentation de l'inscription
    }
}

// Classe Administrateur héritée de User
class Administrateur extends User {
    public function creeCategories(): void {
        // Implémentation de la création de catégories
    }

    public function modifierCategories(): void {
        // Implémentation de la modification de catégories
    }

    public function supprimerCategories(): void {
        // Implémentation de la suppression de catégories
    }

    public function creeTags(): void {
        // Implémentation de la création de tags
    }

    public function modifierTags(): void {
        // Implémentation de la modification de tags
    }

    public function supprimerTags(): void {
        // Implémentation de la suppression de tags
    }

    public function consulterProfils(): void {
        // Implémentation de la consultation des profils
    }

    public function validerArticles(int $articleId): bool {
        // Implémentation de la validation d'un article
        return true;
    }

    public function rejeterArticle(int $articleId): bool {
        // Implémentation du rejet d'un article
        return false;
    }

    public function bannirUtilisateur(int $userId): bool {
        // Implémentation du bannissement d'un utilisateur
        return true;
    }

    public function supprimerCommentaire(int $commentaireId): bool {
        // Implémentation de la suppression d'un commentaire
        return true;
    }
}

// Classe Auteur héritée de User
class Auteur extends Utilisateur {
    public function creerArticle(string $titre, string $contenu, int $categorieId, array $tags, string $image): bool {
        // Implémentation de la création d'un article
        return true;
    }

    public function modifierArticle(int $articleId, string $titre, string $contenu, int $categorieId, array $tags, string $image): bool {
        // Implémentation de la modification d'un article
        return true;
    }

    public function supprimerArticle(int $articleId): void {
        // Implémentation de la suppression d'un article
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