<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karya Tidak Ditemukan - RUPANA</title>
    <style>
        :root {
            /* Warna Resmi Sesuai Logo RUPANA */
            --rupana-teal: #2E8A85;
            --rupana-clay: #C07A53;
            --rupana-dark: #1E4D4A;
            --bg-smooth: #F9FBFB;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background-color: var(--bg-smooth);
            color: var(--rupana-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .error-wrapper {
            text-align: center;
            max-width: 650px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        /* --- VISUAL GEOMETRIS 404 BARU --- */
        .canvas-404 {
            position: relative;
            width: 100%;
            height: 220px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .text-404 {
            font-size: 11rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -2px;
            background: linear-gradient(135deg, var(--rupana-teal), var(--rupana-clay));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.15;
            user-select: none;
        }

        /* Elemen Ornamen Anyaman Abstrak Melingkar (Mirip filosofi logo) */
        .kriya-orbit {
            position: absolute;
            border: 3px solid var(--rupana-teal);
            border-radius: 50%;
            animation: spinOrbit 25s linear infinite;
            opacity: 0.7;
        }

        .orbit-1 {
            width: 180px;
            height: 180px;
            border-top-color: transparent;
            border-bottom-color: var(--rupana-clay);
        }

        .orbit-2 {
            width: 130px;
            height: 130px;
            border-right-color: transparent;
            border-left-color: var(--rupana-clay);
            animation-direction: reverse;
            animation-duration: 15s;
        }

        /* Node Titik Presisi */
        .craft-node {
            position: absolute;
            width: 12px;
            height: 12px;
            background-color: var(--rupana-clay);
            border-radius: 50%;
            box-shadow: 0 0 15px var(--rupana-clay);
            animation: pulseNode 2s ease-in-out infinite alternate;
        }

        /* --- TYPOGRAPHY & INTERFACE --- */
        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--rupana-dark);
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 1.1rem;
            color: #5A7573;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Tombol Modern Menyesuaikan Aksen Logo */
        .btn-rupana {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 36px;
            background-color: var(--rupana-teal);
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 25px rgba(46, 138, 133, 0.25);
        }

        .btn-rupana:hover {
            background-color: #246D69;
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(46, 138, 133, 0.35);
        }

        .btn-rupana svg {
            transition: transform 0.3s ease;
        }

        .btn-rupana:hover :not(svg) {
            color: #FFFFFF;
        }
        
        .btn-rupana:hover svg {
            transform: translateX(-4px);
        }

        /* --- ANIMASI ENGINE --- */
        @keyframes spinOrbit {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulseNode {
            0% { transform: scale(0.9); opacity: 0.6; }
            100% { transform: scale(1.2); opacity: 1; }
        }

        /* Background grid kriya minimalis statis */
        .bg-grid-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-image: radial-gradient(var(--rupana-teal) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.03;
            z-index: 1;
        }
    </style>
</head>
<body>

<div class="bg-grid-pattern"></div>

<div class="error-wrapper" id="interactiveScene">
    <div class="canvas-404">
        <div class="text-404">404</div>
        <div class="kriya-orbit orbit-1"></div>
        <div class="kriya-orbit orbit-2"></div>
        <div class="craft-node" style="top: 25%; left: 38%;"></div>
        <div class="craft-node" style="bottom: 30%; right: 36%; background-color: var(--rupana-teal); box-shadow: 0 0 15px var(--rupana-teal);"></div>
    </div>

    <h1>Lapak Tidak Ditemukan</h1>
    <p>Tautan URL toko kreatif yang Anda tuju belum terdaftar atau salah ketik susunan alamatnya, nih. Mari kembali ke galeri utama.</p>
    
    <a href="<?= base_url(); ?>" class="btn-rupanas btn-rupana">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali ke Galeri RUPANA
    </a>
</div>

<script>
    // Efek interaktif arah kursor yang smooth & elegan (tidak lebay)
    document.addEventListener('mousemove', (e) => {
        const scene = document.getElementById('interactiveScene');
        const x = (window.innerWidth / 2 - e.clientX) * 0.015;
        const y = (window.innerHeight / 2 - e.clientY) * 0.015;
        
        scene.style.transform = `translate3d(${x}px, ${y}px, 0)`;
    });
</script>

</body>
</html>