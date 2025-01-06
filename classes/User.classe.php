<?php
class User {
    protected int $id_user;
    protected string $nom;
    protected string $email;
    protected string $motDePasse;
    protected int $role_id;

    public function __construct( string $nom, string $email, string $motDePasse, int $role_id) {
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
        $this->role_id = $role_id;
    }


    public function seConnecter(PDO $pdo, string $email, string $password): bool {
        try {
            $query = "SELECT * FROM utilisateurs WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                $this->id_user = $user['id_user'];
                $this->nom = $user['nom'];
                $this->email = $user['email'];
                $this->role_id = $user['role_id'];
                $_SESSION['id_user'] = $this->id_user;
                $_SESSION['nom'] = $this->nom;
                $_SESSION['email'] = $this->email;
                $_SESSION['role_id'] = $this->role_id;
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            return false;
        }
    }
    public function Infos_User(PDO $pdo, int $id): ?array {
        try {
            $query = "SELECT * FROM utilisateurs WHERE id_user = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
            return null;
        }
    }

    public function modifierutili(PDO $pdo, int $id, array $data): bool {
        try {
            $query = "UPDATE utilisateurs 
                      SET nom = :nom, email = :email, bio = :bio, photo_profil = :photo_profil 
                      WHERE id_user = :id";

            $stmt = $pdo->prepare($query);
            return $stmt->execute([
                ':id' => $id,
                ':nom' => $data['nom'],
                ':email' => $data['email'],
                ':bio' => $data['bio'],
                ':photo_profil' => $data['photo_profil'] ?? null,
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function setId(int $id): void {
        $this->id_user = $id;
    }
    public function getId(): string {
        return $this->id_user;
    }
    public function getNom(): string {
        return $this->nom;
    }
    public function getRole(): string {
        return $this->role_id;
    }

    public function getEmail(): string {
        return $this->email;
    }
}

?>