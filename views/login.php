<?php

    require_once ("../config/db_connect.php");
    require_once ("../classes/User.classe.php");
    require_once ("../classes/Utilisateur.php");
    require_once ("../classes/Auteur.php");
    require_once ("../classes/Admin.php");
    // Vérification que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connct'])) {
    // Récupération des données du formulaire
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    session_start();
    // Validation des champs
    if (empty($email) || empty($motDePasse)) {
        die('Tous les champs sont requis.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('L\'adresse email n\'est pas valide.');
    }
    $user = new User('', $email, $motDePasse, 0);

    try {
        if ($user->seConnecter($pdo, $email, $motDePasse)) {
        
            echo "<script>console.log('" . $_SESSION['id_user'] . "');</script>";
            echo 'Connexion réussie. Redirection en cours...';
            if($_SESSION['role_id']==1){
                header('Refresh: 2; URL=./admin/dashboard.php');
            }elseif ($_SESSION['role_id']==2) {
                header('Refresh: 2; URL=./auteur/dashboard.php');
            }else {
                header('Refresh: 2; URL=./utilisateur/home.php');
            }
            
            exit();
        } else {
            die('Email ou mot de passe incorrect.');
        }
    } catch (Exception $e) {
        error_log('Erreur lors de la connexion : ' . $e->getMessage());
        die('Une erreur est survenue. Veuillez réessayer plus tard.');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtCulture - Authentification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.0/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
</head>
<body class="bg-black overflow-hidden min-h-screen flex justify-center items-center">
<div id="canvas-container" class="fixed inset-0 -z-10"></div>
    <!-- Container principal -->
    <div class="h-auto flex flex-col  md:flex-row">
        <!-- Section gauche décorative -->
        <div class="hidden md:flex md:w-1/2 rounded-xl bg-gradient-to-br from-pink-600 to-blue-500 p-12 relative">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="items-center relative z-10 text-white">
                <h1 class="text-4xl font-bold mb-6">L'Art & La Culture</h1>
                <p class="text-xl mb-8">Rejoignez notre communauté de passionnés d'art et de culture.</p>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Partagez vos passions artistiques</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Découvrez des contenus uniques</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Échangez avec des passionnés</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section droite avec les formulaires -->
        <div class="h-auto md:w-1/2 flex items-center justify-center p-8 bg-white/10 backdrop-blur-lg rounded-xl p-8 w-full max-w-md 
                    shadow-[0_0_50px_rgba(192,38,211,0.2)] transform hover:scale-105 transition-all duration-500">
            <div class="w-full max-w-md">
                <!-- Onglets -->
                <div class="flex mb-8">
                    <button id="loginTab" class="w-1/2 pb-4 text-lg font-semibold text-center border-b-2 border-blue-500">Connexion</button>
                    <button id="registerTab" class="w-1/2 pb-4 text-lg font-semibold text-center border-b-2 border-transparent text-gray-500">Inscription</button>
                </div>

                <!-- Formulaire de connexion -->
                <form action="./login.php" method="POST" id="loginForm" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input type="password" name="mot_de_passe" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <!-- <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-700">Se souvenir de moi</label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Mot de passe oublié ?</a>
                    </div> -->
                    <button type="submit" name="connct" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Se connecter
                    </button>
                </form>

                <!-- Formulaire d'inscription (caché par défaut) -->
                <form action="./inscription.php" method="POST" id="registerForm" class="hidden space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                        <input type="text" name="nom" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input type="password" name="password" required minlength="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Sélectionnez un rôle</label>
                        <select name="role" id="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="3">Utilisateur</option>
                            <option value="2">Auteur</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmez le mot de passe</label>
                        <input type="password" name="copass" required minlength="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" required class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-700">
                            J'accepte les <a href="#" class="text-blue-600 hover:text-blue-500">conditions d'utilisation</a>
                        </label>
                    </div>
                    <button type="submit" name="inscri" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        S'inscrire
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Gestion des onglets
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        loginTab.addEventListener('click', () => {
            loginTab.classList.add('border-blue-500', 'text-gray-900');
            loginTab.classList.remove('border-transparent', 'text-gray-500');
            registerTab.classList.remove('border-blue-500', 'text-gray-900');
            registerTab.classList.add('border-transparent', 'text-gray-500');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
        });

        registerTab.addEventListener('click', () => {
            registerTab.classList.add('border-blue-500', 'text-gray-900');
            registerTab.classList.remove('border-transparent', 'text-gray-500');
            loginTab.classList.remove('border-blue-500', 'text-gray-900');
            loginTab.classList.add('border-transparent', 'text-gray-500');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        });

        // Validation du formulaire d'inscription
        // const registerFormEl = document.getElementById('registerForm');
        // registerFormEl.addEventListener('submit', (e) => {
        //     e.preventDefault();
        //     const password = registerFormEl.querySelector('input[type="password"]').value;
        //     const confirmPassword = registerFormEl.querySelectorAll('input[type="password"]')[1].value;

        //     if (password !== confirmPassword) {
        //         alert('Les mots de passe ne correspondent pas');
        //         return;
        //     }

        //     // Ici, vous pouvez ajouter le code pour envoyer les données au serveur
        //     console.log('Formulaire d\'inscription soumis');
        // });

        // Validation du formulaire de connexion
        // const loginFormEl = document.getElementById('loginForm');
        // loginFormEl.addEventListener('submit', (e) => {
        //     e.preventDefault();
        //     // Ici, vous pouvez ajouter le code pour envoyer les données au serveur
        //     console.log('Formulaire de connexion soumis');
        // });

        // Animation des champs de formulaire
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('transform', 'scale-105');
                input.classList.add('ring-2', 'ring-blue-200');
            });

            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('transform', 'scale-105');
                input.classList.remove('ring-2', 'ring-blue-200');
            });
        });
    </script>
     <script>
        // Three.js Background Animation
        const container = document.getElementById('canvas-container');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

        renderer.setSize(window.innerWidth, window.innerHeight);
        container.appendChild(renderer.domElement);

        // Create particles
        const geometry = new THREE.BufferGeometry();
        const particleCount = 5000;
        const positions = new Float32Array(particleCount * 3);
        const colors = new Float32Array(particleCount * 3);

        for(let i = 0; i < particleCount * 3; i += 3) {
            positions[i] = (Math.random() - 0.5) * 10;
            positions[i + 1] = (Math.random() - 0.5) * 10;
            positions[i + 2] = (Math.random() - 0.5) * 10;

            colors[i] = Math.random() * 0.5 + 0.5;
            colors[i + 1] = Math.random() * 0.3;
            colors[i + 2] = Math.random() * 0.5 + 0.5;
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        const material = new THREE.PointsMaterial({
            size: 0.02,
            vertexColors: true,
            transparent: true,
            opacity: 0.8
        });

        const particles = new THREE.Points(geometry, material);
        scene.add(particles);

        camera.position.z = 5;

        // Animation
        function animate() {
            requestAnimationFrame(animate);

            particles.rotation.x += 0.0005;
            particles.rotation.y += 0.0005;

            renderer.render(scene, camera);
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // GSAP Animations
        gsap.from(".auth-container", {
            duration: 1.5,
            opacity: 0,
            y: 50,
            ease: "power4.out"
        });

        gsap.from(".auth-input", {
            duration: 1,
            opacity: 0,
            x: -50,
            stagger: 0.2,
            ease: "power3.out",
            delay: 0.5
        });

        animate();
    </script>
</body>
</html>