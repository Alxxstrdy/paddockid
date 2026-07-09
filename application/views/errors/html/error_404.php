<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Paddock Lost | PaddockID</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #05070c;
            background-image: radial-gradient(circle at 50% 50%, rgba(255, 24, 24, 0.04) 0%, transparent 60%);
        }
        .font-syne { font-family: 'Syne', sans-serif; }
        .glass-card {
            background: rgba(15, 22, 38, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
    </style>
</head>
<body class="text-slate-100 antialiased min-h-screen flex flex-col justify-between">

    <header class="p-6 lg:px-12 border-b border-white/[0.02] bg-slate-950/10 backdrop-blur-sm w-full">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="#">                
                <img src="Logo_PaddockID.png" alt="PaddockID Logo" class="h-7 w-auto object-contain">
            </a>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center px-4 py-12 text-center relative z-10">
        
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20 z-0">
            <div class="w-96 h-96 bg-red-600/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-md w-full glass-card p-8 sm:p-10 rounded-2xl border border-white/[0.06] shadow-2xl relative z-10">
            
            <div class="w-16 h-16 bg-red-500/10 border border-red-500/20 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6 transform -rotate-6 hover:rotate-0 transition-transform duration-300 shadow-lg shadow-red-500/5">
                <i data-lucide="triangle-alert" class="w-8 h-8"></i>
            </div>

            <h1 class="font-syne text-7xl sm:text-8xl font-extrabold uppercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-white via-slate-200 to-slate-500 leading-none mb-2">
                404
            </h1>
            
            <h2 class="text-sm font-semibold text-red-400 uppercase tracking-widest mb-4 font-mono">
                [ BOX BOX BOX • PADDOCK LOST ]
            </h2>

            <p class="text-xs sm:text-sm text-slate-400 leading-relaxed mb-8">
                Waduh, sepertinya kamu keluar lintasan. Halaman yang kamu cari tidak ditemukan atau telah dipindahkan.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="#" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-5 py-3 rounded-xl shadow-lg shadow-red-600/10 transition-all duration-300 active:scale-95 group">
                    <i data-lucide="home" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                
                <button onclick="window.history.back()" class="inline-flex items-center justify-center gap-2 bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white text-xs font-semibold px-5 py-3 rounded-xl border border-white/[0.06] transition-all duration-300 active:scale-95">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali Sebelumnya</span>
                </button>
            </div>

        </div>

    </main>

    <footer class="py-6 text-center text-[10px] text-slate-600 tracking-wide border-t border-white/[0.02] bg-slate-950/10">
        &copy; 2026 PaddockID. Race Control Error System.
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>