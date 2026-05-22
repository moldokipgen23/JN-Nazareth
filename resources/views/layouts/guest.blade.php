<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ setting('school_name', config('app.name', 'Sign in')) }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $logo = setting('school_logo')
            ? \App\Helpers\Settings::storageUrl(setting('school_logo'))
            : asset('images/logo.png');
    @endphp
    <style>
        :root { --gx: #6366f1; --gc: #22d3ee; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Figtree', system-ui, sans-serif;
            background: #05060f;
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }
        #ai-bg { position: fixed; inset: 0; z-index: 0; display: block; }
        .auth-wrap {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 18px;
        }
        .auth-brand { text-align: center; margin-bottom: 22px; }
        .auth-brand img {
            width: 76px;
            height: 76px;
            object-fit: contain;
            border-radius: 18px;
            background: rgba(255, 255, 255, .06);
            padding: 8px;
            box-shadow: 0 0 0 1px rgba(99, 102, 241, .35), 0 12px 40px rgba(99, 102, 241, .25);
        }
        .auth-brand .nm {
            margin-top: 12px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .04em;
            color: #f1f5f9;
        }
        .glass-card {
            width: 100%;
            max-width: 410px;
            background: rgba(13, 17, 33, .72);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(99, 102, 241, .28);
            border-radius: 22px;
            padding: 32px 30px;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, .03) inset,
                0 30px 70px rgba(0, 0, 0, .6),
                0 0 60px rgba(99, 102, 241, .15);
        }
        /* Re-skin the Breeze form components for the dark card */
        .glass-card label {
            color: #94a3b8 !important;
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: .03em;
        }
        .glass-card input[type=email],
        .glass-card input[type=password],
        .glass-card input[type=text] {
            width: 100%;
            background: rgba(255, 255, 255, .05) !important;
            border: 1px solid rgba(148, 163, 184, .22) !important;
            border-radius: 11px !important;
            color: #f1f5f9 !important;
            padding: 11px 14px !important;
            font-size: 14px;
            box-shadow: none !important;
            transition: border-color .2s, box-shadow .2s;
        }
        .glass-card input::placeholder { color: #64748b; }
        .glass-card input[type=email]:focus,
        .glass-card input[type=password]:focus,
        .glass-card input[type=text]:focus {
            border-color: var(--gx) !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .22) !important;
            outline: none;
        }
        .glass-card input[type=checkbox] {
            background: rgba(255, 255, 255, .08);
            border-color: rgba(148, 163, 184, .35);
        }
        .glass-card .text-sm,
        .glass-card span { color: #94a3b8; }
        .glass-card button {
            width: 100%;
            justify-content: center;
            background: linear-gradient(135deg, var(--gx), var(--gc)) !important;
            border: 0 !important;
            border-radius: 11px !important;
            padding: 12px 18px !important;
            color: #fff !important;
            font-weight: 700 !important;
            letter-spacing: .04em;
            box-shadow: 0 10px 26px rgba(99, 102, 241, .4);
            transition: transform .15s, box-shadow .2s, filter .2s;
        }
        .glass-card button:hover {
            filter: brightness(1.08);
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(99, 102, 241, .5);
        }
        .glass-card a { color: #818cf8; }
        .glass-card a:hover { color: #a5b4fc; }
        .auth-hint {
            position: relative;
            z-index: 2;
            margin-top: 20px;
            font-size: 11.5px;
            color: #475569;
            letter-spacing: .05em;
        }
    </style>
</head>
<body>
    <canvas id="ai-bg"></canvas>

    <div class="auth-wrap">
        <div class="auth-brand">
            <a href="/"><img src="{{ $logo }}" alt="{{ setting('school_name') }}"></a>
            <div class="nm">{{ setting('school_name', 'J.N. Nazareth English School') }}</div>
        </div>

        <div class="glass-card">
            {{ $slot }}
        </div>

        <div class="auth-hint">Click anywhere — the background responds.</div>
    </div>

    <script>
    (function () {
        const canvas = document.getElementById('ai-bg');
        const ctx = canvas.getContext('2d');
        let W, H, particles = [], ripples = [];
        const BASE = 80, MAX = 200, LINK = 130;

        function resize() {
            W = canvas.width = window.innerWidth;
            H = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        function makeParticle(x, y, burst) {
            const a = Math.random() * Math.PI * 2;
            const s = burst ? (Math.random() * 3 + 1.5) : (Math.random() * 0.5 + 0.15);
            return {
                x: x ?? Math.random() * W,
                y: y ?? Math.random() * H,
                vx: Math.cos(a) * s,
                vy: Math.sin(a) * s,
                r: Math.random() * 1.8 + 1,
                life: burst ? 1 : Infinity,
            };
        }

        for (let i = 0; i < BASE; i++) particles.push(makeParticle());

        // Mouse click → particle burst + expanding ripple
        window.addEventListener('click', function (e) {
            ripples.push({ x: e.clientX, y: e.clientY, r: 0, a: 0.6 });
            for (let i = 0; i < 16 && particles.length < MAX; i++) {
                particles.push(makeParticle(e.clientX, e.clientY, true));
            }
        });

        function step() {
            ctx.clearRect(0, 0, W, H);

            // Background glow
            const g = ctx.createRadialGradient(W * 0.5, H * 0.35, 0, W * 0.5, H * 0.35, Math.max(W, H) * 0.7);
            g.addColorStop(0, 'rgba(30,27,75,0.55)');
            g.addColorStop(1, 'rgba(5,6,15,0)');
            ctx.fillStyle = g;
            ctx.fillRect(0, 0, W, H);

            // Ripples
            for (let i = ripples.length - 1; i >= 0; i--) {
                const rp = ripples[i];
                rp.r += 6;
                rp.a -= 0.012;
                if (rp.a <= 0) { ripples.splice(i, 1); continue; }
                ctx.beginPath();
                ctx.arc(rp.x, rp.y, rp.r, 0, Math.PI * 2);
                ctx.strokeStyle = 'rgba(99,102,241,' + rp.a + ')';
                ctx.lineWidth = 2;
                ctx.stroke();
            }

            // Particles
            for (let i = particles.length - 1; i >= 0; i--) {
                const p = particles[i];
                p.x += p.vx; p.y += p.vy;
                if (p.life !== Infinity) {
                    p.life -= 0.012;
                    p.vx *= 0.97; p.vy *= 0.97;
                    if (p.life <= 0) { particles.splice(i, 1); continue; }
                }
                if (p.x < 0) p.x = W; if (p.x > W) p.x = 0;
                if (p.y < 0) p.y = H; if (p.y > H) p.y = 0;
                const alpha = p.life === Infinity ? 0.8 : p.life;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(165,180,252,' + alpha + ')';
                ctx.fill();
            }

            // Links between nearby particles (neural-network look)
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const d = Math.hypot(dx, dy);
                    if (d < LINK) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(99,102,241,' + (0.16 * (1 - d / LINK)) + ')';
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }

            while (particles.length < BASE) particles.push(makeParticle());

            requestAnimationFrame(step);
        }
        step();
    })();
    </script>
</body>
</html>
