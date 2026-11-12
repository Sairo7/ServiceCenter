<div class="container py-4 my-auto">

  <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <span class="badge bg-warning text-dark px-3 py-1 rounded-pill extra-small fw-bold text-uppercase mb-1">
        <i class="bi bi-star-fill text-dark me-1"></i> Quality Assurance & Monitoring
      </span>
      <h3 class="fw-extrabold text-dark mb-0">Analisis Rating & Feedback Pelanggan</h3>
    </div>
    <div>
      <span class="badge bg-dark px-3 py-2 rounded-pill font-weight-medium">
        <i class="bi bi-person-badge me-1"></i> Mode View: <?= ucfirst(str_replace('_', ' ', Auth::role())) ?>
      </span>
    </div>
  </div>

  <?php Flasher::flash(); ?>

  <!-- METRIC STATS CARDS -->
  <div class="row g-3 mb-4">
    
    <!-- 1. Overall Avg Rating -->
    <div class="col-md-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 bg-gradient-dark text-white p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-white-50 extra-small fw-bold text-uppercase">Rata-rata Rating</span>
          <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-star-fill fs-5"></i>
          </div>
        </div>
        <div class="display-5 fw-extrabold text-warning mb-1">
          <?= number_format($overall_stats['avg_rating'] ?? 5.0, 1) ?> <span class="fs-6 text-white-50">/ 5.0</span>
        </div>
        <div class="extra-small text-white-50">
          Dari total <strong><?= $overall_stats['total_reviews'] ?? 0 ?></strong> ulasan pelanggan
        </div>
      </div>
    </div>

    <!-- 2. Total Ulasan & Feedback -->
    <div class="col-md-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted extra-small fw-bold text-uppercase">Total Ulasan Masuk</span>
          <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-chat-left-quote-fill fs-5"></i>
          </div>
        </div>
        <div class="display-5 fw-extrabold text-dark mb-1">
          <?= $overall_stats['total_reviews'] ?? 0 ?>
        </div>
        <div class="extra-small text-success fw-semibold">
          <i class="bi bi-check-circle-fill me-1"></i> TERVERIFIKASI SISTEM
        </div>
      </div>
    </div>

    <!-- 3. Tingkat Kepuasan Pelanggan -->
    <div class="col-md-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted extra-small fw-bold text-uppercase">Tingkat Kepuasan</span>
          <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-emoji-smile-fill fs-5"></i>
          </div>
        </div>
        <div class="display-5 fw-extrabold text-success mb-1">
          <?= $overall_stats['satisfaction_percentage'] ?? 100 ?>%
        </div>
        <div class="extra-small text-muted">
          Persentase rating bintang 4 & 5
        </div>
      </div>
    </div>

    <!-- 4. Teknisi Rating Tertinggi -->
    <div class="col-md-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted extra-small fw-bold text-uppercase">Teknisi Terbaik</span>
          <div class="bg-warning bg-opacity-10 text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-trophy-fill fs-5 text-warning"></i>
          </div>
        </div>
        <?php $topTech = $tech_stats[0] ?? null; ?>
        <div class="fw-extrabold text-dark mb-1 text-truncate">
          <?= htmlspecialchars($topTech['technician_name'] ?? 'Budi Santoso') ?>
        </div>
        <div class="extra-small text-primary fw-bold">
          ⭐ <?= number_format($topTech['avg_rating'] ?? 5.0, 1) ?> (<?= $topTech['total_reviews'] ?? 0 ?> ulasan)
        </div>
      </div>
    </div>

  </div>

  <!-- SEARCH & FILTER BAR -->
  <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-3">
      <input type="hidden" name="url" value="order/ratings">

      <div class="col-md-4">
        <label class="form-label extra-small fw-bold text-muted text-uppercase">Pencarian Text</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
          <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Cari tiket, customer, ulasan..." value="<?= htmlspecialchars($selected_search ?? ($search ?? '')) ?>">
        </div>
      </div>

      <div class="col-md-2">
        <label class="form-label extra-small fw-bold text-muted text-uppercase">Rating Bintang</label>
        <select name="stars" class="form-select bg-light">
          <option value="">Semua Bintang</option>
          <option value="5" <?= ($selected_stars ?? '') === '5' ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ 5 Bintang</option>
          <option value="4" <?= ($selected_stars ?? '') === '4' ? 'selected' : '' ?>>⭐⭐⭐⭐ 4 Bintang</option>
          <option value="3" <?= ($selected_stars ?? '') === '3' ? 'selected' : '' ?>>⭐⭐⭐ 3 Bintang</option>
          <option value="2" <?= ($selected_stars ?? '') === '2' ? 'selected' : '' ?>>⭐⭐ 2 Bintang</option>
          <option value="1" <?= ($selected_stars ?? '') === '1' ? 'selected' : '' ?>>⭐ 1 Bintang</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label extra-small fw-bold text-muted text-uppercase">Filter Teknisi</label>
        <select name="tech_id" class="form-select bg-light">
          <option value="">Semua Teknisi</option>
          <?php foreach ($technicians as $t): ?>
            <option value="<?= $t['id'] ?>" <?= ($selected_tech ?? '') == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary rounded-3 fw-bold flex-grow-1">
          <i class="bi bi-filter me-1"></i> Terapkan Filter
        </button>
        <a href="<?= BASE_URL ?>/index.php?url=order/ratings" class="btn btn-outline-secondary rounded-3">
          Reset
        </a>
      </div>
    </form>
  </div>

  <!-- TABS CONTAINER -->
  <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-light p-2 border-bottom">
      <ul class="nav nav-pills nav-fill gap-2" id="ratingTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active fw-bold py-2 rounded-3" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-pane" type="button" role="tab">
            <i class="bi bi-chat-square-quote me-1"></i> 1. Daftar Ulasan & Feedback Customer
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link fw-bold py-2 rounded-3" id="tech-tab" data-bs-toggle="tab" data-bs-target="#tech-pane" type="button" role="tab">
            <i class="bi bi-person-badge-fill me-1"></i> 2. Rating Berdasarkan Teknisi
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link fw-bold py-2 rounded-3" id="service-tab" data-bs-toggle="tab" data-bs-target="#service-pane" type="button" role="tab">
            <i class="bi bi-grid-fill me-1"></i> 3. Rating Berdasarkan Jenis Layanan
          </button>
        </li>
      </ul>
    </div>

    <div class="card-body p-4 p-md-5">
      <div class="tab-content" id="ratingTabContent">

        <!-- TAB 1: ALL REVIEWS & FEEDBACK LIST -->
        <div class="tab-pane fade show active" id="reviews-pane" role="tabpanel">
          
          <?php if (!empty($ratings)): ?>
            <div class="row g-4">
              <?php foreach ($ratings as $r): ?>
                <div class="col-lg-6">
                  <div class="card border rounded-4 p-4 h-100 shadow-xs hover-shadow transition-all bg-white">
                    
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                      <div>
                        <span class="badge bg-primary extra-small me-1">Tiket: <?= htmlspecialchars($r['order_code'] ?? 'ORD-00') ?></span>
                        <span class="extra-small text-muted"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?> WIB</span>
                      </div>
                      <div class="text-warning fs-6">
                        <?php for($i=1; $i<=5; $i++): ?>
                          <i class="bi bi-star<?= $i <= $r['stars'] ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                        <span class="fw-bold text-dark ms-1 extra-small"><?= number_format($r['stars'], 1) ?></span>
                      </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3">
                      <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <?= strtoupper(substr($r['customer_name'] ?? 'C', 0, 1)) ?>
                      </div>
                      <div>
                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($r['customer_name'] ?? 'Customer') ?></h6>
                        <span class="extra-small text-muted">Layanan: <strong><?= htmlspecialchars($r['service_name'] ?? 'Jasa IT') ?></strong></span>
                      </div>
                    </div>

                    <!-- Komentar -->
                    <div class="p-3 bg-light rounded-3 border mb-3 small">
                      <span class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Komentar Ulasan:</span>
                      <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($r['user_comment'] ?? $r['comment'])) ?></p>
                    </div>

                    <!-- Feedback Layanan -->
                    <?php if (!empty($r['service_feedback'])): ?>
                      <div class="p-3 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25 small mb-3">
                        <span class="extra-small text-uppercase text-primary fw-bold d-block mb-1"><i class="bi bi-lightbulb-fill me-1"></i> Feedback Layanan:</span>
                        <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($r['service_feedback'])) ?></p>
                      </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center justify-content-between pt-2 border-top extra-small text-muted mt-auto">
                      <div>
                        <i class="bi bi-person-badge text-primary me-1"></i> Teknisi: <strong><?= htmlspecialchars($r['technician_name'] ?? 'Teknisi Siswa') ?></strong>
                      </div>
                      <a href="<?= BASE_URL ?>/index.php?url=order/detail/<?= $r['order_id'] ?>" class="text-decoration-none text-primary fw-bold">
                        Lihat Tiket <i class="bi bi-arrow-right me-1"></i>
                      </a>
                    </div>

                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-5 text-muted">
              <i class="bi bi-chat-square-x display-3 text-muted d-block mb-2"></i>
              <h5 class="fw-bold">Belum Ada Ulasan Customer</h5>
              <p class="small text-muted">Belum ada ulasan yang sesuai dengan filter pencarian Anda.</p>
            </div>
          <?php endif; ?>

        </div>

        <!-- TAB 2: RATING BY TECHNICIAN -->
        <div class="tab-pane fade" id="tech-pane" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover align-middle border text-dark">
              <thead class="table-light extra-small text-uppercase fw-bold">
                <tr>
                  <th>No</th>
                  <th>Nama Teknisi Siswa</th>
                  <th>Spesialisasi Keterampilan</th>
                  <th class="text-center">Total Ulasan</th>
                  <th class="text-center">Rata-rata Rating</th>
                  <th class="text-center">Total 5 Bintang</th>
                  <th class="text-center">Status Performa</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($tech_stats)): ?>
                  <?php foreach ($tech_stats as $idx => $ts): ?>
                    <tr>
                      <td class="fw-bold"><?= $idx + 1 ?></td>
                      <td>
                        <strong class="text-dark d-block"><?= htmlspecialchars($ts['technician_name']) ?></strong>
                      </td>
                      <td class="small text-muted"><?= htmlspecialchars($ts['specialization'] ?? 'Instalasi Jaringan & Hardware') ?></td>
                      <td class="text-center font-weight-bold"><?= $ts['total_reviews'] ?> Ulasan</td>
                      <td class="text-center">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-6">
                          ⭐ <?= number_format($ts['avg_rating'], 1) ?> / 5.0
                        </span>
                      </td>
                      <td class="text-center text-success fw-bold">
                        <i class="bi bi-star-fill text-warning me-1"></i> <?= $ts['five_star_count'] ?> kali
                      </td>
                      <td class="text-center">
                        <?php if ($ts['avg_rating'] >= 4.5): ?>
                          <span class="badge bg-success text-white px-3 py-1 rounded-pill">Sangat Baik 🏆</span>
                        <?php elseif ($ts['avg_rating'] >= 3.5): ?>
                          <span class="badge bg-info text-dark px-3 py-1 rounded-pill">Baik 👍</span>
                        <?php else: ?>
                          <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Perlu Bimbingan</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada statistik rating teknisi.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 3: RATING BY SERVICE -->
        <div class="tab-pane fade" id="service-pane" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover align-middle border text-dark">
              <thead class="table-light extra-small text-uppercase fw-bold">
                <tr>
                  <th>No</th>
                  <th>Nama Jenis Layanan Jasa</th>
                  <th>Kategori Layanan</th>
                  <th class="text-center">Total Pemesanan Di-rating</th>
                  <th class="text-center">Rata-rata Kepuasan</th>
                  <th class="text-center">Indikator Mutu</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($service_stats)): ?>
                  <?php foreach ($service_stats as $idx => $ss): ?>
                    <tr>
                      <td class="fw-bold"><?= $idx + 1 ?></td>
                      <td>
                        <strong class="text-dark d-block"><?= htmlspecialchars($ss['service_name']) ?></strong>
                      </td>
                      <td class="small">
                        <span class="badge bg-secondary extra-small"><?= htmlspecialchars($ss['category']) ?></span>
                      </td>
                      <td class="text-center fw-semibold"><?= $ss['total_reviews'] ?> Ulasan</td>
                      <td class="text-center">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-6">
                          ⭐ <?= number_format($ss['avg_rating'], 1) ?> / 5.0
                        </span>
                      </td>
                      <td class="text-center">
                        <?php if ($ss['avg_rating'] >= 4.5): ?>
                          <span class="badge bg-success text-white px-3 py-1 rounded-pill"><i class="bi bi-shield-check me-1"></i> Mutu Sangat Tinggi</span>
                        <?php else: ?>
                          <span class="badge bg-primary text-white px-3 py-1 rounded-pill">Mutu Baik</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada statistik rating jenis layanan.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

  </div>

</div>
