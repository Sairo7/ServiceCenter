<!-- Hero Section -->
<section class="hero-section py-5 position-relative overflow-hidden">
  <div class="container py-4 position-relative z-1">
    <div class="row align-items-center gy-5">
      <div class="col-lg-7">
        <span class="badge bg-white text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3 shadow-sm d-inline-flex align-items-center">
          <i class="bi bi-patch-check-fill text-primary me-2"></i> Unit Bisnis Jasa Resmi Siswa TKJ
        </span>
        <h1 class="display-4 hero-heading mb-3">
          Solusi Layanan IT & Jaringan <span class="hero-highlight text-primary-gradient">Terpercaya</span> Lingkungan Sekolah
        </h1>
        <p class="hero-desc mb-4">
          Melayani perbaikan laptop/PC, instalasi CCTV, perancangan jaringan LAN/Wi-Fi, dan konfigurasi router MikroTik secara profesional oleh teknisi berpengalaman siswa TKJ.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="#layanan" class="btn btn-primary-gradient btn-lg px-4 py-3 rounded-pill shadow fw-bold">
            <i class="bi bi-tools me-2"></i> Lihat Semua Jasa Teknis
          </a>
          <a href="<?= BASE_URL ?>/index.php?url=<?= Auth::check() ? 'dashboard' : 'auth/login' ?>" class="btn btn-outline-blue btn-lg px-4 py-3 rounded-pill fw-semibold">
            <i class="bi bi-box-arrow-in-right me-2"></i> <?= Auth::check() ? 'Dashboard Saya' : 'Masuk / Pesan Jasa' ?>
          </a>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="hero-card p-4 text-dark position-relative">
          <div class="badge bg-success text-white position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill shadow-sm">
            <i class="bi bi-check-circle-fill me-1"></i> Siap Melayani
          </div>
          <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 p-3 my-3" style="width: 72px; height: 72px; font-size: 2.2rem; background: linear-gradient(135deg, #eff6ff, #ecfeff) !important;">
            <i class="bi bi-cpu-fill text-primary"></i>
          </div>
          <h4 class="fw-bold mb-2 text-navy">Punya Masalah Perangkat IT?</h4>
          <p class="text-slate-muted small mb-4">Konsultasikan kendala PC, Laptop, CCTV, atau Router Anda kepada tim teknisi TKJ kami.</p>
          <a href="<?= BASE_URL ?>/index.php?url=<?= Auth::check() ? 'order/create' : 'auth/login' ?>" class="btn btn-primary-gradient w-100 py-3 rounded-3 fw-bold">
            <i class="bi bi-calendar-plus me-2"></i> Pesan Layanan Sekarang
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Flash Messages -->
<div class="container mt-4">
  <?php Flasher::flash(); ?>
</div>

<!-- Stats Counter Banner -->
<section class="py-4 bg-white border-bottom shadow-sm">
  <div class="container">
    <div class="row text-center gy-3">
      <div class="col-6 col-md-3">
        <h2 class="fw-extrabold text-primary mb-0">150+</h2>
        <p class="text-slate-muted small fw-medium mb-0">Perangkat Ditangani</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-extrabold text-success mb-0">98%</h2>
        <p class="text-slate-muted small fw-medium mb-0">Tingkat Kepuasan</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-extrabold text-warning mb-0">20+</h2>
        <p class="text-slate-muted small fw-medium mb-0">Teknisi Siswa TKJ</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-extrabold text-info mb-0">8+</h2>
        <p class="text-slate-muted small fw-medium mb-0">Jenis Jasa Teknis</p>
      </div>
    </div>
  </div>
</section>

<!-- Services Grid Section -->
<section id="layanan" class="py-5" style="background-color: #f8fafc;">
  <div class="container py-2">
    <div class="text-center max-w-700 mx-auto mb-5">
      <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase tracking-wider px-3 py-2 rounded-pill small mb-2">Layanan Unggulan</span>
      <h2 class="fw-extrabold text-navy display-6 mt-1">Jasa Teknis yang Kami Sediakan</h2>
      <p class="text-slate-muted">Unit bisnis TKJ menyediakan berbagai macam jasa perbaikan dan instalasi infrastruktur IT untuk kebutuhan pribadi, sekolah, maupun instansi.</p>
    </div>

    <div class="row gy-4">
      <?php if (!empty($services)): ?>
        <?php foreach ($services as $service): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card card-service h-100">
              <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="service-icon-box">
                    <i class="bi <?= htmlspecialchars($service['icon'] ?? 'bi-wrench') ?>"></i>
                  </div>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill extra-small fw-semibold">
                    <?= htmlspecialchars($service['category'] ?? 'Jasa IT') ?>
                  </span>
                </div>
                <h5 class="card-title fw-bold text-navy mb-2"><?= htmlspecialchars($service['title'] ?? $service['name']) ?></h5>
                <p class="card-text text-slate-muted small flex-grow-1 mb-4"><?= htmlspecialchars($service['description']) ?></p>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                  <div>
                    <span class="text-slate-muted extra-small d-block">Estimasi Biaya:</span>
                    <span class="fw-extrabold text-success fs-6">Rp <?= number_format($service['price_estimate'] ?? $service['base_price'] ?? 0, 0, ',', '.') ?></span>
                  </div>
                  <a href="<?= BASE_URL ?>/index.php?url=<?= Auth::check() ? 'order/create/' . $service['id'] : 'auth/login' ?>" class="btn btn-outline-blue btn-sm rounded-pill px-3">
                    Pesan Jasa <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- AI Assistant Featured Section -->
<section class="py-5 bg-white border-top border-bottom">
  <div class="container">
    <div class="ai-section p-4 p-md-5">
      <div class="row align-items-center gy-4">
        <div class="col-lg-2 text-center text-lg-start">
          <div class="ai-icon-badge">
            <i class="bi bi-cpu-fill"></i>
          </div>
        </div>
        <div class="col-lg-7 text-center text-lg-start">
          <span class="badge bg-primary text-white px-3 py-1 rounded-pill small fw-semibold mb-2">
            <i class="bi bi-magic me-1"></i> Fitur Unggulan
          </span>
          <h3 class="fw-extrabold text-navy mb-2">AI Assistant TKJ Service</h3>
          <p class="text-slate-muted mb-0 fs-6">
            AI membantu customer memilih layanan dan membantu teknisi melakukan troubleshooting awal secara otomatis dan cepat.
          </p>
        </div>
        <div class="col-lg-3 text-center text-lg-end">
          <button type="button" onclick="if(window.openTkjChatbot){window.openTkjChatbot();}else{window.location='<?= BASE_URL ?>/index.php?url=ai';}" class="btn btn-primary-gradient btn-lg px-4 py-3 rounded-pill fw-bold shadow">
            <i class="bi bi-robot me-2"></i> Coba AI Assistant
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section class="py-5" style="background-color: #f8fafc;">
  <div class="container py-3">
    <div class="text-center max-w-700 mx-auto mb-5">
      <span class="badge bg-info-subtle text-info-emphasis fw-bold text-uppercase tracking-wider px-3 py-2 rounded-pill small mb-2">Alur Pelayanan</span>
      <h2 class="fw-extrabold text-navy mt-1">Cara Pemesanan Jasa Teknis</h2>
    </div>

    <div class="row text-center gy-4">
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
          <div class="step-number bg-primary-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3 shadow-sm" style="width: 54px; height: 54px; font-size: 1.25rem;">1</div>
          <h5 class="fw-bold text-navy">Pilih Layanan</h5>
          <p class="text-slate-muted small mb-0">Pilih jenis jasa teknis yang dibutuhkan melalui website ini.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
          <div class="step-number bg-primary-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3 shadow-sm" style="width: 54px; height: 54px; font-size: 1.25rem;">2</div>
          <h5 class="fw-bold text-navy">Verifikasi Operator</h5>
          <p class="text-slate-muted small mb-0">Operator unit bisnis meninjau pesanan & menentukan teknisi siswa.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
          <div class="step-number bg-primary-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3 shadow-sm" style="width: 54px; height: 54px; font-size: 1.25rem;">3</div>
          <h5 class="fw-bold text-navy">Pengerjaan Teknisi</h5>
          <p class="text-slate-muted small mb-0">Teknisi siswa TKJ mengeksekusi pengerjaan langsung di lokasi / lab.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
          <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3 shadow-sm" style="width: 54px; height: 54px; font-size: 1.25rem;">4</div>
          <h5 class="fw-bold text-navy">Selesai & Garansi</h5>
          <p class="text-slate-muted small mb-0">Pengerjaan selesai dengan bukti pengujian dan jaminan garansi.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="tentang" class="py-5 bg-dark-navy text-white">
  <div class="container py-3">
    <div class="row align-items-center gy-4">
      <div class="col-lg-6">
        <span class="badge bg-primary text-white px-3 py-2 rounded-pill mb-3">Tentang Unit Bisnis TKJ</span>
        <h2 class="fw-extrabold mb-3 text-white">Menciptakan Lulusan Siap Kerja Melalui Unit Produksi Jasa Sekolah</h2>
        <p class="text-slate-muted mb-4" style="color: #cbd5e1 !important;">
          Unit bisnis jasa TKJ merupakan wadah Teaching Factory (TEFA) yang melatih kewirausahaan dan keahlian praktis siswa Teknik Komputer dan Jaringan. Seluruh pengerjaan diawasi langsung oleh guru pembimbing produktif TKJ yang bersertifikasi industri.
        </p>
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-shield-check text-success fs-3"></i>
              <span class="small fw-semibold text-white">Bergaransi & Resmi</span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-mortarboard-fill text-warning fs-3"></i>
              <span class="small fw-semibold text-white">Bimbingan Guru Ahli</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="p-4 rounded-4 border border-secondary border-opacity-50 text-center" style="background: rgba(255, 255, 255, 0.05);">
          <i class="bi bi-buildings-fill text-info display-3 mb-3"></i>
          <h4 class="fw-bold text-white">Workshop & Lab Rekayasa Jaringan</h4>
          <p class="small mb-0" style="color: #cbd5e1;">Lokasi: Lab TKJ Gedung Unit Produksi Sekolah, Gedung B Lantai 2</p>
        </div>
      </div>
    </div>
  </div>
</section>
