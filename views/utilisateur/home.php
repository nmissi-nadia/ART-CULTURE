<?php 
    require_once ("../../config/db_connect.php");
    require_once ("../../classes/User.classe.php");
    require_once ("../../classes/Utilisateur.php");
    session_start();

    if (!isset($_SESSION['id_user']) && !isset($_SESSION['role_id'])!==3) {
        header('Location: ./login.php'); 
        exit();
    }
    $utilisateur = new Utilisateur($_SESSION['nom'], $_SESSION['email'], '', $_SESSION['role_id']);
    $query = "SELECT id, nom FROM categories";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Récupérer les articles
    $page = intval($_GET['page'] ?? 1);
    $limit = 6;
    $data = $utilisateur->AfficherArticles($pdo, $page, $limit);
    $articles = $data['articles'];
    $totalArticles = $data['total'];
    $totalPages = ceil($totalArticles / $limit);
    
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles - Plateforme Culturelle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
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
                    <a href="/auteurs" class="text-gray-700 hover:text-blue-600">Auteurs</a>
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
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mes articles</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                            <hr class="my-2">
                            <a href="../lougout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Déconnexion</a>
                        </div>
                    </div>
                    <a href="../lougout.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Deconnexion
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
                    <a href="./categories" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Catégories</a>
                    <a href="./articles" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Articles</a>
                    <a href="./auteurs" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Auteurs</a>
                    <a href="./a-propos" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">À propos</a>
                    <hr class="my-2">
                    <a href="./profil" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Mon compte</a>
                    
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        
    <div id="filtrage" class="mb-8">
            <form action="home.php" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Rechercher un article..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="flex gap-4 flex-wrap">
                    <select name="category" class="px-4 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Toutes les catégories</option>
                        <?php 
                            foreach ($categories as $categorie) {
                                echo '<option value="' . $categorie['id'] . '">' . htmlspecialchars($categorie['nom']) . '</option>';
                            }
                        ?>
                    </select>
                    <select name="date" class="px-4 py-2 border rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Toutes les dates</option>
                        <option value="2023-01-01">2023</option>
                        <option value="2022-01-01">2022</option>
                        <option value="2021-01-01">2021</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filtrer</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="articles-grid">
        <?php
           
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            $date = $_GET['date'] ?? '';

            $query = "SELECT a.id, a.titre, a.contenu, a.date_creation, a.image_couverture, c.nom AS categorie
                      FROM articles a
                      JOIN categories c ON a.categorie_id = c.id
                      WHERE a.status = 'publie'";

            if ($search) {
                $query .= " AND (a.titre LIKE :search OR a.contenu LIKE :search)";
            }
            if ($category) {
                $query .= " AND a.categorie_id = :category";
            }
            if ($date) {
                $query .= " AND DATE(a.date_creation) = :date";
            }

            $stmt = $pdo->prepare($query);

            if ($search) {
                $searchParam = '%' . $search . '%';
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            }
            if ($category) {
                $stmt->bindParam(':category', $category, PDO::PARAM_INT);
            }
            if ($date) {
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            }

            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Afficher les articles filtrés
            foreach ($articles as $article) {
                echo '<div class="bg-white rounded-lg shadow-lg p-6">';
                echo '<img src="' . htmlspecialchars($article['image_couverture']) . '" alt="' . htmlspecialchars($article['titre']) . '" class="w-full h-48 object-cover rounded-lg mb-4">';
                echo '<h2 class="text-xl font-semibold mb-2">' . htmlspecialchars($article['titre']) . '</h2>';
                echo '<p class="text-gray-700 mb-4">' . htmlspecialchars(substr($article['contenu'], 0, 100)) . '...</p>';
                echo '<p class="text-sm text-gray-600">Catégorie: ' . htmlspecialchars($article['categorie']) . '</p>';
                echo '<p class="text-sm text-gray-600">Date: ' . htmlspecialchars($article['date_creation']) . '</p>';
                echo '<a href="./detailsarticle.php?id=' . $article['id'] . '" class="block mb-0 mt-4 px-4 py-2 text-blue-700">Lire la suite ...</a>';
                echo '</div>';
            }
        ?>
          
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="flex justify-center mt-6">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="px-4 py-2 mx-1 rounded border <?= $i === $page ? 'bg-blue-600 text-white' : 'border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
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
    <script>
        

        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

    </script>
</body>
</html>