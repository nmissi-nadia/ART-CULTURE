<?php 
// require_once ("./User.classe.php");
    class Admin extends User {

        public function utilisateurpaRole(PDO $pdo): array {
            try {
                $query = "SELECT u.id_user, u.nom,u.date_inscription,u.status, u.email, r.nom AS role 
                          FROM utilisateurs u
                          JOIN roles r ON u.role_id = r.id
                          ORDER BY r.nom, u.nom";
    
                $stmt = $pdo->query($query);
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                // Organiser les utilisateurs par rôles
                $utilsRole = [];
                foreach ($users as $user) {
                    $role = $user['role'];
                    if (!isset($utilsRole[$role])) {
                        $utilsRole[$role] = [];
                    }
                    $utilsRole[$role][] = $user;
                }
    
                return $utilsRole;
            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
                return [];
            }
        }

        public function creerCategorie(PDO $pdo, string $nom, string $description): bool {
            try {
                $query = "INSERT INTO categories (nom, id_admin, description_cat) VALUES (:nom, :id_admin, :description_cat)";
                $stmt = $pdo->prepare($query);
                return $stmt->execute([
                    'nom' => $nom,
                    'id_admin' => $this->getId(),
                    'description_cat' => $description,
                ]);
            } catch (PDOException $e) {
                error_log("Erreur lors de la création de la catégorie : " . $e->getMessage());
                return false;
            }
        }
    
        public function modifierCategorie(PDO $pdo, int $categorieId, string $nom, string $description): bool {
            try {
                $query = "UPDATE categories SET nom = :nom, description_cat = :description_cat WHERE id = :id";
                $stmt = $pdo->prepare($query);
                return $stmt->execute([
                    'id' => $categorieId,
                    'nom' => $nom,
                    'description_cat' => $description,
                ]);
            } catch (PDOException $e) {
                error_log("Erreur modification catégorie : " . $e->getMessage());
                return false;
            }
        }
    
        public function supprimerCategorie(PDO $pdo, int $categorieId): bool {
            try {
                $query = "DELETE FROM categories WHERE id = :id";
                $stmt = $pdo->prepare($query);
                return $stmt->execute(['id' => $categorieId]);
            } catch (PDOException $e) {
                error_log("Erreur suppression catégorie : " . $e->getMessage());
                return false;
            }
        }
    
        public function consulterProfils(PDO $pdo): array {
            try {
                $query = "SELECT * FROM utilisateurs";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erreur lors de la consultation des profils : " . $e->getMessage());
                return [];
            }
        }
    
        public function validerArticle(PDO $pdo, int $articleId): bool {
            try {
                $query = "UPDATE articles SET status = 'publie' WHERE id = :id";
                $stmt = $pdo->prepare($query);
                return $stmt->execute(['id' => $articleId]);
            } catch (PDOException $e) {
                error_log("Erreur validation article : " . $e->getMessage());
                return false;
            }
        }
        public function getArticles(PDO $pdo, int $offset, int $limit): array {
            try {
                $query = "SELECT * FROM articles LIMIT :offset, :limit";
                $stmt = $pdo->prepare($query);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erreur lors de la consultation des articles : " . $e->getMessage());
                return [];
            }
        }
        public function getTotalArticles(PDO $pdo): int {
            try {
                $query = "SELECT COUNT(*) FROM articles";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                return (int) $stmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("Erreur lors de la consultation du nombre total d'articles : " . $e->getMessage());
                return 0;
            }
        }
            public function ArticlesEnAttente(PDO $pdo): int {
                try {
                    $query = "SELECT COUNT(*) FROM articles where status = 'en_attente'";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    return (int) $stmt->fetchColumn();
                } catch (PDOException $e) {
                    error_log("Erreur lors de la consultation du nombre total d'articles : " . $e->getMessage());
                    return 0;
                }
            }

        public function getCategories(PDO $pdo): array {
            try {
                $query = "SELECT * FROM categories";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Erreur lors de la consultation des catégories : " . $e->getMessage());
                return [];
            }
        }
        public function TotalUsers(PDO $pdo): int {
            try {
                $query = "SELECT COUNT(*) FROM utilisateurs";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                return (int) $stmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("Erreur lors de la consultation du nombre total d'utilisateurs : " . $e->getMessage());
                return 0;
            }
        }
        
        public function TotalCategories(PDO $pdo): int {
            try {
                $query = "SELECT COUNT(*) FROM categories";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                return (int) $stmt->fetchColumn();
            } catch (PDOException $e) {
                error_log("Erreur lors de la consultation du nombre total de catégories : " . $e->getMessage());
                return 0;
            }
        }
    }
    
?>