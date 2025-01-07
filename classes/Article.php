<?php 
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
?>