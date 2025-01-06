<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Non Trouvée</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.0/gsap.min.js"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 overflow-hidden bg-black">
    <div class="absolute inset-0">
        <div id="particles" class="absolute inset-0"></div>
    </div>

    <div class="relative z-10 text-center">
        <h1 class="error-text text-[200px] font-bold leading-none bg-clip-text text-transparent 
                   bg-gradient-to-r from-purple-400 to-pink-600">
            404
        </h1>
        
        <div class="glitch-wrapper mt-8">
            <h2 class="glitch text-4xl font-bold text-white mb-6" data-text="Page Non Trouvée">
                Page Non Trouvée
            </h2>
        </div>

        <p class="text-gray-300 mb-12 max-w-md mx-auto text-lg">
            Plongez dans un nouvel univers artistique en retournant à notre galerie
        </p>

        <a href="./login.php" class="group relative inline-flex items-center justify-center px-8 py-4 
                          text-lg font-bold text-white transition-all duration-300 
                          bg-gradient-to-r from-purple-500 to-pink-500 rounded-full 
                          hover:from-purple-600 hover:to-pink-600 overflow-hidden">
            <span class="relative z-10">Retour à l'Accueil</span>
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-purple-600 to-pink-600 
                        opacity-0 group-hover:opacity-100 transition-opacity duration-300 
                        blur-xl group-hover:blur-2xl"></div>
        </a>
    </div>

    <style>
        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }

        .glitch {
            animation: glitch 1s linear infinite;
            position: relative;
        }

        .glitch::before,
        .glitch::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .glitch::before {
            left: 2px;
            text-shadow: -2px 0 #ff00ff;
            clip: rect(24px, 550px, 90px, 0);
            animation: glitch-anim 3s infinite linear alternate-reverse;
        }

        .glitch::after {
            left: -2px;
            text-shadow: -2px 0 #00ffff;
            clip: rect(85px, 550px, 140px, 0);
            animation: glitch-anim 2s infinite linear alternate-reverse;
        }
    </style>

    <script>
        // Particles animation
        const particles = [];
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        document.getElementById('particles').appendChild(canvas);

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        class Particle {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 3 + 1;
                this.speedX = Math.random() * 2 - 1;
                this.speedY = Math.random() * 2 - 1;
                this.color = `hsl(${Math.random() * 60 + 280}, 100%, 70%)`;
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                if (this.x > canvas.width || this.x < 0 || 
                    this.y > canvas.height || this.y < 0) {
                    this.reset();
                }
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        }

        for (let i = 0; i < 100; i++) {
            particles.push(new Particle());
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(particle => {
                particle.update();
                particle.draw();
            });
            requestAnimationFrame(animate);
        }

        animate();

        // GSAP Animations
        gsap.from(".error-text", {
            duration: 2,
            opacity: 0,
            y: 100,
            ease: "elastic.out(1, 0.3)",
            delay: 0.5
        });

        gsap.from(".glitch-wrapper", {
            duration: 1,
            opacity: 0,
            scale: 0.5,
            ease: "back.out(1.7)",
            delay: 1
        });
    </script>
</body>
</html>