-- Suppression des tables si elles existent déjà
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS roles;

-- Creation de base de donnees
create database art_culture;
use art_culture;

-- Table des rôles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    photo_profil VARCHAR(255),
    bio TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP,
    status ENUM('actif', 'inactif', 'banni') DEFAULT 'actif',
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Table des catégories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    id_admin INT NOT NULL,
    description_cat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_admin) REFERENCES utilisateurs(id_user)
);

-- Table des articles
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(150) NOT NULL,
    contenu TEXT NOT NULL,
    image_couverture VARCHAR(255),
    auteur_id INT NOT NULL,
    categorie_id INT NOT NULL,
    status ENUM('en_attente', 'publie', 'rejete') DEFAULT 'en_attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    vues INT DEFAULT 0,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

-- Insertions des rôles de base
INSERT INTO roles (nom) VALUES 
('admin'),
('auteur'),
('utilisateur');

-- Insertions des catégories initiales
INSERT INTO categories (nom, description) VALUES 
('Peinture',1, 'Articles sur la peinture, les techniques et les artistes'),
('Musique',1, 'Actualités musicales, critiques et découvertes'),
('Littérature',1, 'Romans, poésie et analyses littéraires'),
('Cinéma',1, 'Critiques de films et actualités du 7e art'),
('Photographie',1, 'Art photographique et techniques'),
('Théâtre',1, 'Actualités théâtrales et critiques de pièces'),
('Architecture',1, 'Design et histoire architecturale');

-- Création d'un administrateur par défaut (mot de passe: Admin123!)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id) VALUES 
('Admin', 'admin@artculture.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Création des vues demandées

-- Vue des derniers articles (30 derniers jours)
CREATE VIEW derniers_articles AS
SELECT 
    a.id,
    a.titre,
    a.contenu,
    a.image_couverture,
    a.date_creation,
    u.nom as auteur,
    c.nom as categorie
FROM articles a
JOIN utilisateurs u ON a.auteur_id = u.id
JOIN categories c ON a.categorie_id = c.id
WHERE a.status = 'publie'
AND a.date_creation >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
ORDER BY a.date_creation DESC;

-- Vue des statistiques des auteurs
CREATE VIEW stats_auteurs AS
SELECT 
    u.id,
    u.nom,
    COUNT(a.id) as nombre_articles,
    SUM(a.vues) as total_vues
FROM utilisateurs u
LEFT JOIN articles a ON u.id = a.auteur_id AND a.status = 'publie'
GROUP BY u.id, u.nom
ORDER BY nombre_articles DESC;

-- Requêtes statistiques demandées
-- 1. Nombre total d'articles par catégorie
CREATE VIEW articles_par_categorie AS
SELECT 
    c.nom as categorie,
    COUNT(a.id) as nombre_articles
FROM categories c
LEFT JOIN articles a ON c.id = a.categorie_id AND a.status = 'publie'
GROUP BY c.id, c.nom;

-- 2. Auteurs les plus actifs
CREATE VIEW auteurs_actifs AS
SELECT 
    u.nom,
    COUNT(a.id) as nombre_articles,
    MAX(a.date_creation) as dernier_article
FROM utilisateurs u
JOIN articles a ON u.id = a.auteur_id
WHERE a.status = 'publie'
GROUP BY u.id, u.nom
ORDER BY nombre_articles DESC
LIMIT 10;

-- 3. Moyenne d'articles par catégorie
CREATE VIEW moyenne_articles_categorie AS
SELECT 
    AVG(article_count) as moyenne_articles
FROM (
    SELECT categorie_id, COUNT(*) as article_count
    FROM articles
    WHERE status = 'publie'
    GROUP BY categorie_id
) as counts;

-- 4. Catégories sans articles
CREATE VIEW categories_vides AS
SELECT 
    c.nom,
    c.description
FROM categories c
LEFT JOIN articles a ON c.id = a.categorie_id
WHERE a.id IS NULL;
