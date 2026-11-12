<div class="bg-dark-gradient text-white py-4 mb-4 border-bottom border-secondary">
  <div class="container">
    <div class="row align-items-center gy-3">
      <div class="col-md-7">
        <span class="badge bg-primary text-white px-3 py-2 rounded-pill mb-2">
          <i class="bi bi-tools me-1"></i> Unit Bisnis Jasa TKJ
        </span>
        <h1 class="display-6 fw-extrabold mb-1">Katalog Layanan Jasa Teknis</h1>
        <p class="text-light opacity-75 mb-0 small">
          Pilih jasa instalasi, perbaikan, atau pemeliharaan perangkat IT profesional siswa TKJ.
        </p>
      </div>
      <div class="col-md-5 text-md-end">
        <?php if (Auth::hasRole('operator')): ?>
          <a href="<?= BASE_URL ?>/index.php?url=service/manage" class="btn btn-warning text-dark font-weight-bold px-4 py-2 rounded-pill shadow-sm">
            <i class="bi bi-gear-fill me-1"></i> Kelola Jasa Operator
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="container mb-5">
  
  <?php Flasher::flash(); ?>

  <!-- Search & Category Filter Bar -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
    <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 align-items-center">
      <input type="hidden" name="url" value="service">

      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama jasa, misal: CCTV, Router, Laptop..." value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
      </div>

      <div class="col-md-4">
        <select name="cat" class="form-select bg-light">
          <option value="">-- Semua Kategori Jasa --</option>
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>" <?= ($selected_category ?? '') === $cat ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>

      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary-gradient w-100 text-white rounded-3 fw-semibold">
          <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>
        <?php if (!empty($search) || !empty($selected_category)): ?>
          <a href="<?= BASE_URL ?>/index.php?url=service" class="btn btn-outline-secondary rounded-3" title="Reset Filter">
            <i class="bi bi-x-circle"></i>
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Services Card Grid -->
  <?php if (!empty($services)): ?>
    <div class="row gy-4">
      <?php foreach ($services as $service): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card card-service h-100 border-0 shadow-sm rounded-4 transition-all hover-lift bg-white">
            <div class="card-body p-4 d-flex flex-column">
              
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="service-icon text-primary bg-primary-subtle d-inline-flex align-items-center justify-content-center rounded-3 p-3" style="width: 52px; height: 52px; font-size: 1.4rem;">
                  <i class="bi <?= htmlspecialchars($service['icon'] ?? 'bi-wrench') ?>"></i>
                </div>
                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill extra-small fw-semibold">
                  <?= htmlspecialchars($service['category'] ?? 'Jasa IT') ?>
                </span>
              </div>

              <h5 class="card-title fw-bold text-dark mb-2">
                <?= htmlspecialchars($service['title'] ?? $service['name']) ?>
              </h5>
              
              <p class="card-text text-muted small flex-grow-1 mb-3 line-clamp-3">
                <?= htmlspecialchars($service['description']) ?>
              </p>

              <div class="d-flex align-items-center gap-3 text-muted extra-small mb-3">
                <div><i class="bi bi-clock-history text-primary me-1"></i> Est: <?= htmlspecialchars($service['estimated_duration_hours'] ?? 2) ?> Jam</div>
                <div><i class="bi bi-shield-check text-success me-1"></i> Bergaransi</div>
              </div>

              <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block">Harga Mulai:</span>
                  <span class="fw-extrabold text-success fs-5">
                    Rp <?= number_format($service['price_estimate'] ?? $service['base_price'] ?? 0, 0, ',', '.') ?>
                  </span>
                </div>
                
                <div class="d-flex gap-1">
                  <a href="<?= BASE_URL ?>/index.php?url=service/detail/<?= $service['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    Detail
                  </a>
                  <a href="<?= BASE_URL ?>/index.php?url=service/apply/<?= $service['id'] ?>" class="btn btn-primary-gradient text-white btn-sm rounded-pill px-3 fw-bold">
                    Ajukan
                  </a>
                </div>
              </div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
      <i class="bi bi-search text-muted display-1 mb-3"></i>
      <h4 class="fw-bold text-dark mb-2">Layanan Jasa Tidak Ditemukan</h4>
      <p class="text-muted small">Coba ubah kata kunci pencarian atau pilih filter kategori lainnya.</p>
      <a href="<?= BASE_URL ?>/index.php?url=service" class="btn btn-outline-primary rounded-pill px-4">
        Tampilkan Semua Jasa
      </a>
    </div>
  <?php endif; ?>

</div>
