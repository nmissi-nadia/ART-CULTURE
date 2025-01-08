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
    
            if ($user) {
                if (password_verify($motDePasse, $user['mot_de_passe'])) {
                    $this->setIdUser($user['id_user']);
                    $this->setNom($user['nom']);
                    $this->setEmail($user['email']);
                    $this->setMotDePasse($user['mot_de_passe']);
                    $this->setRole($user['role_id']);
                    $this->setPhotoProfil($user['photo_profil'] ?? '');
                    $this->setBio($user['bio'] ?? '');
                    $this->setDateInscription(new DateTime($user['date_inscription']));
                    $this->setDerniereConnexion(new DateTime($user['derniere_connexion']));
    
                    $_SESSION['id_user'] = $this->getIdUser();
                    $_SESSION['nom'] = $this->getNom();
                    $_SESSION['email'] = $this->getEmail();
                    $_SESSION['role_id'] = $this->getRole();
                    $_SESSION['photo_profil'] = $this->getPhotoProfil();
                    $_SESSION['bio'] = $this->getBio();
                    $_SESSION['date_inscription'] = $this->getDateInscription();
                    $_SESSION['derniere_connexion'] = $this->getDerniereConnexion();
    
                    // Mettre à jour la dernière connexion
                    $stmt = $pdo->prepare('UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id_user = ?');
                    $stmt->execute([$this->id_user]);
    
                    return true;
                } else {
                    error_log('Mot de passe incorrect.');
                    return false;
                }
            } else {
                error_log('Utilisateur non trouvé.');
                return false;
            }
        } catch (Exception $e) {
            error_log('Erreur lors de la connexion: ' . $e->getMessage());
            return false;
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








?>