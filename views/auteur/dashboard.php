<?php
   require_once ("../../config/db_connect.php");
   require_once ("../../classes/User.classe.php");
   require_once ("../../classes/Utilisateur.php");
   require_once ("../../classes/Auteur.php");
session_start();

// Vérification si l'utilisateur est connecté et a le rôle "Auteur"
echo "<script>console.log('" . $_SESSION['id_user'] . "');</script>";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['role_id'])!==2) { 
    header('Location: ../login.php'); 
    exit();
}

// Inclusion des fichiers nécessaires


try {
    // Instancier l'auteur avec les données de la session
    $auteur = new Auteur($_SESSION['nom'], $_SESSION['email'], '', $_SESSION['role_id']);

    // Récupérer les articles de l'auteur
    $page = intval($_GET['page'] ?? 1);
    $limit = 6;
    $data = $auteur->AfficherArticlesByAuteur($pdo, $_SESSION['id_user'], $page, $limit);
    $articles = $data['articles'];
    $totalArticles = $data['total'];
    $totalPages = ceil($totalArticles / $limit);
} catch (PDOException $e) {
    die('Erreur lors de la récupération des articles : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Auteur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<style>
        .header-hidden {
            transform: translateY(-100%);
        }

        header {
            position: sticky;
            top: 0;
            z-index: 50;
            transition: transform 0.3s ease-in-out;
        }
    </style>
<body class="bg-gray-100">
    <!-- En-tête -->
    <header class="bg-white shadow-md">
        <div class="bg-gradient-to-r from-purple-600 to-blue-500 text-white px-4 py-1 text-sm">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <p>Découvrez notre nouvelle section "Art contemporain"</p>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-gray-200">Newsletter</a>
                    <a href="#" class="hover:text-gray-200">Contact</a>
                </div>
            </div>
        </div>

        <nav class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <a href="/" class="flex items-center space-x-2">
                        <span class="text-2xl font-bold text-gradient bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-blue-500">
                            ArtCulture
                        </span>
                    </a>
                </div>

                <!-- Menu principal - Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/categories" class="text-gray-700 hover:text-blue-600">Catégories</a>
                    <a href="/articles" class="text-gray-700 hover:text-blue-600">Articles</a>
                    <a href="/a-propos" class="text-gray-700 hover:text-blue-600">À propos</a>
                </div>

                <!-- Boutons d'action -->
                <div class="hidden md:flex items-center space-x-4">
                    <button class="text-gray-700 hover:text-blue-600">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    <div class="relative group">
                        <button class="flex items-center space-x-1 text-gray-700 hover:text-blue-600">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            <span>Mon compte</span>
                        </button>
                        <!-- Dropdown menu -->
                        <div class="absolute right-0 w-48 mt-2 py-2 bg-white rounded-md shadow-xl hidden group-hover:block">
                            <a href="../profil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                            <a href="/mes-articles" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mes articles</a>
                            <a href="/parametres" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                            <hr class="my-2">
                            <a href="../lougout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Déconnexion</a>
                        </div>
                    </div>
                    <a href="./auteur.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Publier
                    </a>
                </div>

                <!-- Menu burger - Mobile -->
                <button class="md:hidden text-gray-700" id="mobileMenuButton">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Menu mobile -->
            <div class="md:hidden hidden" id="mobileMenu">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="/categories" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Catégories</a>
                    <a href="/articles" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Articles</a>
                    <a href="/auteurs" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Auteurs</a>
                    <a href="/a-propos" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">À propos</a>
                    <hr class="my-2">
                    <a href="../profil.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Mon compte</a>
                    <a href="./auteur.php" class="block px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Publier</a>
                    <a href="../lougout.php" class="block px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Deconnexion</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenu principal -->
    <main class="container max-w-7xl mx-auto my-8 px-4 py-8">
    <span class="mr-4 text-2xl color-purple">Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?></span>

        <div class="mb-8  mt-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           placeholder="Rechercher un article..." 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="flex gap-4 flex-wrap">
                    <select class="px-4 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Toutes les catégories</option>
                        <option value="peinture">Peinture</option>
                        <option value="musique">Musique</option>
                        <option value="litterature">Littérature</option>
                        <option value="cinema">Cinéma</option>
                    </select>
                    <select class="px-4 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="recent">Plus récents</option>
                        <option value="popular">Plus populaires</option>
                        <option value="viewed">Plus vus</option>
                    </select>
                </div>
            </div>
        </div>
            <h2 class="text-2xl font-bold">Vos Articles</h2>
            

        <!-- Grille des articles -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <img src="<?= htmlspecialchars($article['image'] ?? '/api/placeholder/800/400') ?>" 
                             alt="<?= htmlspecialchars($article['title']) ?>" 
                             class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                    <?= htmlspecialchars($article['category']) ?>
                                </span>
                                <span class="ml-2"><?= htmlspecialchars($article['date']) ?></span>
                            </div>
                            <h2 class="text-xxl font-bold text-gray-800 mb-2">
                                <?= htmlspecialchars($article['title']) ?>
                            </h2>
                            <p class="text-gray-600 mb-4">
                                <?= htmlspecialchars(substr($article['excerpt'], 0, 100)) ?>...
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun article trouvé.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-10 mb-4">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="px-4 py-2 mx-1 rounded border <?= $i === $page ? 'bg-blue-600 text-white' : 'border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>

    <!-- Pied de page -->
    <footer class="bg-gradient-to-r from-purple-600 to-blue-500 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- À propos -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold">À propos d'ArtCulture</h3>
                    <p class="text-gray-400">Une plateforme dédiée à l'art et à la culture, créée par des passionnés pour des passionnés.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i data-lucide="facebook" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i data-lucide="twitter" class="w-5 h-5"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i data-lucide="instagram" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>

                <!-- Catégories -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold">Catégories</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Peinture</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Musique</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Littérature</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Cinéma</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Photographie</a></li>
                    </ul>
                </div>

                <!-- Liens utiles -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold">Liens utiles</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Guide de publication</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Conditions d'utilisation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Politique de confidentialité</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Nous contacter</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold">Newsletter</h3>
                    <p class="text-gray-400">Restez informé de nos dernières actualités</p>
                    <form class="space-y-2">
                        <input type="email" placeholder="Votre email" class="w-full px-4 py-2 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:border-blue-500">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            S'abonner
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; 2024 ArtCulture. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    <script>
        // Initialisation des icônes Lucide
        lucide.createIcons();

        // Gestion du menu mobile
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        let isMenuOpen = false;

        mobileMenuButton.addEventListener('click', () => {
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                mobileMenu.classList.remove('hidden');
                mobileMenuButton.innerHTML = '<i data-lucide="x" class="w-6 h-6"></i>';
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.innerHTML = '<i data-lucide="menu" class="w-6 h-6"></i>';
            }
            lucide.createIcons();
        });

        // Gestion du scroll pour le header
        let lastScroll = 0;
        const header = document.querySelector('header');

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll <= 0) {
                header.classList.remove('header-hidden');
                return;
            }

            if (currentScroll > lastScroll && !header.classList.contains('header-hidden')) {
                // Scroll vers le bas
                header.classList.add('header-hidden');
            } else if (currentScroll < lastScroll && header.classList.contains('header-hidden')) {
                // Scroll vers le haut
                header.classList.remove('header-hidden');
            }
            lastScroll = currentScroll;
        });
    </script>
</body>
</html>

                