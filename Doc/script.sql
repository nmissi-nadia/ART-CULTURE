-- Création de la base de données
CREATE DATABASE art_culture_v2;
USE art_culture_v2;

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
    image_couverture VARCHAR(255) NOT NULL,
    auteur_id INT NOT NULL,
    categorie_id INT NOT NULL,
    status ENUM('en_attente', 'publie', 'rejete') DEFAULT 'en_attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    vues INT DEFAULT 0,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id_user) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

-- Table des tags
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);

-- Table de liaison entre articles et tags
CREATE TABLE tags_articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id)
);

-- Table des commentaires
CREATE TABLE commentaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_user)
);

-- Table des favoris
CREATE TABLE favoris (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    article_id INT NOT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_user) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);
-- Insertions des rôles de base
INSERT INTO roles (nom) VALUES 
('admin'),
('auteur'),
('utilisateur');

-- Insertions des catégories initiales

INSERT INTO categories (nom, id_admin, description_cat) VALUES 
('Peinture', 1, 'Articles sur la peinture, les techniques et les artistes'),
('Musique', 1, 'Actualités musicales, critiques et découvertes'),
('Littérature', 1, 'Romans, poésie et analyses littéraires'),
('Cinéma', 1, 'Critiques de films et actualités du 7e art'),
('Photographie', 1, 'Art photographique et techniques'),
('Théâtre', 1, 'Actualités théâtrales et critiques de pièces'),
('Architecture', 1, 'Design et histoire architecturale');

-- Création d'un administrateur par défaut (mot de passe: Admin123!)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id) VALUES 
('Admin', 'admin@artculture.fr', '$2y$10$jC2EIGJmg7FEJLG0DVsihu/6xxhTH11.A0VzznyGHTKYiW5M5g/xG', 1);

-- Création de quelques tags
INSERT INTO tags (nom) VALUES 
('peinture'),
('musique'),
('littérature'),
('cinéma'),
('photographie'),
('théâtre'),
('architecture'),
('histoire'),
('art moderne'),
('art contemporain'),
('art classique'),
('art abstrait'),
('art figuratif'),
('art urbain'),
('art conceptuel'),
('art minimaliste'),
('art cinétique'),
('art numérique'),
('art brut'),
('art naïf'),
('art singulier'),
('art premier'),
('art populaire'),
('art primitif'),
('art académique'),
('art déco'),
('art nouveau'),
('art roman'),
('art gothique'),
('art baroque'),
('art rococo'),
('art classique'),
('art moderne'),
('art contemporain'),
('art urbain'),
('art conceptuel'),
('art minimaliste'),
('art cinétique'),
('art numérique'),
('art brut'),
('art naïf'),
('art singulier'),
('art premier'),
('art populaire'),
('art primitif'),
('art académique'),
('art déco'),
('art nouveau'),
('art roman');

-- Création de quelques articles

INSERT INTO articles (titre, contenu, image_couverture, auteur_id, categorie_id) VALUES 
('Les secrets de la Joconde', 'Découvrez les mystères de la Joconde de Léonard de Vinci.', 'joconde.jpg', 2, 1),
('Les 10 albums incontournables de 2021', 'Une sélection des meilleurs albums de l''année.', 'albums.jpg', 1, 2),
('Les grands classiques de la littérature française', 'Découvrez ou redécouvrez les chefs-d''oeuvre de la littérature française.', 'livres.jpg', 2, 3),
('Les films à ne pas manquer en 2021', 'Les blockbusters et les films indépendants à voir absolument.', 'films.jpg', 5, 4),
('Les plus belles photos de l''année', 'Une sélection des plus belles photos de l''année.', 'photos.jpg', 5, 5),
('Les pièces de théâtre à voir en 2021', 'Les pièces classiques et contemporaines à ne pas manquer.', 'theatre.jpg', 6, 6),
('Les chefs-d''oeuvre de l''architecture moderne', 'Les bâtiments emblématiques de l''architecture moderne.', 'architecture.jpg', 5, 7);

-- Liaison des articles avec des tags


-- Création des vues demandées

-- Vue 1: Nombre d'articles par catégorie
CREATE VIEW articles_par_categorie AS
SELECT categories.nom AS categorie, COUNT(articles.id) AS nb_articles
FROM categories
LEFT JOIN articles ON categories.id = articles.categorie_id
GROUP BY categories.id;

-- Vue 2: Nombre de commentaires par article
CREATE VIEW commentaires_par_article AS
SELECT articles.id AS id_article, articles.titre, COUNT(commentaires.id) AS nb_commentaires
FROM articles
LEFT JOIN commentaires ON articles.id = commentaires.article_id
GROUP BY articles.id;


-- Vue 3: Nombre de favoris par article
CREATE VIEW favoris_par_article AS
SELECT articles.id AS id_article, articles.titre, COUNT(favoris.id) AS nb_favoris
FROM articles
LEFT JOIN favoris ON articles.id = favoris.article_id
GROUP BY articles.id;

-- Vue 4: Nombre de vues par article
CREATE VIEW vues_par_article AS
SELECT articles.id AS id_article, articles.titre, articles.vues
FROM articles;


-- Afficher les articles les plus likés avec leur titre, le nombre de likes, et leur catégorie.
CREATE VIEW articles_plus_likes AS
SELECT 
    a.id AS article_id,
    a.titre AS titre_article,
    COUNT(f.id) AS nombre_likes,
    c.nom AS categorie
FROM articles a
JOIN favoris f ON a.id = f.article_id
JOIN categories c ON a.categorie_id = c.id
WHERE a.status = 'publie'
GROUP BY a.id, a.titre, c.nom
ORDER BY nombre_likes DESC;

-- Création d'une procédure stockée :
-- Mettre à jour automatiquement le statut is_active d’un utilisateur à 0 (banni) après une action d’administration.
CREATE PROCEDURE bannir_utilisateur(IN user_id INT)
BEGIN
    UPDATE utilisateurs
    SET status = 'banni'
    WHERE id_user = user_id;
END;
-- Requête SQL :
-- Identifier les tags les plus associés aux articles publiés au cours des 30 derniers jours, en affichant le nom du tag et le nombre d’associations.
SELECT 
    t.nom AS tag,
    COUNT(ta.tag_id) AS nombre_associations
FROM tags_articles ta
JOIN tags t ON ta.tag_id = t.id
JOIN articles a ON ta.article_id = a.id
WHERE a.status = 'publie'
AND a.date_creation >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
GROUP BY t.nom
ORDER BY nombre_associations DESC;
