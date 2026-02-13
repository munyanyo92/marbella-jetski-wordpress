/**
 * jetski-anim.js — v6 ULTRA-REALISTIC
 * ────────────────────────────────────
 * Real Yamaha action photo as a masked sprite + canvas 2D
 * particle system for water effects. No Three.js needed.
 *
 * • Real photo with CSS radial mask (no visible rectangle)
 * • CSS filter color-matched to hero dark overlay
 * • Pre-rendered soft-circle particle texture
 * • 600 particles: rooster spray, side spray, mist, foam, droplets
 * • Additive blending for glowing water FX
 * • Wake trail line
 * • Wave bob (3-layer sin) + random jumps
 * • IntersectionObserver visibility gating
 * • prefers-reduced-motion respected
 */
(function () {
  'use strict';
  console.log('[jetski] v6 ultra-real loaded');
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  if (document.readyState === 'loading')
    document.addEventListener('DOMContentLoaded', init);
  else init();

  function init () {
    var hero = document.querySelector('.hero');
    if (!hero) { console.warn('[jetski] no .hero'); return; }
    try { new Anim(hero); }
    catch (e) {
      console.error('[jetski] CRASH:', e);
      var d = document.createElement('div');
      d.style.cssText = 'position:fixed;top:8px;left:8px;background:red;color:#fff;padding:10px 16px;z-index:99999;font:13px/1.4 monospace;max-width:90vw;word-break:break-all;border-radius:6px';
      d.textContent = '[jetski] ' + e.message;
      document.body.appendChild(d);
    }
  }

  /* ═══════════════════════════════════════════════════
     A N I M A T I O N   E N G I N E
     ═══════════════════════════════════════════════════ */
  function Anim (hero) {
    var sin = Math.sin, cos = Math.cos, abs = Math.abs, PI = Math.PI;
    var R = Math.random;
    var mob = innerWidth < 768;

    /* ─── constants ─── */
    var SPRITE_W = mob ? 185 : 320;
    var SPRITE_H = SPRITE_W * 9 / 16;

    /* ═════════════════════════════════════
       P H O T O   S P R I T E
       ═════════════════════════════════════ */
    var wrap = document.createElement('div');
    var ws = wrap.style;
    ws.position = 'absolute';
    ws.left = '0';
    ws.top = '0';
    ws.zIndex = '2';
    ws.pointerEvents = 'none';
    ws.width = SPRITE_W + 'px';
    ws.height = SPRITE_H + 'px';
    ws.overflow = 'hidden';
    ws.willChange = 'transform';
    ws.transition = 'none';
    // radial mask — soft ellipse, feathers edges to transparent
    var mask = 'radial-gradient(ellipse 52% 50% at 52% 48%, rgba(0,0,0,1) 12%, rgba(0,0,0,0.7) 35%, rgba(0,0,0,0.3) 52%, transparent 68%)';
    ws.webkitMaskImage = mask;
    ws.maskImage = mask;
    // drop shadow for water reflection glow
    ws.filter = 'drop-shadow(0 18px 12px rgba(0,150,220,0.10))';

    var img = document.createElement('img');
    img.src = 'https://marbellajetski.com/wp-content/uploads/2021/05/2021-Yamaha-VXCRUISERHO-EU-Yellow-Action-001-03-1.jpg';
    img.alt = '';
    img.draggable = false;
    img.setAttribute('aria-hidden', 'true');
    var is = img.style;
    is.width = '100%';
    is.height = '100%';
    is.objectFit = 'cover';
    is.display = 'block';
    is.transform = 'scaleX(-1)';                           // flip to face right →
    is.filter = 'brightness(0.58) contrast(1.30) saturate(0.72)';   // match dark hero overlay
    is.pointerEvents = 'none';
    is.userSelect = 'none';

    wrap.appendChild(img);
    hero.appendChild(wrap);

    /* ═════════════════════════════════════
       C A N V A S   O V E R L A Y
       ═════════════════════════════════════ */
    var cv = document.createElement('canvas');
    cv.className = 'jsk-canvas';
    cv.setAttribute('aria-hidden', 'true');
    hero.appendChild(cv);
    var ctx = cv.getContext('2d');

    /* ═════════════════════════════════════
       P R E - R E N D E R   T E X T U R E S
       ═════════════════════════════════════ */
    function makeParticleTexture (size, r, g, b) {
      var c = document.createElement('canvas');
      c.width = c.height = size;
      var x = c.getContext('2d');
      var gr = x.createRadialGradient(size/2, size/2, 0, size/2, size/2, size/2);
      gr.addColorStop(0,   'rgba(' + r + ',' + g + ',' + b + ',1)');
      gr.addColorStop(0.25,'rgba(' + r + ',' + g + ',' + b + ',0.6)');
      gr.addColorStop(0.6, 'rgba(' + r + ',' + g + ',' + b + ',0.15)');
      gr.addColorStop(1,   'rgba(' + r + ',' + g + ',' + b + ',0)');
      x.fillStyle = gr;
      x.fillRect(0, 0, size, size);
      return c;
    }

    var texSpray = makeParticleTexture(64, 215, 232, 255);    // bright blue-white spray
    var texMist  = makeParticleTexture(64, 190, 215, 240);    // subtle blue mist
    var texFoam  = makeParticleTexture(48, 240, 248, 255);    // white foam
    var texDrop  = makeParticleTexture(32, 230, 245, 255);    // individual droplets

    /* ═════════════════════════════════════
       P A R T I C L E   P O O L
       ═════════════════════════════════════ */
    var PN = mob ? 300 : 600;
    var pool = [];
    for (var pi = 0; pi < PN; pi++) {
      pool.push({ on: false, x:0, y:0, vx:0, vy:0, sz:0, alpha:0, age:0, maxAge:0, tex: texSpray });
    }
    var pIdx = 0;

    function emit (x, y, vx, vy, sz, life, tex) {
      var p = pool[pIdx];
      pIdx = (pIdx + 1) % PN;
      p.on = true;
      p.x = x;  p.y = y;
      p.vx = vx; p.vy = vy;
      p.sz = sz;
      p.alpha = 1;
      p.age = 0;
      p.maxAge = life;
      p.tex = tex || texSpray;
    }

    /* ═════════════════════════════════════
       E M I T T E R S
       ═════════════════════════════════════ */

    // rooster spray — big upward plume behind the sprite
    function emitRooster (cx, cy, dt) {
      var n = ((mob ? 5 : 14) * dt) | 0;
      for (var i = 0; i < n; i++) {
        var angle = -PI/2 + (R() - 0.5) * 0.9;
        var spd = 1.8 + R() * 3.8;
        emit(
          cx - SPRITE_W * 0.32 + (R()-0.5) * 28,
          cy + SPRITE_H * 0.05,
          cos(angle) * spd * 0.45 - 0.6,
          sin(angle) * spd,
          3 + R() * 9,
          40 + (R() * 55) | 0,
          texSpray
        );
      }
    }

    // side spray — curtain fanning outward
    function emitSide (cx, cy, dt) {
      var n = ((mob ? 2 : 7) * dt) | 0;
      for (var i = 0; i < n; i++) {
        var side = R() > 0.5 ? 1 : -1;
        emit(
          cx + (R()-0.5) * SPRITE_W * 0.4,
          cy + (R()-0.3) * SPRITE_H * 0.25,
          side * (0.6 + R() * 1.8),
          -0.3 - R() * 1.2,
          2 + R() * 6,
          22 + (R() * 32) | 0,
          texSpray
        );
      }
    }

    // mist — large slow drifting clouds above rooster
    function emitMist (cx, cy) {
      for (var i = 0; i < (mob ? 1 : 2); i++) {
        emit(
          cx - SPRITE_W * 0.28 + (R()-0.5) * 40,
          cy - SPRITE_H * 0.35 - R() * 35,
          -0.35 - R() * 0.35,
          -0.15 - R() * 0.35,
          18 + R() * 32,
          75 + (R() * 60) | 0,
          texMist
        );
      }
    }

    // foam trail — small dots left behind on water surface
    function emitFoam (cx, cy) {
      for (var i = 0; i < 2; i++) {
        emit(
          cx - SPRITE_W * 0.15 - R() * SPRITE_W * 0.55,
          cy + SPRITE_H * 0.18 + R() * 5,
          -0.15, 0.02,
          1.5 + R() * 3,
          60 + (R() * 60) | 0,
          texFoam
        );
      }
    }

    // droplets — tiny fast arcing drops
    function emitDroplets (cx, cy, dt) {
      var n = ((mob ? 1 : 4) * dt) | 0;
      for (var i = 0; i < n; i++) {
        var angle = -PI/2 + (R()-0.5) * 1.4;
        var spd = 2.5 + R() * 4.5;
        emit(
          cx - SPRITE_W * 0.25 + (R()-0.5) * 30,
          cy - R() * 20,
          cos(angle) * spd * 0.5,
          sin(angle) * spd,
          0.8 + R() * 2.5,
          20 + (R() * 28) | 0,
          texDrop
        );
      }
    }

    // bow spray — kicked up at the front/nose
    function emitBow (cx, cy, dt) {
      var n = ((mob ? 1 : 4) * dt) | 0;
      for (var i = 0; i < n; i++) {
        var side = R() > 0.5 ? 1 : -1;
        emit(
          cx + SPRITE_W * 0.35 + R() * 15,
          cy + SPRITE_H * 0.05 - R() * 12,
          0.3 + R() * 0.6,
          -0.5 - R() * 1.5,
          1.5 + R() * 5,
          16 + (R() * 22) | 0,
          texSpray
        );
      }
    }

    // landing splash explosion
    function emitSplash (cx, cy) {
      var n = mob ? 35 : 75;
      for (var i = 0; i < n; i++) {
        var angle = R() * PI * 2;
        var spd = 1.5 + R() * 5.5;
        emit(
          cx + (R()-0.5) * SPRITE_W * 0.5,
          cy + 5,
          cos(angle) * spd,
          sin(angle) * spd - 2.0,
          2 + R() * 7,
          28 + (R() * 40) | 0,
          R() > 0.5 ? texSpray : texDrop
        );
      }
    }

    /* ═════════════════════════════════════
       W A K E   T R A I L   D A T A
       ═════════════════════════════════════ */
    var WAKE_LEN = mob ? 60 : 120;
    var wake = [];   // { x, y, alpha }
    for (var wi = 0; wi < WAKE_LEN; wi++) wake.push({ x: -999, y: 0, a: 0 });
    var wakeIdx = 0;

    /* ═════════════════════════════════════
       A N I M A T I O N   S T A T E
       ═════════════════════════════════════ */
    var W = 1, H = 1;
    var px = -SPRITE_W - 120;
    var baseY = 0;
    var py = 0;
    var phase = R() * 200;
    var jumping = false, jumpVy = 0, jumpDy = 0, jumpCD = 0;
    var speed = mob ? 1.3 : 2.0;
    var frame = 0;
    var visible = true;

    function resize () {
      W = hero.offsetWidth;
      H = hero.offsetHeight;
      cv.width  = W;
      cv.height = H;
      baseY = H * (mob ? 0.68 : 0.72) - SPRITE_H / 2;
    }
    resize();
    window.addEventListener('resize', resize);

    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (e) { visible = e[0].isIntersecting; }, { threshold: 0.04 }).observe(cv);
    }

    /* ═════════════════════════════════════
       M A I N   L O O P
       ═════════════════════════════════════ */
    var lastT = 0;

    function loop (ts) {
      requestAnimationFrame(loop);
      if (!visible) { lastT = 0; return; }

      var dt = lastT ? Math.min((ts - lastT) / 16.67, 3) : 1;
      lastT = ts;
      frame++;

      /* ── movement ── */
      px += speed * dt;
      phase += 0.025 * dt;

      /* ── wave bob (3-layer sine) ── */
      var w1 = sin(phase * 2.3)  * 7;
      var w2 = sin(phase * 3.9 + 1.1) * 3;
      var w3 = sin(phase * 1.2 + 0.5) * 4.5;
      var waveY = w1 + w2 + w3;

      /* ── jump ── */
      jumpCD += dt;
      if (!jumping && jumpCD > 320 && R() < 0.0025 * dt) {
        jumping = true;
        jumpVy = -(3 + R() * 3.5);
        jumpDy = 0;
        jumpCD = 0;
      }
      if (jumping) {
        jumpVy += 0.14 * dt;
        jumpDy += jumpVy * dt;
        if (jumpDy >= 0) {
          jumpDy = 0; jumping = false; jumpVy = 0;
          emitSplash(px + SPRITE_W / 2, baseY + waveY + SPRITE_H * 0.55);
        }
      }

      py = baseY + waveY + jumpDy;

      /* ── tilt ── */
      var futW = sin((phase + 0.04) * 2.3) * 7;
      var tilt = (futW - w1) * 0.35 + (jumping ? jumpVy * 0.7 : 0);

      /* ── move sprite ── */
      ws.transform = 'translate3d(' + (px | 0) + 'px,' + (py | 0) + 'px,0) rotate(' + tilt.toFixed(2) + 'deg)';

      /* ── shimmer (subtle brightness pulse) ── */
      var shimmer = 0.58 + sin(ts * 0.002) * 0.04;
      is.filter = 'brightness(' + shimmer.toFixed(3) + ') contrast(1.30) saturate(0.72)';

      /* ── loop reset ── */
      if (px > W + 120) {
        px = -SPRITE_W - 120;
        phase = R() * 200;
        // clear wake
        for (var cw = 0; cw < WAKE_LEN; cw++) wake[cw].a = 0;
      }

      /* ── sprite centre for emitters ── */
      var cx = px + SPRITE_W * 0.50;
      var cy = py + SPRITE_H * 0.50;
      var onScr = px > -SPRITE_W - 200 && px < W + 200;

      /* ── emit particles ── */
      if (onScr && !jumping) {
        emitRooster(cx, cy, dt);
        emitSide(cx, cy, dt);
        emitBow(cx, cy, dt);
        emitDroplets(cx, cy, dt);
        if (frame % 3 === 0) emitMist(cx, cy);
        if (frame % 2 === 0) emitFoam(cx, cy);
      }

      /* ── wake trail ── */
      if (onScr && !jumping && frame % 2 === 0) {
        wake[wakeIdx].x = cx - SPRITE_W * 0.10;
        wake[wakeIdx].y = cy + SPRITE_H * 0.22;
        wake[wakeIdx].a = 0.22;
        wakeIdx = (wakeIdx + 1) % WAKE_LEN;
      }

      /* ═══════════════════════════════
         D R A W   C A N V A S
         ═══════════════════════════════ */
      ctx.clearRect(0, 0, W, H);

      /* ── wake trail lines ── */
      ctx.globalCompositeOperation = 'source-over';
      ctx.lineWidth = mob ? 0.8 : 1.2;
      ctx.strokeStyle = 'rgba(160,195,225,0.12)';
      ctx.beginPath();
      var wStarted = false;
      for (var wj = 0; wj < WAKE_LEN; wj++) {
        var wk = wake[(wakeIdx + wj) % WAKE_LEN];
        if (wk.a < 0.01) { wStarted = false; continue; }
        if (!wStarted) { ctx.moveTo(wk.x, wk.y); wStarted = true; }
        else ctx.lineTo(wk.x, wk.y);
        wk.a *= 0.992;
      }
      ctx.stroke();

      // second V-line slightly wider
      ctx.lineWidth = mob ? 0.5 : 0.8;
      ctx.strokeStyle = 'rgba(140,180,215,0.06)';
      ctx.beginPath();
      wStarted = false;
      for (var wj2 = 0; wj2 < WAKE_LEN; wj2++) {
        var wk2 = wake[(wakeIdx + wj2) % WAKE_LEN];
        if (wk2.a < 0.01) { wStarted = false; continue; }
        if (!wStarted) { ctx.moveTo(wk2.x, wk2.y + 4); wStarted = true; }
        else ctx.lineTo(wk2.x, wk2.y + 4 + wj2 * 0.08);
      }
      ctx.stroke();

      /* ── particles (additive blending) ── */
      ctx.globalCompositeOperation = 'lighter';

      for (var pi2 = 0; pi2 < PN; pi2++) {
        var p = pool[pi2];
        if (!p.on) continue;

        p.age += dt;
        if (p.age >= p.maxAge) { p.on = false; continue; }

        var life = p.age / p.maxAge;
        p.alpha = (1 - life * life) * 0.55;

        /* physics */
        p.vy += 0.035 * dt;   // gravity
        p.vx *= 0.992;
        p.vy *= 0.992;
        p.x += p.vx * dt;
        p.y += p.vy * dt;

        /* cull off-screen or below hero */
        if (p.x < -50 || p.x > W + 50 || p.y > H + 30) { p.on = false; continue; }

        /* draw */
        if (p.alpha < 0.01) continue;
        var sz = p.sz * (1 - life * 0.28);
        var d = sz * 2;
        ctx.globalAlpha = p.alpha;
        ctx.drawImage(p.tex, p.x - sz, p.y - sz, d, d);
      }

      ctx.globalAlpha = 1;
      ctx.globalCompositeOperation = 'source-over';
    }

    requestAnimationFrame(loop);
    console.log('[jetski] v6 ultra-real running');
  }
})();
