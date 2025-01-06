<?php
    require_once ("../../config/db_connect.php");
    require_once ("../../classes/User.classe.php");
    require_once ("../../classes/Admin.php");

session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../login.php'); 
    exit();
}
try {
    // Instanciation de l'administrateur
    $admin = new Admin($_SESSION['nom'], $_SESSION['email'], '', $_SESSION['role_id']);
    $admin->setId($_SESSION['id_user']);
    $utilsRole = $admin->utilisateurpaRole($pdo);
    // $articles = $admin->getArticles($pdo);      

    // Pagination
   // Pagination
   $totalArticles = $admin->getTotalArticles($pdo);
   $articlesPerPage = 6;
   $totalPages = ceil($totalArticles / $articlesPerPage);
   $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
   $offset = ($currentPage - 1) * $articlesPerPage;
   $articles = $admin->getArticles($pdo, $offset, $articlesPerPage);
    
    $categories = $admin->getCategories($pdo);
    $totalUsers = $admin->TotalUsers($pdo);
    $totalCategories = $admin->TotalCategories($pdo);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['ajoutcat'])) {
            $nom = $_POST['nom'];
            $description_cat = $_POST['description_cat'];
            // $admin = new Admin($_SESSION['nom'], $_SESSION['email'], '', $_SESSION['id_user']);

            if ($admin->creerCategorie($pdo, $nom, $description_cat)) {
                header('Location: ./dashboard.php');
                exit();
            } else {
                header('Location: ./dashboard.php?error=1');
                exit();
            }
    }   
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Mobile Header -->
    <header class="lg:hidden bg-gray-900 text-white p-4 flex justify-between items-center">
        <h1 class="font-bold">Dashboard Admin</h1>
        <button onclick="toggleMobileMenu()" class="p-2 hover:bg-gray-800 rounded-lg">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden">
        <div class="bg-gray-900 text-white w-64 h-full p-4 transform transition-transform -translate-x-full" id="mobile-sidebar">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-bold">Menu</h2>
                <button onclick="toggleMobileMenu()" class="p-2 hover:bg-gray-800 rounded-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="space-y-4">
                <button onclick="setActiveTab('users')" class="flex items-center w-full p-3 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-users mr-3"></i>
                    <span>Utilisateurs</span>
                </button>
                <button onclick="setActiveTab('articles')" class="flex items-center w-full p-3 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Articles</span>
                </button>
                <button onclick="setActiveTab('categories')" class="flex items-center w-full p-3 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-folder-open mr-3"></i>
                    <span>Catégories</span>
                </button>
                <div>
                    <a href="../lougout.php" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Déconnexion</a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <aside id="desktop-sidebar particules" class="hidden lg:block fixed top-0 left-0 h-full w-64 bg-gray-900 text-white transition-all duration-300 z-30">
        <div class="flex justify-between items-center p-4 border-b border-gray-800">
            <h1 class="font-bold">Dashboard Admin</h1>
            <button onclick="toggleSidebar()" class="p-2 hover:bg-gray-800 rounded-lg">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <nav class="p-4 space-y-2">
            <button onclick="setActiveTab('users')" class="flex items-center w-full p-3 rounded-lg hover:bg-gray-800">
                <i class="fas fa-users mr-3"></i>
                <span class="sidebar-text">Utilisateurs</span>
            </button>
            <button onclick="setActiveTab('articles')" class="flex items-center w-full p-3 rounded-lg hover:bg-gray-800">
                <i class="fas fa-file-alt mr-3"></i>
                <span class="sidebar-text">Articles</span>
            </button>
            <button onclick="setActiveTab('categories')" class="flex items-center w-full p-3 rounded-lg hover:bg-gray-800">
                <i class="fas fa-folder-open mr-3"></i>
                <span class="sidebar-text">Catégories</span>
            </button>
            <div>
                    <a href="../lougout.php" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Déconnexion</a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main id="main-content" class="lg:ml-64 transition-all duration-300">
        <div class="p-4 lg:p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6">
                <!-- Users Card -->
                <div class="bg-white rounded-lg shadow p-4 lg:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500">Total Utilisateurs</p>
                            <h3 class="text-xl lg:text-2xl font-bold mt-2"><?= $totalUsers ?></h3>
                        </div>
                        <i class="fas fa-users text-blue-500 text-xl lg:text-2xl"></i>
                    </div>
                    <p class="text-sm text-green-500 mt-4">+5 aujourd'hui</p>
                </div>

                <!-- Articles Card -->
                <div class="bg-white rounded-lg shadow p-4 lg:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500">Total Articles</p>
                            <h3 class="text-xl lg:text-2xl font-bold mt-2"><?= $totalArticles ?></h3>
                        </div>
                        <i class="fas fa-file-alt text-blue-500 text-xl lg:text-2xl"></i>
                    </div>
                    <?php
                $articlesEnAttente = $admin->ArticlesEnAttente($pdo);
                ?>
                    <p class="text-sm text-orange-500 mt-4"><?= $articlesEnAttente ?></p>
               
                
                </div>

                <!-- Categories Card -->
                <div class="bg-white rounded-lg shadow p-4 lg:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500">Catégories</p>
                            <h3 class="text-xl lg:text-2xl font-bold mt-2"><?= $totalCategories ?></h3>
                        </div>
                        <i class="fas fa-folder-open text-blue-500 text-xl lg:text-2xl"></i>
                    </div>
                    <p class="text-sm text-blue-500 mt-4">6 actives</p>
                </div>
            </div>

            <!-- Content Table -->
            <div id="utilisateur" class="bg-white rounded-lg shadow">
                <div class="p-4 lg:p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h3 class="text-lg font-medium">Liste des Utilisateurs</h3>
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                            <input type="text" placeholder="Rechercher..." 
                                   class="px-4 py-2 border rounded-lg flex-grow sm:flex-grow-0">
                            <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 whitespace-nowrap">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Ajouter
                            </button>
                        </div>
                    </div>
                </div>
                <?php if (!empty($utilsRole)): ?>
                    <?php foreach ($utilsRole as $role => $users): ?>
                        <div class="overflow-x-auto">
                        <h3 class="ml-10 text-xl font-semibold text-gray-800 mb-4"><?= htmlspecialchars($role) ?></h3>
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Statut</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['id_user']) ?></td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['nom']) ?></td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['email']) ?></td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell"><?= htmlspecialchars($user['date_inscription']) ?></td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $user['status'] === 'actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                <?= htmlspecialchars($user['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                        <a href="profilutilisateur.php?id=<?= $user['id_user'] ?>" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                            <form action="supprimerUse.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= $user['id_user'] ?>">
                                                <button type="submit" name="supprimeutilis" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun utilisateur trouvé.</p>
                    <?php endif; ?>
                <!-- Mobile Pagination -->
                <div class="flex justify-between items-center p-4 lg:p-6 border-t border-gray-200">
                    <button class="px-4 py-2 border rounded text-sm text-gray-600 hover:bg-gray-50">Précédent</button>
                    <span class="text-sm text-gray-600">Page 1 sur 5</span>
                    <button class="px-4 py-2 border rounded text-sm text-gray-600 hover:bg-gray-50">Suivant</button>
                </div>
            </div>
            
            
            <div id="article" class="bg-white rounded-lg shadow mt-10">
                <div class="p-4 lg:p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h3 class="text-lg font-medium">Liste des Articles</h3>
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                            <input type="text" placeholder="Rechercher..." 
                                class="px-4 py-2 border rounded-lg flex-grow sm:flex-grow-0">
                            <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 whitespace-nowrap">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Ajouter
                            </button>
                        </div>
                    </div>
                </div>
                <?php if (!empty($articles)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                        <?php foreach ($articles as $article): ?>
                            
                            <a href="./detailsarticle.php?id=<?= $article['id'] ?>" class="bg-transparent rounded-lg shadow p-4 block">
                                    <!-- <div class="bg-white rounded-lg shadow p-4"> -->
                                    <img src="<?= htmlspecialchars($article['image_couverture']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="w-full h-48 object-cover rounded-lg mb-4">
                                    <h3 class="text-lg font-medium mb-2"><?= htmlspecialchars($article['titre']) ?></h3>
                                    <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars(substr($article['contenu'], 0, 100)) ?>...</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600"><?= htmlspecialchars($article['date_creation']) ?></span>
                                        <div class="flex space-x-2">
                                            <button class="px-2 py-1 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700" onclick="changeStatus(<?= $article['id'] ?>, 'publie')">Publier</button>
                                            <button class="px-2 py-1 bg-yellow-600 text-white rounded-lg text-xs hover:bg-yellow-700" onclick="changeStatus(<?= $article['id'] ?>, 'en_attente')">En attente</button>
                                            <button class="px-2 py-1 bg-red-600 text-white rounded-lg text-xs hover:bg-red-700" onclick="changeStatus(<?= $article['id'] ?>, 'rejete')">Rejeter</button>
                                        </div>
                                    </div>
                                <!-- </div> -->
                            </a>    
                            
                        <?php endforeach; ?>
                    </div>
                    <!-- Pagination -->
                    <div class="flex justify-between items-center p-4 lg:p-6 border-t border-gray-200">
                        <button class="px-4 py-2 border rounded text-sm text-gray-600 hover:bg-gray-50" onclick="changePage('prev')">Précédent</button>
                        <span class="text-sm text-gray-600">Page <?= $currentPage ?> sur <?= $totalPages ?></span>
                        <button class="px-4 py-2 border rounded text-sm text-gray-600 hover:bg-gray-50" onclick="changePage('next')">Suivant</button>
                    </div>
                <?php else: ?>
                    <p>Aucun article trouvé.</p>
                <?php endif; ?>
            </div>

            <script>
            function changeStatus(articleId, status) {
                // AJAX request to change the status of the article
                fetch('modifier_article_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: articleId, status: status }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors du changement de statut');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

                function changePage(direction) {
                    let currentPage = <?= $currentPage ?>;
                    let totalPages = <?= $totalPages ?>;
                    if (direction === 'prev' && currentPage > 1) {
                        currentPage--;
                    } else if (direction === 'next' && currentPage < totalPages) {
                        currentPage++;
                    }
                    window.location.href = `?page=${currentPage}`;
                }
                </script>


            <div id="categorie" class="bg-white rounded-lg shadow mt-10">
                <div class="p-4 lg:p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h3 class="text-lg font-medium">Gestion des Catégories</h3>
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                            <input type="text" placeholder="Rechercher..." 
                                class="px-4 py-2 border rounded-lg flex-grow sm:flex-grow-0">
                            <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 whitespace-nowrap" onclick="openModal()">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Ajouter
                            </button>
                        </div>
                    </div>
                </div>
                    <?php if (!empty($categories)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($categories as $categorie): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($categorie['id']) ?></td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($categorie['nom']) ?></td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($categorie['description_cat']) ?></td>
                                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                            <button name="modifiercategorie" class="text-blue-600 hover:text-blue-900" onclick="openEditModal(<?= $categorie['id'] ?>, '<?= htmlspecialchars($categorie['nom']) ?>', '<?= htmlspecialchars($categorie['description_cat']) ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="supprimecategory.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');" style="display:inline;">
                                                <input type="hidden" name="category_id" value="<?= $categorie['id'] ?>">
                                                <button type="submit" name="supprimercategorie" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Aucune catégorie trouvée.</p>
                    <?php endif; ?>
            </div>
        </div>
    </main>
            <!-- Modal pour Ajout d'une nv cat -->
            <div id="modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-lg font-medium mb-4">Ajouter une nouvelle catégorie</h2>
                        <form action="#" method="POST">
                            <div class="mb-4">
                                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                                <input type="text" name="nom" id="nom" class="mt-1 px-4 py-2 border rounded-lg w-full" required>
                            </div>
                            <div class="mb-4">
                                <label for="description_cat" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description_cat" id="description_cat" class="mt-1 px-4 py-2 border rounded-lg w-full" rows="4"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 mr-2" onclick="closeModal()">Annuler</button>
                                <button type="submit" name="ajoutcat"  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                <!-- Modal pour Modification des  categorie -->
            <div id="editModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                        <h2 class="text-lg font-medium mb-4">Modifier la catégorie</h2>
                        <form id="editCategoryForm" action="modcategory.php" method="POST">
                            <input type="hidden" name="category_id" id="editCategoryId">
                            <div class="mb-4">
                                <label for="editCategoryName" class="block text-sm font-medium text-gray-700">Nom</label>
                                <input type="text" name="nom" id="editCategoryName" class="mt-1 px-4 py-2 border rounded-lg w-full" required>
                            </div>
                            <div class="mb-4">
                                <label for="editCategoryDescription" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description_cat" id="editCategoryDescription" class="mt-1 px-4 py-2 border rounded-lg w-full" rows="4" required></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 mr-2" onclick="closeEditModal()">Annuler</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
    </div>
            <script>
                    function openModal() {
                        document.getElementById('modal').classList.remove('hidden');
                    }
                    function closeModal() {
                        document.getElementById('modal').classList.add('hidden');
                    }
           
                function openEditModal(id, name, description) {
                    document.getElementById('editCategoryId').value = id;
                    document.getElementById('editCategoryName').value = name;
                    document.getElementById('editCategoryDescription').value = description;
                    document.getElementById('editModal').classList.remove('hidden');
                }

                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }
                
    
    

        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const overlay = document.getElementById('mobile-menu');
            const sidebar = document.getElementById('mobile-sidebar');
            
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    sidebar.classList.remove('-translate-x-full');
                }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }

        // Toggle Desktop Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('desktop-sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');
            
            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                mainContent.classList.remove('lg:ml-64');
                mainContent.classList.add('lg:ml-20');
                sidebarTexts.forEach(text => text.classList.add('hidden'));
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                mainContent.classList.remove('lg:ml-20');
                mainContent.classList.add('lg:ml-64');
                sidebarTexts.forEach(text => text.classList.remove('hidden'));
            }
        }

        // Populate Table
        function populateTable() {
            const tbody = document.getElementById('table-body');
            tbody.innerHTML = tableData.map(item => `
               
            `).join('');
        }

        // Set Active Tab
        function setActiveTab(tab) {
            const buttons = document.querySelectorAll('nav button');
            buttons.forEach(button => button.classList.remove('bg-blue-600'));
            event.currentTarget.classList.add('bg-blue-600');
            
            if (window.innerWidth < 1024) {
                toggleMobileMenu();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            populateTable();
        });
    </script>
    
</body>
</html>