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
        <div class="card-body p-4 p-md-5 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
          <div>
            <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold text-uppercase mb-2">
              <i class="bi bi-clock-history me-1"></i> Customer Order Tracker
            </span>
            <h2 class="fw-extrabold mb-1">Daftar & Status Pesanan Saya</h2>
            <p class="text-light opacity-75 mb-0 small">Pantau perkembangan tiket pengerjaan jasa teknis yang telah Anda ajukan.</p>
          </div>
          <div>
            <a href="<?= BASE_URL ?>/index.php?url=order/create" class="btn btn-primary-gradient text-white btn-lg rounded-pill shadow-sm px-4 fw-bold">
              <i class="bi bi-plus-circle-fill me-2"></i> Ajukan Jasa Baru
            </a>
          </div>
        </div>
      </div>

      <!-- Search & Status Filter -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 align-items-center">
          <input type="hidden" name="url" value="order/my_orders">

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nomor tiket / nama jasa..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
          </div>

          <div class="col-md-4">
            <select name="status" class="form-select bg-light">
              <option value="">-- Semua Status Order --</option>
              <?php
                $statusList = [
                  'Diajukan', 'Menunggu Verifikasi', 'Disetujui', 'Teknisi Ditugaskan', 
                  'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart', 
                  'Selesai', 'Ditutup', 'Dibatalkan'
                ];
                foreach ($statusList as $st):
              ?>
                <option value="<?= $st ?>" <?= ($selected_status ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 rounded-3">Filter</button>
            <?php if (!empty($search) || !empty($selected_status)): ?>
              <a href="<?= BASE_URL ?>/index.php?url=order/my_orders" class="btn btn-outline-secondary rounded-3">Reset</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- Orders Data Table -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-check me-2 text-primary"></i> Tiket Order Saya (<?= count($orders ?? []) ?> Tiket)
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">No. Tiket</th>
                  <th>Layanan Jasa & Subjek</th>
                  <th>Tanggal Diinginkan</th>
                  <th>Status Pengerjaan</th>
                  <th class="pe-4 text-end">Aksi Tiket</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($orders)): ?>
                  <?php foreach ($orders as $ord): 
                    $st = $ord['status'];
                    $badgeClass = 'bg-secondary-subtle text-secondary';
                    if ($st === 'Diajukan') $badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                    else if ($st === 'Menunggu Verifikasi') $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                    else if ($st === 'Disetujui' || $st === 'Teknisi Ditugaskan' || $st === 'Dijadwalkan') $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                    else if ($st === 'Dalam Pengerjaan') $badgeClass = 'bg-primary text-white';
                    else if ($st === 'Menunggu Customer/Sparepart') $badgeClass = 'bg-warning text-dark';
                    else if ($st === 'Selesai' || $st === 'Ditutup') $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                    else if ($st === 'Dibatalkan') $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                  ?>
                    <tr>
                      <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($ord['order_code']) ?></td>
                      <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($ord['service_name']) ?></div>
                        <span class="extra-small text-muted line-clamp-1" style="max-width: 320px; display: block;">
                          <?= htmlspecialchars($ord['subject'] ?? $ord['problem_description']) ?>
                        </span>
                      </td>
                      <td><?= date('d M Y', strtotime($ord['scheduled_date'])) ?></td>
                      <td>
                        <span class="badge <?= $badgeClass ?> px-2 py-1">
                          <i class="bi bi-circle-fill me-1 extra-small"></i> <?= htmlspecialchars($st) ?>
                        </span>
                      </td>
                      <td class="pe-4 text-end">
                        <div class="btn-group btn-group-sm">
                          <a href="<?= BASE_URL ?>/index.php?url=order/detail/<?= $ord['id'] ?>" class="btn btn-outline-primary fw-semibold">
                            <i class="bi bi-eye me-1"></i> Rincian
                          </a>
                          <?php if (in_array($st, ['Diajukan', 'Menunggu Verifikasi', 'Disetujui'])): ?>
                            <a href="<?= BASE_URL ?>/index.php?url=order/cancel/<?= $ord['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan pengajuan order ini?')" title="Batalkan Order">
                              Batalkan
                            </a>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center py-5">
                      <i class="bi bi-inbox text-muted display-4 d-block mb-2"></i>
                      <div class="fw-bold text-dark mb-1">Belum Ada Tiket Pesanan</div>
                      <p class="text-muted small mb-3">Anda belum membuat pengajuan layanan jasa teknis.</p>
                      <a href="<?= BASE_URL ?>/index.php?url=order/create" class="btn btn-primary btn-sm rounded-pill px-4">
                        Buat Pengajuan Sekarang
                      </a>
                    </td>
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
