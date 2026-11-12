<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    <!-- Sidebar Area -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Content Area -->
    <div class="col-lg-9 col-xl-10">
      
      <?php Flasher::flash(); ?>

      <div class="card border-0 bg-dark-gradient text-white rounded-4 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase">
              <i class="bi bi-speedometer me-1"></i> Dashboard Operator
            </span>
            <span class="text-white-50 extra-small"><i class="bi bi-shield-check me-1"></i> Panel Kendali Operasional Unit TEFA</span>
          </div>
          <h2 class="fw-extrabold mb-1">Pusat Pengawasan Operasional Jasa TKJ</h2>
          <p class="text-light opacity-75 mb-0 small">Verifikasi pengajuan order, penugasan teknisi, stok sparepart, dan penerbitan tagihan transaksi.</p>
        </div>
      </div>

      <!-- 7 KPI Cards for Operator -->
      <div class="row gy-3 mb-4">
        <div class="col-sm-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-4" style="border-color: #6f42c1 !important;">
            <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Pengguna</span>
            <h3 class="fw-extrabold mb-0" style="color: #6f42c1;"><?= htmlspecialchars($stats['total_users'] ?? 0) ?></h3>
            <div class="mt-2">
              <span class="badge rounded-pill px-2 py-1 extra-small" style="background-color: rgba(111,66,193,0.1); color: #6f42c1;">
                <i class="bi bi-person-lines-fill me-1"></i>Semua Akun Aktif
              </span>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center">
            <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Customer</span>
            <h3 class="fw-extrabold text-primary mb-0"><?= htmlspecialchars($stats['total_customers'] ?? 0) ?></h3>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center">
            <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Teknisi</span>
            <h3 class="fw-extrabold text-info mb-0"><?= htmlspecialchars($stats['total_technicians'] ?? 0) ?></h3>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-warning border-4">
            <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Order Baru</span>
            <h3 class="fw-extrabold text-warning mb-0"><?= htmlspecialchars($stats['new_orders'] ?? 0) ?></h3>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-primary border-4">
            <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Order Berjalan</span>
            <h3 class="fw-extrabold text-primary mb-0"><?= htmlspecialchars($stats['ongoing_orders'] ?? 0) ?></h3>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-success border-4">
            <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Order Selesai</span>
            <h3 class="fw-extrabold text-success mb-0"><?= htmlspecialchars($stats['completed_orders'] ?? 0) ?></h3>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center">
            <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Omset</span>
            <h4 class="fw-extrabold text-success mb-0">Rp <?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></h4>
          </div>
        </div>
      </div>

      <div class="row gy-4 mb-4">
        
        <!-- GRAFIK JUMLAH ORDER BERDASARKAN STATUS -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">
              <i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Grafik Jumlah Order Berdasarkan Status (10 Alur)
            </h5>

            <div class="status-chart-list">
              <?php if (!empty($stats['status_chart'])): ?>
                <?php foreach ($stats['status_chart'] as $stName => $count): 
                  $percent = min(100, max(8, $count * 15));
                  $barColor = 'bg-primary';
                  if ($stName === 'Diajukan') $barColor = 'bg-secondary';
                  else if ($stName === 'Dalam Pengerjaan' || $stName === 'Teknisi Ditugaskan') $barColor = 'bg-warning text-dark';
                  else if ($stName === 'Selesai' || $stName === 'Ditutup') $barColor = 'bg-success';
                  else if ($stName === 'Dibatalkan') $barColor = 'bg-danger';
                ?>
                  <div class="mb-2">
                    <div class="d-flex align-items-center justify-content-between extra-small fw-bold text-dark mb-1">
                      <span><?= htmlspecialchars($stName) ?></span>
                      <span><?= $count ?> Order</span>
                    </div>
                    <div class="progress rounded-pill bg-light" style="height: 12px;">
                      <div class="progress-bar <?= $barColor ?> rounded-pill" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $count ?>" aria-valuemin="0" aria-valuemax="20"></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- JASA PALING BANYAK DIPESAN -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">
              <i class="bi bi-star-fill text-warning me-2"></i> Jasa Paling Banyak Dipesan (Most Popular Services)
            </h5>

            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th>Ranking & Nama Layanan Jasa</th>
                    <th>Kategori</th>
                    <th class="text-center">Total Order</th>
                    <th class="text-end">Omset (Rp)</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($stats['popular_services'])): ?>
                    <?php foreach ($stats['popular_services'] as $idx => $ps): ?>
                      <tr>
                        <td>
                          <span class="badge bg-dark rounded-circle me-1" style="width: 24px; height: 24px; line-height: 16px;"><?= $idx + 1 ?></span>
                          <span class="fw-bold text-dark"><?= htmlspecialchars($ps['name']) ?></span>
                        </td>
                        <td>
                          <span class="badge bg-info-subtle text-info border border-info-subtle extra-small"><?= htmlspecialchars($ps['category']) ?></span>
                        </td>
                        <td class="text-center fw-bold text-primary"><?= htmlspecialchars($ps['total_orders']) ?>x</td>
                        <td class="text-end fw-bold text-success">Rp <?= number_format($ps['revenue'], 0, ',', '.') ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>
