<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Maintenance - Segera Kembali!</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Palet Warna Kustom */
        :root {
            --color-terracotta: #E2725B;
            --color-raw-green: #6A994E;
            --color-dark-charcoal: #1A1A1A;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--color-dark-charcoal);
        }

        /* Animasi */
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }

        @keyframes fadeInMoveUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes rotateGear {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .animated-pulse {
            animation: pulse 2s infinite ease-in-out;
        }

        .animated-fade-in-up {
            animation: fadeInMoveUp 1s ease-out forwards;
        }

        .animated-rotate-gear {
            animation: rotateGear 10s linear infinite;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen text-white p-4">
    <div class="text-center max-w-lg mx-auto">
        <div class="mb-8">
            <svg class="w-32 h-32 mx-auto text-raw-green animated-rotate-gear" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                <path d="M15.42 16.58L10.83 12l4.59-4.59L14 6l-6 6 6 6z" fill="var(--color-terracotta)" />
            </svg>
        </div>

        <h1 class="text-5xl md:text-6xl font-extrabold mb-4 text-terracotta animated-fade-in-up">
            Sedang Dalam Perbaikan
        </h1>
        <p class="text-lg md:text-xl mb-8 text-white animated-fade-in-up" style="animation-delay: 0.3s;">
            Situs kami sedang mengalami pemeliharaan rutin untuk meningkatkan pengalaman Anda.
        </p>
        <p class="text-md md:text-lg mb-10 text-white animated-fade-in-up" style="animation-delay: 0.6s;">
            Kami akan segera kembali! Terima kasih atas kesabaran Anda.
        </p>

        <div class="flex justify-center space-x-3 mb-8 animated-pulse" style="animation-delay: 0.9s;">
            <div class="w-3 h-3 bg-raw-green rounded-full"></div>
            <div class="w-3 h-3 bg-terracotta rounded-full"></div>
            <div class="w-3 h-3 bg-raw-green rounded-full"></div>
        </div>

        <p class="text-sm text-gray-400 animated-fade-in-up" style="animation-delay: 1.2s;">
            &copy; 2025 Merra Coffee and Talk. Hak Cipta Dilindungi.
        </p>
    </div>

    <script>
        // Contoh JavaScript sederhana untuk kustomisasi, tidak wajib untuk animasi ini
        // Anda bisa menambahkan fitur seperti hitung mundur di sini jika diinginkan.
        // const countdownElement = document.getElementById('countdown');
        // let timeLeft = 3600; // 1 jam dalam detik

        // function updateCountdown() {
        //     const minutes = Math.floor(timeLeft / 60);
        //     const seconds = timeLeft % 60;
        //     countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        //     timeLeft--;

        //     if (timeLeft < 0) {
        //         clearInterval(countdownInterval);
        //         countdownElement.textContent = "Situs Kembali Online!";
        //     }
        // }

        // const countdownInterval = setInterval(updateCountdown, 1000);
    </script>
</body>
</html>