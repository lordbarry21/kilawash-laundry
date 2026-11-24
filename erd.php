<?php
/**
 * ============================================================================
 * FILE: erd.php
 * FUNGSI: Halaman Publik Interaktif untuk menampilkan Diagram Relasi Tabel
 *         (ERD) dan isi lengkap `database/schema.sql` tanpa terpotong.
 *         Dapat dibuka langsung lewat link dokumentasi tanpa login.
 * URL:    /erd.php atau /erd
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/helpers.php';

$schemaPath = __DIR__ . '/database/schema.sql';
$schemaSql  = is_file($schemaPath) ? (string) file_get_contents($schemaPath) : '-- File database/schema.sql tidak ditemukan.';
$sqlLines   = explode("\n", str_replace("\r\n", "\n", trim($schemaSql)));
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#f7f9fa">
  <title>ERD &amp; Skema Database — <?= e(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/tokens.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <link rel="stylesheet" href="assets/css/layout.css">
  <style>
    .erd-page {
      min-height: 100dvh;
      background-color: var(--bg);
    }

    .erd-nav {
      position: sticky;
      top: 0;
      z-index: 20;
      background-color: var(--card);
      border-bottom: 1px solid var(--hairline);
    }

    .erd-nav__inner {
      max-width: 1280px;
      margin: 0 auto;
      padding: 14px 24px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .erd-container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 32px 24px 64px;
      display: flex;
      flex-direction: column;
      gap: 28px;
    }

    /* Canvas Visual ERD dengan Garis Relasi SVG */
    .erd-canvas {
      position: relative;
      background-color: var(--card);
      border: 1px solid var(--hairline);
      border-radius: var(--r-lg);
      padding: 28px;
      overflow-x: auto;
      box-shadow: var(--shadow-card);
    }

    .erd-stage {
      position: relative;
      min-width: 920px;
      display: grid;
      grid-template-columns: 1fr 1.25fr 1fr;
      gap: 56px;
      align-items: start;
      padding: 8px 4px;
    }

    .erd-lines {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
    }

    .erd-table-card {
      position: relative;
      z-index: 2;
      background-color: var(--card);
      border: 1.5px solid var(--hairline-strong);
      border-radius: var(--r-md);
      overflow: hidden;
      transition: border-color var(--m-in) var(--ease), box-shadow var(--m-in) var(--ease);
    }

    .erd-table-card.is-highlighted {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(13, 116, 128, 0.14);
    }

    .erd-table-card__head {
      background-color: var(--ink);
      color: #ffffff;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .erd-table-card--center .erd-table-card__head {
      background-color: var(--accent);
    }

    .erd-table-card__name {
      font-family: var(--font-mono);
      font-size: 0.9375rem;
      font-weight: 500;
    }

    .erd-table-card__tag {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.8);
    }

    .erd-cols {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .erd-col {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 8px 14px;
      border-bottom: 1px solid var(--hairline);
      font-size: 0.8125rem;
    }

    .erd-col:last-child {
      border-bottom: 0;
    }

    .erd-col--fk {
      background-color: #fffbeb;
      cursor: pointer;
    }

    .erd-col--pk {
      background-color: #f0f9fa;
    }

    .erd-col__left {
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
    }

    .key-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 26px;
      padding: 2px 5px;
      border-radius: 4px;
      font-family: var(--font-mono);
      font-size: 0.6875rem;
      font-weight: 600;
    }

    .key-pill--pk { background-color: var(--accent-soft); color: var(--accent-ink); }
    .key-pill--fk { background-color: #fef3c7; color: #92400e; }
    .key-pill--uk { background-color: var(--surface); color: var(--muted); }

    .erd-col__type {
      font-family: var(--font-mono);
      font-size: 0.75rem;
      color: var(--muted);
      white-space: nowrap;
    }

    /* Kartu Penjelasan Relasi */
    .rel-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
    }

    @media (max-width: 900px) {
      .rel-grid {
        grid-template-columns: 1fr;
      }
    }

    .rel-card {
      background-color: var(--card);
      border: 1px solid var(--hairline);
      border-radius: var(--r-md);
      padding: 18px 20px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .rel-card__title {
      font-size: 0.9375rem;
      font-weight: 500;
      color: var(--ink);
    }

    .rel-card__code {
      font-family: var(--font-mono);
      font-size: 0.78rem;
      color: var(--accent-ink);
      background-color: var(--accent-soft);
      padding: 6px 10px;
      border-radius: 6px;
      overflow-x: auto;
    }

    /* Viewer SQL Lengkap */
    .sql-viewer {
      background-color: #16232a;
      color: #e6eff2;
      border-radius: 0 0 var(--r-md) var(--r-md);
      overflow-x: auto;
      padding: 20px 0;
      font-family: var(--font-mono);
      font-size: 0.8125rem;
      line-height: 1.65;
    }

    .sql-line {
      display: grid;
      grid-template-columns: 52px minmax(0, 1fr);
      padding: 0 20px 0 0;
    }

    .sql-line:hover {
      background-color: rgba(255, 255, 255, 0.04);
    }

    .sql-ln {
      text-align: right;
      padding-right: 16px;
      color: rgba(230, 239, 242, 0.35);
      user-select: none;
    }

    .sql-code {
      white-space: pre;
    }

    .sql-comment {
      color: #7dd3dc;
    }
  </style>
</head>
<body class="erd-page">
  <header class="erd-nav">
    <div class="erd-nav__inner">
      <a href="login.php" class="brand">
        <span class="brand__mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="6"/><path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><line x1="7" y1="6" x2="7.01" y2="6"/><line x1="11" y1="6" x2="11.01" y2="6"/></svg>
        </span>
        <span>
          <span class="brand__name"><?= e(APP_NAME) ?> — ERD &amp; Skema Database</span>
          <span class="brand__sub">Bari Hartanto Achmad • XII RPL 1 • ASAS Manajemen Laundry</span>
        </span>
      </a>

      <div class="btn-group">
        <a href="login.php" class="btn btn--primary btn--sm">Buka Aplikasi</a>
        <a href="https://github.com/lordbarry21/kilawash-laundry/blob/main/database/schema.sql" target="_blank" rel="noopener" class="btn btn--secondary btn--sm">Lihat di GitHub</a>
      </div>
    </div>
  </header>

  <main class="erd-container">
    <!-- 1. Header -->
    <div class="page-head">
      <div>
        <h1 class="page-head__title">Entity Relationship Diagram (ERD) &amp; Skema SQL</h1>
        <p class="page-head__desc">
          Database <code>laundry_asas</code> (MySQL 8.4 InnoDB, <code>utf8mb4</code>) dengan 3 tabel berelasi dan proteksi Foreign Key penuh.
        </p>
      </div>
      <div class="btn-group">
        <span class="badge badge--accent">3 Tabel Relasional</span>
        <span class="badge badge--masuk">2 Foreign Key Aktif</span>
      </div>
    </div>

    <!-- 2. Diagram Visual ERD Interaktif -->
    <section class="erd-canvas" aria-labelledby="erd-visual-heading">
      <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px">
        <div>
          <h2 class="card__title" id="erd-visual-heading">Diagram Relasi Antar Tabel (One-to-Many)</h2>
          <p class="card__subtitle">Arahkan kursor ke baris Foreign Key (<code>layanan_id</code> atau <code>user_id</code>) untuk menyorot relasinya.</p>
        </div>
        <span class="muted mono" style="font-size:0.75rem">users (1) ──&lt; transaksi (N) &gt;── layanan (1)</span>
      </div>

      <div class="erd-stage" id="erd-stage">
        <svg class="erd-lines" id="erd-svg" aria-hidden="true"></svg>

        <!-- TABEL 1: users -->
        <article class="erd-table-card" id="tbl-users">
          <header class="erd-table-card__head">
            <span class="erd-table-card__name">users</span>
            <span class="erd-table-card__tag">Autentikasi &amp; RBAC</span>
          </header>
          <ul class="erd-cols">
            <li class="erd-col erd-col--pk" id="col-users-id">
              <span class="erd-col__left"><span class="key-pill key-pill--pk">PK</span><strong class="mono">id</strong></span>
              <span class="erd-col__type">INT UNSIGNED AI</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">nama</span></span>
              <span class="erd-col__type">VARCHAR(100)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="key-pill key-pill--uk">UK</span><span class="mono">username</span></span>
              <span class="erd-col__type">VARCHAR(50)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">password_hash</span></span>
              <span class="erd-col__type">VARCHAR(255)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">role</span></span>
              <span class="erd-col__type">ENUM('admin','kasir')</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">avatar</span></span>
              <span class="erd-col__type">MEDIUMTEXT NULL</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">created_at</span></span>
              <span class="erd-col__type">TIMESTAMP</span>
            </li>
          </ul>
        </article>

        <!-- TABEL 2 (TENGAH): transaksi -->
        <article class="erd-table-card erd-table-card--center" id="tbl-transaksi">
          <header class="erd-table-card__head">
            <span class="erd-table-card__name">transaksi</span>
            <span class="erd-table-card__tag">Tabel Transaksi Utama</span>
          </header>
          <ul class="erd-cols">
            <li class="erd-col erd-col--pk">
              <span class="erd-col__left"><span class="key-pill key-pill--pk">PK</span><strong class="mono">id</strong></span>
              <span class="erd-col__type">INT UNSIGNED AI</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="key-pill key-pill--uk">UK</span><span class="mono">kode_transaksi</span></span>
              <span class="erd-col__type">VARCHAR(30)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">nama_pelanggan</span></span>
              <span class="erd-col__type">VARCHAR(120)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">no_hp</span></span>
              <span class="erd-col__type">VARCHAR(25)</span>
            </li>
            <li class="erd-col erd-col--fk" id="col-trx-layanan" data-rel-target="tbl-layanan">
              <span class="erd-col__left"><span class="key-pill key-pill--fk">FK</span><strong class="mono">layanan_id</strong></span>
              <span class="erd-col__type">→ layanan.id</span>
            </li>
            <li class="erd-col erd-col--fk" id="col-trx-user" data-rel-target="tbl-users">
              <span class="erd-col__left"><span class="key-pill key-pill--fk">FK</span><strong class="mono">user_id</strong></span>
              <span class="erd-col__type">→ users.id</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">berat_kg</span></span>
              <span class="erd-col__type">DECIMAL(6,2)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">harga_satuan</span></span>
              <span class="erd-col__type">INT UNSIGNED</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">total_harga</span></span>
              <span class="erd-col__type">INT UNSIGNED</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">status</span></span>
              <span class="erd-col__type">ENUM('Masuk','Proses','Selesai')</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">catatan</span></span>
              <span class="erd-col__type">TEXT NULL</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">tanggal_masuk</span></span>
              <span class="erd-col__type">DATETIME</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">tanggal_selesai</span></span>
              <span class="erd-col__type">DATETIME NULL</span>
            </li>
          </ul>
        </article>

        <!-- TABEL 3: layanan -->
        <article class="erd-table-card" id="tbl-layanan">
          <header class="erd-table-card__head">
            <span class="erd-table-card__name">layanan</span>
            <span class="erd-table-card__tag">Master Jenis Layanan</span>
          </header>
          <ul class="erd-cols">
            <li class="erd-col erd-col--pk" id="col-layanan-id">
              <span class="erd-col__left"><span class="key-pill key-pill--pk">PK</span><strong class="mono">id</strong></span>
              <span class="erd-col__type">INT UNSIGNED AI</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="key-pill key-pill--uk">UK</span><span class="mono">nama_layanan</span></span>
              <span class="erd-col__type">VARCHAR(80)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">harga_per_kg</span></span>
              <span class="erd-col__type">INT UNSIGNED</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">estimasi_waktu</span></span>
              <span class="erd-col__type">VARCHAR(60)</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">deskripsi</span></span>
              <span class="erd-col__type">TEXT NULL</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">created_at</span></span>
              <span class="erd-col__type">TIMESTAMP</span>
            </li>
            <li class="erd-col">
              <span class="erd-col__left"><span class="mono">updated_at</span></span>
              <span class="erd-col__type">TIMESTAMP</span>
            </li>
          </ul>
        </article>
      </div>
    </section>

    <!-- 3. Penjelasan Aturan Relasi & Integritas -->
    <section class="rel-grid" aria-label="Ringkasan aturan relasi database">
      <article class="rel-card">
        <h3 class="rel-card__title">1. Relasi Transaksi → Layanan (1:N)</h3>
        <div class="rel-card__code">fk_transaksi_layanan: transaksi.layanan_id → layanan.id</div>
        <p class="muted" style="font-size:0.875rem">
          Menggunakan <code>ON UPDATE CASCADE ON DELETE RESTRICT</code>. Layanan yang sudah pernah dipakai di riwayat transaksi (seperti Reguler, Ekspress, Superkilat) terlindungi dari penghapusan agar laporan tidak rusak.
        </p>
      </article>

      <article class="rel-card">
        <h3 class="rel-card__title">2. Relasi Transaksi → Users (1:N)</h3>
        <div class="rel-card__code">fk_transaksi_user: transaksi.user_id → users.id</div>
        <p class="muted" style="font-size:0.875rem">
          Menghubungkan setiap pencatatan transaksi dengan petugas Admin atau Kasir yang menginput order, sehingga tercetak jelas di struk (`struk.php`) dan laporan keuangan (`laporan.php`).
        </p>
      </article>

      <article class="rel-card">
        <h3 class="rel-card__title">3. Snapshot Tarif &amp; Query JOIN</h3>
        <div class="rel-card__code">total_harga = ROUND(berat_kg * harga_per_kg)</div>
        <p class="muted" style="font-size:0.875rem">
          Kolom <code>harga_satuan</code> menyimpan tarif saat transaksi dibuat, sementara laporan keuangan menggabungkan tabel via <code>INNER JOIN layanan</code> dan <code>GROUP BY layanan.id</code>.
        </p>
      </article>
    </section>

    <!-- 4. Isi Lengkap database/schema.sql (Full Tanpa Terpotong) -->
    <section class="card" aria-labelledby="full-sql-heading">
      <div class="card__header">
        <div>
          <h2 class="card__title" id="full-sql-heading">Kode Lengkap <code>database/schema.sql</code></h2>
          <p class="card__subtitle"><?= count($sqlLines) ?> baris SQL — mencakup CREATE DATABASE, 3 tabel, Foreign Key, dan Seed Data</p>
        </div>
        <button type="button" class="btn btn--secondary btn--sm" id="copy-sql-btn">Salin SQL</button>
      </div>

      <div class="sql-viewer" id="sql-source" tabindex="0" role="region" aria-label="Kode lengkap database/schema.sql">
        <?php foreach ($sqlLines as $idx => $line): ?>
          <?php $isComment = str_starts_with(ltrim($line), '--'); ?>
          <div class="sql-line">
            <span class="sql-ln"><?= $idx + 1 ?></span>
            <span class="sql-code<?= $isComment ? ' sql-comment' : '' ?>"><?= e($line) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <script>
    // Gambar garis relasi SVG bezier dari kolom PK ke kolom FK secara presisi
    function drawErdLines() {
      const stage = document.getElementById('erd-stage');
      const svg = document.getElementById('erd-svg');
      if (!stage || !svg) return;

      const sRect = stage.getBoundingClientRect();
      svg.setAttribute('viewBox', `0 0 ${sRect.width} ${sRect.height}`);
      svg.innerHTML = '';

      const connect = (fromId, toId, color, labelLeft, labelRight) => {
        const a = document.getElementById(fromId);
        const b = document.getElementById(toId);
        if (!a || !b) return;
        const ra = a.getBoundingClientRect();
        const rb = b.getBoundingClientRect();

        const fromRight = ra.left < rb.left;
        const x1 = (fromRight ? ra.right : ra.left) - sRect.left;
        const y1 = (ra.top + ra.height / 2) - sRect.top;
        const x2 = (fromRight ? rb.left : rb.right) - sRect.left;
        const y2 = (rb.top + rb.height / 2) - sRect.top;
        const cx = (x1 + x2) / 2;

        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', `M ${x1} ${y1} C ${cx} ${y1}, ${cx} ${y2}, ${x2} ${y2}`);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke', color);
        path.setAttribute('stroke-width', '2.2');
        svg.appendChild(path);

        // Titik ujung & label kardinalitas 1 -> N
        for (const [cxDot, cyDot, txt, dx] of [
          [x1, y1, labelLeft, fromRight ? 10 : -16],
          [x2, y2, labelRight, fromRight ? -18 : 10],
        ]) {
          const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
          dot.setAttribute('cx', cxDot);
          dot.setAttribute('cy', cyDot);
          dot.setAttribute('r', '4');
          dot.setAttribute('fill', color);
          svg.appendChild(dot);

          const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
          t.setAttribute('x', cxDot + dx);
          t.setAttribute('y', cyDot - 8);
          t.setAttribute('fill', color);
          t.setAttribute('font-size', '11');
          t.setAttribute('font-weight', '700');
          t.setAttribute('font-family', 'JetBrains Mono, monospace');
          t.textContent = txt;
          svg.appendChild(t);
        }
      };

      connect('col-users-id', 'col-trx-user', '#0d7480', '1', 'N');
      connect('col-layanan-id', 'col-trx-layanan', '#b45309', '1', 'N');
    }

    window.addEventListener('load', drawErdLines);
    window.addEventListener('resize', drawErdLines);

    // Sorot tabel tujuan saat hover baris Foreign Key
    document.querySelectorAll('[data-rel-target]').forEach((el) => {
      const target = document.getElementById(el.dataset.relTarget);
      if (!target) return;
      el.addEventListener('mouseenter', () => target.classList.add('is-highlighted'));
      el.addEventListener('mouseleave', () => target.classList.remove('is-highlighted'));
    });

    // Tombol salin kode SQL lengkap
    const copyBtn = document.getElementById('copy-sql-btn');
    if (copyBtn) {
      copyBtn.addEventListener('click', async () => {
        const lines = [...document.querySelectorAll('.sql-code')].map((n) => n.textContent).join('\n');
        try {
          await navigator.clipboard.writeText(lines);
          copyBtn.textContent = 'Tersalin!';
          setTimeout(() => (copyBtn.textContent = 'Salin SQL'), 1800);
        } catch {}
      });
    }
  </script>
</body>
</html>
