<div class="container py-4 my-auto">
  
  <?php Flasher::flash(); ?>

  <div class="mb-3">
    <a href="<?= BASE_URL ?>/index.php?url=service" class="text-decoration-none text-secondary small">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog Layanan Jasa
    </a>
  </div>

  <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-dark-gradient text-white p-4 p-md-5 border-0">
      <div class="row align-items-center gy-3">
        <div class="col-md-8">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary px-3 py-2 rounded-pill extra-small text-uppercase">
              <?= htmlspecialchars($service['category'] ?? 'Jasa IT') ?>
            </span>
            <span class="badge bg-secondary-subtle text-light border px-2 py-1 extra-small">
              Kode: <?= htmlspecialchars($service['code'] ?? 'SVC-001') ?>
            </span>
          </div>
          <h2 class="display-6 fw-extrabold mb-2"><?= htmlspecialchars($service['title'] ?? $service['name']) ?></h2>
          <p class="text-white opacity-75 mb-0">Unit Bisnis Jasa Siswa Teknik Komputer & Jaringan (TKJ)</p>
        </div>
        <div class="col-md-4 text-md-end">
          <div class="bg-white bg-opacity-10 p-3 rounded-3 d-inline-block text-start border border-white border-opacity-25">
            <span class="text-white-50 extra-small d-block">Estimasi Biaya Jasa:</span>
            <span class="display-6 fw-bold text-success">
              Rp <?= number_format($service['price_estimate'] ?? $service['base_price'] ?? 0, 0, ',', '.') ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-4 p-md-5">
      <div class="row gy-4">
        
        <!-- Left Side Specs -->
        <div class="col-lg-8">
          <h5 class="fw-bold text-dark mb-3">Deskripsi & Spesifikasi Layanan</h5>
          <p class="text-secondary lead fs-6 mb-4">
            <?= nl2br(htmlspecialchars($service['description'])) ?>
          </p>

          <h5 class="fw-bold text-dark mb-3">Keuntungan Memesan Jasa Teknis TKJ</h5>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                <i class="bi bi-shield-check text-success fs-3"></i>
                <div>
                  <div class="fw-semibold text-dark">Jaminan Bergaransi</div>
                  <div class="extra-small text-muted">Garansi pengerjaan & pengujian jaringan.</div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                <i class="bi bi-person-workspace text-primary fs-3"></i>
                <div>
                  <div class="fw-semibold text-dark">Pembimbing Industri</div>
                  <div class="extra-small text-muted">Diawasi langsung oleh guru produktif TKJ.</div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                <i class="bi bi-clock-history text-warning fs-3"></i>
                <div>
                  <div class="fw-semibold text-dark">Estimasi Waktu Pengerjaan</div>
                  <div class="extra-small text-muted">Durasi ± <?= htmlspecialchars($service['estimated_duration_hours'] ?? 2) ?> Jam Kerja.</div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                <i class="bi bi-wallet2 text-info fs-3"></i>
                <div>
                  <div class="fw-semibold text-dark">Harga Terjangkau</div>
                  <div class="extra-small text-muted">Tarif fleksibel standar sekolah & instansi.</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side CTA Box -->
        <div class="col-lg-4">
          <div class="card border-0 bg-light p-4 rounded-4 text-center">
            <div class="service-icon mx-auto mb-3 text-primary bg-white d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 70px; height: 70px; font-size: 2rem;">
              <i class="bi <?= htmlspecialchars($service['icon'] ?? 'bi-wrench') ?>"></i>
            </div>
            
            <h5 class="fw-bold mb-1">Siap Mengajukan Layanan Ini?</h5>
            <p class="text-muted small mb-4">Klik tombol di bawah ini untuk memulai pengajuan pengerjaan jasa teknis.</p>

            <a href="<?= BASE_URL ?>/index.php?url=service/apply/<?= $service['id'] ?>" class="btn btn-primary-gradient btn-lg w-100 rounded-pill text-white fw-bold shadow-sm mb-3">
              <i class="bi bi-send-fill me-2"></i> Ajukan Layanan Ini
            </a>

            <a href="<?= BASE_URL ?>/index.php?url=service" class="btn btn-outline-secondary w-100 rounded-pill">
              Lihat Jasa Lainnya
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>
