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
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-2">
              <i class="bi bi-inbox-fill me-1"></i> Control Panel Operator
            </span>
            <h2 class="fw-extrabold mb-1">Pengelolaan Seluruh Order Masuk</h2>
            <p class="text-light opacity-75 mb-0 small">Verifikasi pengajuan customer, perbarui 10 tahapan alur status order, dan tunjuk teknisi siswa.</p>
          </div>
        </div>
      </div>

      <!-- Search & Multi-Filter Card -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 align-items-center">
          <input type="hidden" name="url" value="order/manage">

          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Cari tiket, pemesan, lokasi..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
          </div>

          <div class="col-md-3">
            <select name="status" class="form-select bg-light">
              <option value="">-- Filter Status (10 Alur) --</option>
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

          <div class="col-md-3">
            <select name="service_id" class="form-select bg-light">
              <option value="">-- Filter Jenis Jasa --</option>
              <?php if (!empty($services)): ?>
                <?php foreach ($services as $svc): ?>
                  <option value="<?= $svc['id'] ?>" <?= ($selected_service ?? '') == $svc['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($svc['title'] ?? $svc['name']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100 rounded-3">Filter</button>
            <?php if (!empty($search) || !empty($selected_status) || !empty($selected_service)): ?>
              <a href="<?= BASE_URL ?>/index.php?url=order/manage" class="btn btn-outline-secondary rounded-3">Reset</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- Data Table -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-card-checklist me-2 text-warning"></i> Seluruh Tiket Order Masuk (<?= count($orders ?? []) ?> Tiket)
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">No. Tiket</th>
                  <th>Pemesan & Kontak</th>
                  <th>Layanan Jasa</th>
                  <th>Jadwal</th>
                  <th>Status Order Saat Ini</th>
                  <th class="pe-4 text-end">Update Status & Detail</th>
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
                        <div class="fw-bold text-dark"><?= htmlspecialchars($ord['customer_name']) ?></div>
                        <span class="extra-small text-muted"><i class="bi bi-whatsapp me-1 text-success"></i> <?= htmlspecialchars($ord['customer_phone']) ?></span>
                      </td>
                      <td>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($ord['service_name']) ?></div>
                        <span class="extra-small text-muted line-clamp-1" style="max-width: 250px; display: block;">
                          <?= htmlspecialchars($ord['problem_description']) ?>
                        </span>
                      </td>
                      <td><?= date('d M Y', strtotime($ord['scheduled_date'])) ?></td>
                      <td>
                        <span class="badge <?= $badgeClass ?> px-2 py-1">
                          <?= htmlspecialchars($st) ?>
                        </span>
                      </td>
                      <td class="pe-4 text-end">
                        <div class="d-inline-flex align-items-center gap-2">
                          
                          <!-- Form Quick Update Status -->
                          <form action="<?= BASE_URL ?>/index.php?url=order/updateStatus/<?= $ord['id'] ?>" method="POST" class="d-inline">
                            <select name="status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()" style="width: 170px; font-size: 0.78rem;">
                              <?php foreach ($statusList as $statusOption): ?>
                                <option value="<?= $statusOption ?>" <?= $st === $statusOption ? 'selected' : '' ?>>
                                  <?= $statusOption ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </form>

                          <a href="<?= BASE_URL ?>/index.php?url=order/detail/<?= $ord['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-eye"></i> Detail
                          </a>

                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada order masuk.</td>
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
