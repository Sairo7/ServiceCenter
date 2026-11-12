<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    <!-- Sidebar Area -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Content Area -->
    <div class="col-lg-9 col-xl-10">
      
      <?php Flasher::flash(); ?>

      <!-- Executive Read-Only Header Banner -->
      <div class="card border-0 bg-dark-gradient text-white rounded-4 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-bold text-uppercase">
              <i class="bi bi-eye-fill me-1"></i> Dashboard Kepala Sekolah (Read-Only Monitoring)
            </span>
            <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-3 py-2 rounded-pill extra-small">
              <i class="bi bi-building me-1"></i> Executive Unit Teaching Factory TKJ
            </span>
          </div>
          <h2 class="fw-extrabold mb-1">Laporan Eksekutif Business Unit TEFA TKJ</h2>
          <p class="text-light opacity-75 mb-0 small">Pengawasan kinerja operasional, pertumbuhan omset bulanan, dan evaluasi performa teknisi siswa secara realtime.</p>
        </div>
      </div>

      <!-- 4 Executive KPI Cards -->
      <div class="row gy-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Order Masuk</span>
                <h3 class="fw-extrabold text-primary mb-0"><?= htmlspecialchars($stats['total_orders'] ?? 0) ?> Order</h3>
              </div>
              <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                <i class="bi bi-inbox-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Pendapatan TEFA</span>
                <h3 class="fw-extrabold text-success mb-0">Rp <?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></h3>
              </div>
              <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                <i class="bi bi-graph-up-arrow"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Customer Pelanggan</span>
                <h3 class="fw-extrabold text-info mb-0"><?= htmlspecialchars($stats['total_customers'] ?? 0) ?> User</h3>
              </div>
              <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                <i class="bi bi-people-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Jumlah Teknisi Siswa</span>
                <h3 class="fw-extrabold text-warning mb-0"><?= htmlspecialchars($stats['total_technicians'] ?? 0) ?> Siswa</h3>
              </div>
              <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                <i class="bi bi-person-workspace"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- MONTHLY REVENUE & POPULAR SERVICES CHART ROW -->
      <div class="row gy-4 mb-4">
        
        <!-- Statistik Order & Pendapatan Per Bulan -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">
              <i class="bi bi-bar-chart-fill text-success me-2"></i> Statistik Pertumbuhan Order & Pendapatan Per Bulan
            </h5>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th>Bulan & Tahun</th>
                    <th class="text-center">Jumlah Order</th>
                    <th class="text-end">Omset Pendapatan (Rp)</th>
                    <th>Visual Tren Omset</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($stats['monthly_stats'])): ?>
                    <?php foreach ($stats['monthly_stats'] as $ms): 
                      $percent = min(100, max(15, ($ms['revenue'] / 5000000) * 100));
                    ?>
                      <tr>
                        <td class="fw-bold text-dark"><i class="bi bi-calendar3 text-muted me-1"></i> <?= htmlspecialchars($ms['month']) ?></td>
                        <td class="text-center fw-bold text-primary"><?= htmlspecialchars($ms['orders']) ?> Order</td>
                        <td class="text-end fw-bold text-success">Rp <?= number_format($ms['revenue'], 0, ',', '.') ?></td>
                        <td style="width: 140px;">
                          <div class="progress rounded-pill bg-light" style="height: 10px;">
                            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= $percent ?>%;"></div>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Jasa Terpopuler -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">
              <i class="bi bi-star-fill text-warning me-2"></i> Jasa Terpopuler Paling Banyak Dipesan
            </h5>

            <div class="list-group list-group-flush">
              <?php if (!empty($stats['popular_services'])): ?>
                <?php foreach ($stats['popular_services'] as $idx => $ps): ?>
                  <div class="list-group-item px-0 py-2 border-bottom-0">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="fw-bold text-dark extra-small">#<?= $idx + 1 ?> <?= htmlspecialchars($ps['name']) ?></span>
                      <span class="badge bg-primary rounded-pill"><?= $ps['total_orders'] ?> Order</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between extra-small text-muted">
                      <span>Kategori: <?= htmlspecialchars($ps['category']) ?></span>
                      <span class="text-success fw-bold">Rp <?= number_format($ps['revenue'], 0, ',', '.') ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>

      <!-- PERFORMA TEKNISI SISWA TABLE -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-award-fill me-2 text-warning"></i> Tabel Evaluasi Performa Teknisi Siswa TKJ
          </h5>
          <span class="badge bg-light text-muted border">Read-Only Monitoring</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">Nama Teknisi Siswa</th>
                  <th>NIS & Kelas</th>
                  <th>Spesialisasi Keahlian</th>
                  <th class="text-center">Total Penugasan</th>
                  <th class="text-center">Tugas Selesai</th>
                  <th class="pe-4 text-end">Completion Rate (%)</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($stats['tech_performance'])): ?>
                  <?php foreach ($stats['tech_performance'] as $tp): ?>
                    <tr>
                      <td class="ps-4 fw-bold text-dark">
                        <i class="bi bi-person-badge-fill me-1 text-primary"></i> <?= htmlspecialchars($tp['name']) ?>
                      </td>
                      <td>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($tp['nis']) ?></span> (<?= htmlspecialchars($tp['class']) ?>)
                      </td>
                      <td>
                        <span class="badge bg-info-subtle text-info border border-info-subtle extra-small"><?= htmlspecialchars($tp['specialization']) ?></span>
                      </td>
                      <td class="text-center fw-bold text-dark"><?= htmlspecialchars($tp['assigned_jobs']) ?> Task</td>
                      <td class="text-center fw-bold text-success"><?= htmlspecialchars($tp['completed_jobs']) ?> Task</td>
                      <td class="pe-4 text-end">
                        <span class="badge bg-success fs-6 px-3 py-1 rounded-pill">
                          <i class="bi bi-check-all me-1"></i> <?= htmlspecialchars($tp['rate']) ?>%
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data evaluasi performa teknisi.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- KUALITAS LAYANAN TEFA & MONITORING RATING PELANGGAN BANNER -->
      <div class="card border-0 shadow-sm rounded-4 bg-gradient-dark text-white p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning text-dark rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.5rem;">
              <i class="bi bi-star-fill"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-1 text-white">Monitoring Kualitatif Layanan TEFA & Rating Pelanggan</h5>
              <p class="extra-small text-white-50 mb-0">Pantau statistik tingkat kepuasan pelanggan, ulasan kualitatif per teknisi, dan mutasi rating per jenis layanan secara komprehensif.</p>
            </div>
          </div>
          <a href="<?= BASE_URL ?>/index.php?url=report&tab=ratings" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow-sm text-dark">
            <i class="bi bi-bar-chart-line-fill me-2"></i> Buka Laporan Rating & Feedback
          </a>
        </div>
      </div>

    </div>
  </div>
</div>
