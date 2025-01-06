<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio IT - 3D Interactive</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/0.160.0/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-text {
            background: linear-gradient(45deg, #FF3366, #FF33FF);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .tech-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .tech-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-black text-white overflow-x-hidden">
    <div id="scene-container" class="fixed inset-0 z-0"></div>
    
    <div class="relative z-10">
        <!-- Hero Section -->
        <section class="min-h-screen flex items-center justify-center p-4">
            <div class="text-center floating">
                <h1 class="text-6xl md:text-8xl font-bold gradient-text mb-6">Sarah Dubois</h1>
                <p class="text-2xl md:text-3xl text-cyan-400">Développeuse Full Stack</p>
                <div class="mt-8 flex justify-center gap-4">
                    <button class="bg-pink-600 hover:bg-pink-700 px-6 py-3 rounded-full transition-all hover:scale-105">
                        Voir mes projets
                    </button>
                    <button class="bg-purple-600 hover:bg-purple-700 px-6 py-3 rounded-full transition-all hover:scale-105">
                        Me contacter
                    </button>
                </div>
            </div>
        </section>

        <!-- Skills Section -->
        <section class="min-h-screen py-20 px-4">
            <h2 class="text-5xl font-bold text-center gradient-text mb-16">Compétences</h2>
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="tech-card rounded-2xl p-6 text-center">
                    <div class="text-4xl mb-4">🚀</div>
                    <h3 class="text-2xl font-bold mb-4 text-pink-400">Frontend</h3>
                    <ul class="space-y-2">
                        <li class="text-gray-300">React.js / Vue.js</li>
                        <li class="text-gray-300">Three.js / WebGL</li>
                        <li class="text-gray-300">CSS3 / SASS</li>
                    </ul>
                </div>
                <div class="tech-card rounded-2xl p-6 text-center">
                    <div class="text-4xl mb-4">⚡</div>
                    <h3 class="text-2xl font-bold mb-4 text-cyan-400">Backend</h3>
                    <ul class="space-y-2">
                        <li class="text-gray-300">Node.js / Express</li>
                        <li class="text-gray-300">Python / Django</li>
                        <li class="text-gray-300">MongoDB / PostgreSQL</li>
                    </ul>
                </div>
                <div class="tech-card rounded-2xl p-6 text-center">
                    <div class="text-4xl mb-4">🛠️</div>
                    <h3 class="text-2xl font-bold mb-4 text-purple-400">DevOps</h3>
                    <ul class="space-y-2">
                        <li class="text-gray-300">Docker / Kubernetes</li>
                        <li class="text-gray-300">CI/CD Pipelines</li>
                        <li class="text-gray-300">AWS / Azure</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Configuration Three.js avancée
        let scene, camera, renderer, particles;

        function init() {
            scene = new THREE.Scene();
            camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.getElementById('scene-container').appendChild(renderer.domElement);

            // Création des particules
            const particlesGeometry = new THREE.BufferGeometry();
            const particlesCount = 5000;
            const posArray = new Float32Array(particlesCount * 3);

            for(let i = 0; i < particlesCount * 3; i++) {
                posArray[i] = (Math.random() - 0.5) * 10;
            }

            particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

            const particlesMaterial = new THREE.PointsMaterial({
                size: 0.005,
                color: '#FF69B4',
                transparent: true,
                opacity: 0.8
            });

            particles = new THREE.Points(particlesGeometry, particlesMaterial);
            scene.add(particles);

            camera.position.z = 3;
        }

        // Animation
        function animate() {
            requestAnimationFrame(animate);
            particles.rotation.y += 0.001;
            particles.rotation.x += 0.001;

            // Effet de mouvement avec la souris
            if (mouseX && mouseY) {
                particles.rotation.y = mouseX * 0.0005;
                particles.rotation.x = mouseY * 0.0005;
            }

            renderer.render(scene, camera);
        }

        // Gestion de la souris
        let mouseX = 0;
        let mouseY = 0;

        document.addEventListener('mousemove', (event) => {
            mouseX = event.clientX - window.innerWidth / 2;
            mouseY = event.clientY - window.innerHeight / 2;
        });

        // Gestion du redimensionnement
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // Initialisation
        init();
        animate();

        // Animations GSAP pour les éléments
        gsap.from('.tech-card', {
            duration: 1,
            y: 100,
            opacity: 0,
            stagger: 0.2,
            scrollTrigger: {
                trigger: '.tech-card',
                start: 'top center+=100',
                toggleActions: 'play none none reverse'
            }
        });
    </script>
</body>
</html>