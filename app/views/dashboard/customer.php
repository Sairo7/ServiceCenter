<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    <!-- Sidebar Area -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Content Area -->
    <div class="col-lg-9 col-xl-10">
      
      <?php Flasher::flash(); ?>

      <!-- Welcome Banner -->
      <div class="card border-0 bg-dark-gradient text-white rounded-4 shadow-sm mb-4 overflow-hidden position-relative">
        <div class="card-body p-4 p-md-5 z-1 position-relative">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold text-uppercase">
              <i class="bi bi-person-fill me-1"></i> Dashboard Customer
            </span>
          </div>
          <h2 class="display-6 fw-extrabold mb-1">Selamat Datang, <?= htmlspecialchars($user['name']) ?>!</h2>
          <p class="text-light opacity-75 mb-3 lead">Pantau progres perbaikan IT, status penugasan teknisi, dan tagihan invoice Anda di sini.</p>
          <a href="<?= BASE_URL ?>/index.php?url=order/create" class="btn btn-primary-gradient text-white btn-lg rounded-pill px-4 shadow-sm font-weight-bold">
            <i class="bi bi-plus-circle-fill me-2"></i> Buat Pengajuan Layanan Jasa
          </a>
        </div>
      </div>

      <!-- 4 Customer KPI Cards -->
      <div class="row gy-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small d-block font-weight-bold text-uppercase">Total Order</span>
                <h3 class="fw-extrabold text-primary mb-0"><?= htmlspecialchars($stats['total_orders'] ?? 0) ?></h3>
              </div>
              <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                <i class="bi bi-ticket-perforated-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small d-block font-weight-bold text-uppercase">Order Aktif (Berjalan)</span>
                <h3 class="fw-extrabold text-warning mb-0"><?= htmlspecialchars($stats['active_orders'] ?? 0) ?></h3>
              </div>
              <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                <i class="bi bi-gear-wide-connected"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small d-block font-weight-bold text-uppercase">Order Selesai</span>
                <h3 class="fw-extrabold text-success mb-0"><?= htmlspecialchars($stats['completed_orders'] ?? 0) ?></h3>
              </div>
              <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                <i class="bi bi-check-circle-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small d-block font-weight-bold text-uppercase">Tagihan Unpaid</span>
                <h3 class="fw-extrabold text-danger mb-0">Rp <?= number_format($stats['unpaid_amount'] ?? 0, 0, ',', '.') ?></h3>
              </div>
              <div class="bg-danger-subtle text-danger p-3 rounded-circle fs-3">
                <i class="bi bi-wallet2"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders History Table -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-clock-history me-2 text-primary"></i> Riwayat Layanan Pesanan Terbaru
          </h5>
          <a href="<?= BASE_URL ?>/index.php?url=order/my_orders" class="btn btn-sm btn-outline-primary rounded-pill">
            Lihat Semua Pesanan Saya
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">No. Tiket</th>
                  <th>Layanan Jasa</th>
                  <th>Tanggal Pengajuan</th>
                  <th>Teknisi Ditugaskan</th>
                  <th>Status Order</th>
                  <th class="pe-4 text-end">Aksi Ticket</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($stats['recent_orders'])): ?>
                  <?php foreach ($stats['recent_orders'] as $ord): 
                    $st = $ord['status'];
                    $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                    if ($st === 'Diajukan') $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                    else if ($st === 'Disetujui' || $st === 'Teknisi Ditugaskan' || $st === 'Dalam Pengerjaan') $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                    else if ($st === 'Selesai' || $st === 'Ditutup') $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                    else if ($st === 'Dibatalkan') $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                  ?>
                    <tr>
                      <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($ord['order_code']) ?></td>
                      <td class="fw-bold text-dark"><?= htmlspecialchars($ord['service_name']) ?></td>
                      <td><?= date('d M Y H:i', strtotime($ord['created_at'])) ?></td>
                      <td>
                        <span class="badge bg-light text-dark border extra-small">
                          <i class="bi bi-person-badge me-1"></i> <?= htmlspecialchars($ord['technician_name'] ?? 'Menunggu Operator') ?>
                        </span>
                      </td>
                      <td>
                        <span class="badge <?= $badgeClass ?> px-2 py-1">
                          <?= htmlspecialchars($st) ?>
                        </span>
                      </td>
                      <td class="pe-4 text-end">
                        <div class="btn-group btn-group-sm">
                          <a href="<?= BASE_URL ?>/index.php?url=order/detail/<?= $ord['id'] ?>" class="btn btn-outline-primary">
                            Detail Tiket
                          </a>
                          <a href="<?= BASE_URL ?>/index.php?url=transaction/invoice/<?= $ord['id'] ?>" class="btn btn-outline-success">
                            Invoice
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pesanan layanan.</td>
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
