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
            <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold text-uppercase">
              <i class="bi bi-graph-up-arrow me-1"></i> Laporan Keuangan TEFA
            </span>
            <span class="text-white-50 extra-small"><i class="bi bi-bank me-1"></i> Teaching Factory Unit TKJ</span>
          </div>
          <h2 class="fw-extrabold mb-1">Laporan Pendapatan Unit Bisnis Jasa</h2>
          <p class="text-light opacity-75 mb-0 small">Ringkasan transaksi lunas, total omset TEFA, rincian biaya jasa, sparepart, & pendapatan bersih.</p>
        </div>
      </div>

      <!-- KPI Summary Cards -->
      <div class="row gy-3 mb-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small d-block font-weight-bold text-uppercase">Total Omset Pendapatan Lunas</span>
                <h3 class="fw-extrabold text-success mb-0">Rp <?= number_format($total_revenue ?? 0, 0, ',', '.') ?></h3>
              </div>
              <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                <i class="bi bi-wallet2"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small d-block font-weight-bold text-uppercase">Transaksi Lunas Terverifikasi</span>
                <h3 class="fw-extrabold text-primary mb-0"><?= htmlspecialchars($paid_count ?? 0) ?> Transaksi</h3>
              </div>
              <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                <i class="bi bi-check-circle-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small d-block font-weight-bold text-uppercase">Status Evaluasi Keuangan</span>
                <h3 class="fw-extrabold text-info mb-0">Sangat Sehat</h3>
              </div>
              <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                <i class="bi bi-award-fill"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Transactions Report Table -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-receipt-cutoff me-2 text-success"></i> Riwayat Transaksi & Invoice Unit Jasa (<?= count($reports ?? []) ?> Record)
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">No. Transaksi</th>
                  <th>Tiket & Layanan</th>
                  <th>Customer Pemesan</th>
                  <th>Biaya Jasa</th>
                  <th>Biaya Part</th>
                  <th>Diskon</th>
                  <th>Grand Total</th>
                  <th>Status Bayar</th>
                  <th class="pe-4 text-end">Aksi Invoice</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($reports)): ?>
                  <?php foreach ($reports as $r): 
                    $pSt = $r['payment_status'] ?? 'Belum Dibayar';
                    $pBadgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                    if ($pSt === 'Menunggu Verifikasi') $pBadgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                    else if ($pSt === 'Lunas') $pBadgeClass = 'bg-success-subtle text-success border border-success-subtle';
                  ?>
                    <tr>
                      <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($r['transaction_code']) ?></td>
                      <td>
                        <div class="fw-bold text-primary"><?= htmlspecialchars($r['order_code']) ?></div>
                        <span class="extra-small text-muted"><?= htmlspecialchars($r['service_name']) ?></span>
                      </td>
                      <td><?= htmlspecialchars($r['customer_name']) ?></td>
                      <td>Rp <?= number_format($r['total_service_fee'] ?? 0, 0, ',', '.') ?></td>
                      <td>Rp <?= number_format($r['total_sparepart_fee'] ?? 0, 0, ',', '.') ?></td>
                      <td class="text-success">- Rp <?= number_format($r['discount'] ?? 0, 0, ',', '.') ?></td>
                      <td class="fw-bold text-dark">Rp <?= number_format($r['grand_total'] ?? 0, 0, ',', '.') ?></td>
                      <td>
                        <span class="badge <?= $pBadgeClass ?> px-2 py-1">
                          <?= htmlspecialchars($pSt) ?>
                        </span>
                      </td>
                      <td class="pe-4 text-end">
                        <a href="<?= BASE_URL ?>/index.php?url=transaction/invoice/<?= $r['order_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                          Buka Invoice
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="9" class="text-center py-4 text-muted">Belum ada riwayat transaksi lunas.</td>
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
