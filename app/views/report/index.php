<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    <!-- Sidebar Navigation -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Report Dashboard Area -->
    <div class="col-lg-9 col-xl-10">
      
      <?php Flasher::flash(); ?>

      <!-- Executive Header Banner -->
      <div class="card border-0 bg-dark-gradient text-white rounded-4 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div>
              <?php if ($is_read_only): ?>
                <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-bold text-uppercase me-2">
                  <i class="bi bi-shield-lock-fill me-1"></i> Mode Read-Only Kepala Sekolah
                </span>
              <?php else: ?>
                <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold text-uppercase me-2">
                  <i class="bi bi-person-gear me-1"></i> Modul Laporan Operator TEFA
                </span>
              <?php endif; ?>
              <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-3 py-2 rounded-pill extra-small">
                <i class="bi bi-building me-1"></i> Unit Bisnis Teaching Factory TKJ
              </span>
            </div>
            <div class="text-white-50 extra-small">
              <i class="bi bi-clock-history me-1"></i> Updates: <?= date('d M Y, H:i') ?> WIB
            </div>
          </div>
          <h2 class="fw-extrabold mb-1">Pusat Laporan & Analytical Business TEFA</h2>
          <p class="text-light opacity-75 mb-0 small">Monitoring performa operasional, omset keuangan, kualitatif teknisi, dan kepuasan pelanggan secara rinci.</p>
        </div>
      </div>

      <!-- FILTER PERIODE & ACTION BAR CARD -->
      <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          
          <!-- Periode Quick Filter Buttons -->
          <div>
            <span class="text-muted extra-small fw-bold text-uppercase d-block mb-2">
              <i class="bi bi-funnel-fill text-primary me-1"></i> Filter Periode Laporan:
            </span>
            <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border" role="group">
              <a href="<?= BASE_URL ?>/index.php?url=report&tab=<?= $active_tab ?>&filter=today" 
                 class="btn rounded-pill px-3 fw-bold <?= $filter_type === 'today' ? 'btn-primary shadow-sm' : 'btn-light text-muted' ?>">
                Hari Ini
              </a>
              <a href="<?= BASE_URL ?>/index.php?url=report&tab=<?= $active_tab ?>&filter=this_week" 
                 class="btn rounded-pill px-3 fw-bold <?= $filter_type === 'this_week' ? 'btn-primary shadow-sm' : 'btn-light text-muted' ?>">
                Minggu Ini
              </a>
              <a href="<?= BASE_URL ?>/index.php?url=report&tab=<?= $active_tab ?>&filter=this_month" 
                 class="btn rounded-pill px-3 fw-bold <?= $filter_type === 'this_month' ? 'btn-primary shadow-sm' : 'btn-light text-muted' ?>">
                Bulan Ini
              </a>
              <a href="<?= BASE_URL ?>/index.php?url=report&tab=<?= $active_tab ?>&filter=this_year" 
                 class="btn rounded-pill px-3 fw-bold <?= $filter_type === 'this_year' ? 'btn-primary shadow-sm' : 'btn-light text-muted' ?>">
                Tahun Ini
              </a>
              <a href="<?= BASE_URL ?>/index.php?url=report&tab=<?= $active_tab ?>&filter=custom" 
                 class="btn rounded-pill px-3 fw-bold <?= $filter_type === 'custom' ? 'btn-primary shadow-sm' : 'btn-light text-muted' ?>">
                Custom Tanggal
              </a>
            </div>
          </div>

          <!-- Export Actions -->
          <div class="d-flex align-items-center gap-2">
            <a href="<?= BASE_URL ?>/index.php?url=report/printView&tab=<?= $active_tab ?>&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" 
               target="_blank" 
               class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
              <i class="bi bi-printer-fill me-1"></i> Cetak / Export PDF
            </a>
            <a href="<?= BASE_URL ?>/index.php?url=report/exportExcel&tab=<?= $active_tab ?>&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" 
               class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
              <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel (CSV)
            </a>
          </div>

        </div>

        <!-- Custom Date Range Selector (Shown if custom selected or toggle) -->
        <?php if ($filter_type === 'custom'): ?>
          <hr class="my-3">
          <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="url" value="report">
            <input type="hidden" name="tab" value="<?= $active_tab ?>">
            <input type="hidden" name="filter" value="custom">

            <div class="col-sm-4 col-md-3">
              <label class="form-label extra-small fw-bold text-muted mb-1">Tanggal Mulai</label>
              <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($start_date) ?>" required>
            </div>
            <div class="col-sm-4 col-md-3">
              <label class="form-label extra-small fw-bold text-muted mb-1">Tanggal Sampai</label>
              <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($end_date) ?>" required>
            </div>
            <div class="col-sm-4 col-md-2 mt-auto">
              <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 fw-bold">
                <i class="bi bi-search me-1"></i> Terapkan
              </button>
            </div>
          </form>
        <?php endif; ?>

        <!-- Active Filter Badge Info -->
        <div class="mt-3">
          <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
            <i class="bi bi-calendar-event text-primary me-1"></i> Periode Terpilih: <strong><?= htmlspecialchars($report_data['period_label'] ?? 'Semua') ?></strong>
          </span>
          <?php if ($is_read_only): ?>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill small ms-1">
              <i class="bi bi-lock-fill me-1"></i> Kepala Sekolah (Mode Peninjauan Akses Terkunci)
            </span>
          <?php endif; ?>
        </div>

      </div>

      <!-- 7 REPORTS NAV TABS -->
      <ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm mb-4 gap-1 flex-wrap">
        <li class="nav-item">
          <a class="nav-link rounded-3 fw-bold small <?= $active_tab === 'orders' ? 'active bg-primary' : 'text-dark' ?>" 
             href="<?= BASE_URL ?>/index.php?url=report&tab=orders&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
            <i class="bi bi-inbox-fill me-1"></i> 1. Jumlah Order
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-3 fw-bold small <?= $active_tab === 'revenue' ? 'active bg-primary' : 'text-dark' ?>" 
             href="<?= BASE_URL ?>/index.php?url=report&tab=revenue&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
            <i class="bi bi-currency-dollar me-1"></i> 2. Pendapatan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-3 fw-bold small <?= $active_tab === 'services' ? 'active bg-primary' : 'text-dark' ?>" 
             href="<?= BASE_URL ?>/index.php?url=report&tab=services&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
            <i class="bi bi-star-fill me-1"></i> 3. Jasa Terlaris
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-3 fw-bold small <?= $active_tab === 'customers' ? 'active bg-primary' : 'text-dark' ?>" 
             href="<?= BASE_URL ?>/index.php?url=report&tab=customers&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
            <i class="bi bi-people-fill me-1"></i> 4. Customer
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-3 fw-bold small <?= $active_tab === 'technicians' ? 'active bg-primary' : 'text-dark' ?>" 
             href="<?= BASE_URL ?>/index.php?url=report&tab=technicians&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
            <i class="bi bi-wrench-adjustable me-1"></i> 5. Pekerjaan Teknisi
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-3 fw-bold small <?= $active_tab === 'ratings' ? 'active bg-primary' : 'text-dark' ?>" 
             href="<?= BASE_URL ?>/index.php?url=report&tab=ratings&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
            <i class="bi bi-chat-left-quote-fill me-1"></i> 6. Rating Layanan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-3 fw-bold small <?= $active_tab === 'period' ? 'active bg-primary' : 'text-dark' ?>" 
             href="<?= BASE_URL ?>/index.php?url=report&tab=period&filter=<?= $filter_type ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
            <i class="bi bi-graph-up-arrow me-1"></i> 7. Berdasarkan Periode
          </a>
        </li>
      </ul>

      <!-- TAB 1: LAPORAN JUMLAH ORDER -->
      <?php if ($active_tab === 'orders'): ?>
        <div class="row gy-3 mb-4">
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Order Masuk</span>
              <h3 class="fw-extrabold text-primary mb-0"><?= number_format($report_data['total_orders'] ?? 0) ?> Order</h3>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Order Aktif (Proses)</span>
              <h3 class="fw-extrabold text-warning mb-0"><?= number_format($report_data['active_orders'] ?? 0) ?> Order</h3>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Order Selesai</span>
              <h3 class="fw-extrabold text-success mb-0"><?= number_format($report_data['completed_orders'] ?? 0) ?> Order</h3>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-danger">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Order Dibatalkan</span>
              <h3 class="fw-extrabold text-danger mb-0"><?= number_format($report_data['cancelled_orders'] ?? 0) ?> Order</h3>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-table me-2 text-primary"></i> Daftar Rincian Order (<?= htmlspecialchars($report_data['period_label']) ?>)
            </h5>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Total: <?= count($report_data['orders'] ?? []) ?> Tiket</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th class="ps-4">No Tiket Order</th>
                    <th>Nama Pelanggan</th>
                    <th>Jasa Layanan Utama</th>
                    <th>Teknisi Ditugaskan</th>
                    <th class="text-center">Prioritas</th>
                    <th class="text-center">Status Pekerjaan</th>
                    <th class="text-end pe-4">Total Biaya (Rp)</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($report_data['orders'])): ?>
                    <?php foreach ($report_data['orders'] as $ord): ?>
                      <tr>
                        <td class="ps-4 fw-bold text-primary">
                          <i class="bi bi-ticket-perforated me-1"></i> <?= htmlspecialchars($ord['order_code']) ?>
                        </td>
                        <td>
                          <div class="fw-bold text-dark"><?= htmlspecialchars($ord['customer_name'] ?? 'Guest') ?></div>
                          <div class="extra-small text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($ord['customer_phone'] ?? '-') ?></div>
                        </td>
                        <td class="fw-medium text-dark"><?= htmlspecialchars($ord['main_service_name'] ?? 'Jasa Teknis IT') ?></td>
                        <td>
                          <?php if (!empty($ord['technician_name'])): ?>
                            <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($ord['technician_name']) ?></span>
                          <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary">Belum Ditugaskan</span>
                          <?php endif; ?>
                        </td>
                        <td class="text-center">
                          <?php 
                          $prio = strtolower($ord['priority'] ?? 'medium');
                          $prioBadge = 'bg-secondary';
                          if ($prio === 'high' || $prio === 'urgent') $prioBadge = 'bg-danger';
                          if ($prio === 'medium') $prioBadge = 'bg-warning text-dark';
                          ?>
                          <span class="badge <?= $prioBadge ?> extra-small text-uppercase"><?= htmlspecialchars($ord['priority'] ?? 'medium') ?></span>
                        </td>
                        <td class="text-center">
                          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1"><?= htmlspecialchars($ord['status']) ?></span>
                        </td>
                        <td class="text-end pe-4 fw-bold text-success">
                          Rp <?= number_format($ord['grand_total'] ?? 0, 0, ',', '.') ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data order pada periode terpilih.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 2: LAPORAN PENDAPATAN -->
      <?php if ($active_tab === 'revenue'): ?>
        <div class="row gy-3 mb-4">
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Omset Gross</span>
              <h3 class="fw-extrabold text-success mb-0">Rp <?= number_format($report_data['gross_revenue'] ?? 0, 0, ',', '.') ?></h3>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Pendapatan Lunas</span>
              <h3 class="fw-extrabold text-primary mb-0">Rp <?= number_format($report_data['paid_revenue'] ?? 0, 0, ',', '.') ?></h3>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Biaya Jasa</span>
              <h3 class="fw-extrabold text-warning mb-0">Rp <?= number_format($report_data['total_service_fee'] ?? 0, 0, ',', '.') ?></h3>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Total Biaya Sparepart</span>
              <h3 class="fw-extrabold text-info mb-0">Rp <?= number_format($report_data['total_sparepart_fee'] ?? 0, 0, ',', '.') ?></h3>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-receipt me-2 text-success"></i> Rincian Invoice & Pendapatan Transaksi (<?= htmlspecialchars($report_data['period_label']) ?>)
            </h5>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Total: <?= count($report_data['transactions'] ?? []) ?> Transaksi</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th class="ps-4">Kode Transaksi</th>
                    <th>No Tiket</th>
                    <th>Nama Customer</th>
                    <th class="text-end">Biaya Jasa (Rp)</th>
                    <th class="text-end">Sparepart (Rp)</th>
                    <th class="text-end">Diskon (Rp)</th>
                    <th class="text-end">Grand Total (Rp)</th>
                    <th class="text-center pe-4">Status Bayar</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($report_data['transactions'])): ?>
                    <?php foreach ($report_data['transactions'] as $trx): ?>
                      <tr>
                        <td class="ps-4 fw-bold text-dark">
                          <i class="bi bi-file-earmark-text text-muted me-1"></i> <?= htmlspecialchars($trx['transaction_code']) ?>
                        </td>
                        <td class="fw-semibold text-primary"><?= htmlspecialchars($trx['order_code']) ?></td>
                        <td class="fw-medium text-dark"><?= htmlspecialchars($trx['customer_name']) ?></td>
                        <td class="text-end">Rp <?= number_format($trx['total_service_fee'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end">Rp <?= number_format($trx['total_sparepart_fee'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end text-danger">- Rp <?= number_format($trx['discount'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-success">Rp <?= number_format($trx['grand_total'] ?? 0, 0, ',', '.') ?></td>
                        <td class="text-center pe-4">
                          <?php if (($trx['payment_status'] ?? '') === 'paid' || ($trx['payment_status'] ?? '') === 'Lunas'): ?>
                            <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i> Lunas</span>
                          <?php else: ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-clock me-1"></i> Belum Dibayar</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data transaksi pendapatan pada periode terpilih.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 3: LAPORAN JASA TERLARIS -->
      <?php if ($active_tab === 'services'): ?>
        <div class="row gy-4 mb-4">
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden h-100">
              <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                  <i class="bi bi-award-fill text-warning me-2"></i> Peringkat Layanan Jasa Paling Banyak Dipesan
                </h5>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light extra-small text-uppercase text-muted">
                      <tr>
                        <th class="ps-4">Peringkat & Nama Layanan Jasa</th>
                        <th>Kategori</th>
                        <th class="text-center">Total Dipesan</th>
                        <th class="text-end pe-4">Omset Dihasilkan (Rp)</th>
                      </tr>
                    </thead>
                    <tbody class="small">
                      <?php if (!empty($report_data['services'])): ?>
                        <?php foreach ($report_data['services'] as $idx => $svc): ?>
                          <tr>
                            <td class="ps-4 fw-bold text-dark">
                              <span class="badge <?= $idx === 0 ? 'bg-warning text-dark' : ($idx === 1 ? 'bg-secondary' : 'bg-light text-dark border') ?> rounded-circle p-2 me-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                #<?= $idx + 1 ?>
                              </span>
                              <?= htmlspecialchars($svc['name']) ?>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($svc['category']) ?></span></td>
                            <td class="text-center fw-bold text-primary"><?= number_format($svc['total_orders']) ?> Order</td>
                            <td class="text-end pe-4 fw-bold text-success">Rp <?= number_format($svc['total_revenue'], 0, ',', '.') ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data pemesanan jasa pada periode terpilih.</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
              <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-pie-chart-fill text-info me-2"></i> Distribusi Per Kategori
              </h5>
              <div class="list-group list-group-flush">
                <?php if (!empty($report_data['categories'])): ?>
                  <?php foreach ($report_data['categories'] as $cat): ?>
                    <div class="list-group-item px-0 py-3 border-bottom-0">
                      <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fw-bold text-dark small"><?= htmlspecialchars($cat['category']) ?></span>
                        <span class="badge bg-primary rounded-pill"><?= $cat['total_orders'] ?> Order</span>
                      </div>
                      <div class="text-muted extra-small text-end fw-semibold text-success">
                        Rp <?= number_format($cat['total_revenue'], 0, ',', '.') ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="text-muted small text-center py-3">Belum ada kategori terdata.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 4: LAPORAN CUSTOMER -->
      <?php if ($active_tab === 'customers'): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-people-fill me-2 text-info"></i> Laporan Demografi & Keaktifan Customer (<?= htmlspecialchars($report_data['period_label']) ?>)
            </h5>
            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">Total: <?= count($report_data['customers'] ?? []) ?> Customer</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th class="ps-4">Nama Pelanggan / Institusi</th>
                    <th>Email & Telepon</th>
                    <th>Tipe Customer</th>
                    <th>Kota Alamat</th>
                    <th class="text-center">Total Order</th>
                    <th class="text-end pe-4">Total Spending (Rp)</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($report_data['customers'])): ?>
                    <?php foreach ($report_data['customers'] as $cust): ?>
                      <tr>
                        <td class="ps-4 fw-bold text-dark">
                          <i class="bi bi-person-circle text-primary me-2"></i> <?= htmlspecialchars($cust['full_name']) ?>
                        </td>
                        <td>
                          <div><?= htmlspecialchars($cust['email']) ?></div>
                          <div class="extra-small text-muted"><?= htmlspecialchars($cust['phone'] ?? '-') ?></div>
                        </td>
                        <td>
                          <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-uppercase extra-small">
                            <?= htmlspecialchars($cust['customer_type'] ?? 'umum') ?>
                          </span>
                        </td>
                        <td class="text-muted"><?= htmlspecialchars($cust['city'] ?? 'Kota Bandung') ?></td>
                        <td class="text-center fw-bold text-primary"><?= number_format($cust['total_orders']) ?> Order</td>
                        <td class="text-end pe-4 fw-bold text-success">Rp <?= number_format($cust['total_spent'] ?? 0, 0, ',', '.') ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data customer terdaftar.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 5: LAPORAN PEKERJAAN TEKNISI -->
      <?php if ($active_tab === 'technicians'): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-wrench-adjustable-circle me-2 text-warning"></i> Laporan Evaluasi Kinerja Teknisi Siswa TKJ (<?= htmlspecialchars($report_data['period_label']) ?>)
            </h5>
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Total: <?= count($report_data['technicians'] ?? []) ?> Siswa Teknisi</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th class="ps-4">Nama Teknisi Siswa</th>
                    <th>NIS & Kelas</th>
                    <th>Spesialisasi Keahlian</th>
                    <th class="text-center">Ditugaskan</th>
                    <th class="text-center">Tugas Selesai</th>
                    <th class="text-center">Sedang Berjalan</th>
                    <th class="text-center pe-4">Rating Rata-rata</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($report_data['technicians'])): ?>
                    <?php foreach ($report_data['technicians'] as $tech): ?>
                      <tr>
                        <td class="ps-4 fw-bold text-dark">
                          <i class="bi bi-person-workspace text-primary me-2"></i> <?= htmlspecialchars($tech['full_name']) ?>
                        </td>
                        <td>
                          <span class="fw-semibold text-dark"><?= htmlspecialchars($tech['nis_nip']) ?></span> (<?= htmlspecialchars($tech['class_grade']) ?>)
                        </td>
                        <td>
                          <span class="badge bg-info-subtle text-info border border-info-subtle extra-small"><?= htmlspecialchars($tech['specialization']) ?></span>
                        </td>
                        <td class="text-center fw-bold text-dark"><?= number_format($tech['total_assigned']) ?> Task</td>
                        <td class="text-center fw-bold text-success"><?= number_format($tech['total_completed']) ?> Task</td>
                        <td class="text-center fw-bold text-warning"><?= number_format($tech['total_ongoing']) ?> Task</td>
                        <td class="text-center pe-4">
                          <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill">
                            <i class="bi bi-star-fill me-1"></i> <?= number_format($tech['avg_rating'], 1) ?> / 5.0
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data evaluasi teknisi pada periode terpilih.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 6: LAPORAN RATING LAYANAN -->
      <?php if ($active_tab === 'ratings'): ?>
        <div class="row gy-4 mb-4">
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 text-center">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Skor Rating Rata-rata TEFA</span>
              <h1 class="display-3 fw-extrabold text-warning my-2">
                <?= number_format($report_data['avg_rating'] ?? 5.0, 1) ?>
              </h1>
              <div class="text-warning fs-4 mb-2">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
              </div>
              <p class="text-muted small mb-0">Berdasarkan <strong><?= number_format($report_data['total_reviews'] ?? 0) ?></strong> ulasan kualitatif pelanggan.</p>
              <div class="mt-3">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                  <i class="bi bi-hand-thumbs-up-fill me-1"></i> <?= $report_data['satisfaction_rate'] ?? 100 ?>% Kepuasan Pelanggan
                </span>
              </div>
            </div>
          </div>

          <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
              <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-star-fill text-warning me-2"></i> Distribusi Bintang Penilaian
              </h5>
              <?php 
              $breakdown = $report_data['stars_breakdown'] ?? [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
              $totalRev = max(1, $report_data['total_reviews'] ?? 1);
              for ($s = 5; $s >= 1; $s--):
                $count = $breakdown[$s] ?? 0;
                $pct = round(($count / $totalRev) * 100);
              ?>
                <div class="d-flex align-items-center gap-3 mb-2">
                  <span class="fw-bold text-dark small" style="width: 70px;"><?= $s ?> Bintang</span>
                  <div class="progress flex-grow-1 bg-light rounded-pill" style="height: 12px;">
                    <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: <?= $pct ?>%;"></div>
                  </div>
                  <span class="text-muted extra-small fw-bold" style="width: 50px; text-align: right;"><?= $count ?> review</span>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
          <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-chat-left-text-fill text-primary me-2"></i> Ulasan Kualitatif & Feedback Pelanggan
            </h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th class="ps-4">No Tiket & Customer</th>
                    <th>Layanan & Teknisi</th>
                    <th class="text-center">Rating</th>
                    <th>Komentar & Feedback</th>
                    <th class="pe-4 text-end">Tanggal</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($report_data['ratings'])): ?>
                    <?php foreach ($report_data['ratings'] as $rt): ?>
                      <tr>
                        <td class="ps-4">
                          <div class="fw-bold text-primary"><?= htmlspecialchars($rt['order_code']) ?></div>
                          <div class="text-dark fw-semibold"><?= htmlspecialchars($rt['customer_name']) ?></div>
                        </td>
                        <td>
                          <div class="fw-medium text-dark"><?= htmlspecialchars($rt['service_name'] ?? 'Jasa Teknis IT') ?></div>
                          <div class="extra-small text-muted"><i class="bi bi-person-badge me-1"></i>Teknisi: <?= htmlspecialchars($rt['technician_name'] ?? 'N/A') ?></div>
                        </td>
                        <td class="text-center">
                          <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                            <?= $rt['stars'] ?> <i class="bi bi-star-fill ms-1"></i>
                          </span>
                        </td>
                        <td class="max-w-500">
                          <div class="text-dark small"><?= nl2br(htmlspecialchars($rt['comment'] ?? '')) ?></div>
                        </td>
                        <td class="pe-4 text-end text-muted extra-small">
                          <?= date('d M Y', strtotime($rt['created_at'])) ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada ulasan rating pada periode terpilih.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- TAB 7: LAPORAN BERDASARKAN PERIODE -->
      <?php if ($active_tab === 'period'): ?>
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bi bi-graph-up-arrow text-success me-2"></i> Rekapitulasi Analisis Tren Waktu (<?= htmlspecialchars($report_data['period_label']) ?>)
            </h5>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Total Periode: <?= count($report_data['timeline'] ?? []) ?> Baris</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light extra-small text-uppercase text-muted">
                  <tr>
                    <th class="ps-4">Periode Waktu</th>
                    <th class="text-center">Total Order Masuk</th>
                    <th class="text-center">Order Selesai</th>
                    <th class="text-end">Gross Omset (Rp)</th>
                    <th class="text-end">Revenue Lunas (Rp)</th>
                    <th class="text-center pe-4">Rating Rata-rata</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php if (!empty($report_data['timeline'])): ?>
                    <?php foreach ($report_data['timeline'] as $row): ?>
                      <tr>
                        <td class="ps-4 fw-bold text-dark">
                          <i class="bi bi-calendar3 text-primary me-2"></i> <?= htmlspecialchars($row['period_date']) ?>
                        </td>
                        <td class="text-center fw-bold text-primary"><?= number_format($row['total_orders']) ?> Order</td>
                        <td class="text-center fw-bold text-success"><?= number_format($row['completed_orders']) ?> Order</td>
                        <td class="text-end fw-bold text-dark">Rp <?= number_format($row['gross_revenue'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-success">Rp <?= number_format($row['paid_revenue'], 0, ',', '.') ?></td>
                        <td class="text-center pe-4">
                          <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                            <i class="bi bi-star-fill me-1"></i> <?= number_format($row['avg_rating'], 1) ?>
                          </span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data garis waktu pada periode terpilih.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
