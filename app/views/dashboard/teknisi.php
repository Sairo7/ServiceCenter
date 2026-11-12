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
            <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold text-uppercase">
              <i class="bi bi-tools me-1"></i> Dashboard Teknisi Siswa
            </span>
            <span class="text-white-50 extra-small"><i class="bi bi-person-workspace me-1"></i> Teaching Factory Unit TKJ</span>
          </div>
          <h2 class="fw-extrabold mb-1">Halo, <?= htmlspecialchars($user['name']) ?>!</h2>
          <p class="text-light opacity-75 mb-0 small">Siap melaksanakan tugas perbaikan IT & instalasi lapangan hari ini.</p>
        </div>
      </div>

      <!-- 3 Technician KPI Cards -->
      <div class="row gy-3 mb-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small font-weight-bold text-uppercase d-block">Pekerjaan Hari Ini</span>
                <h3 class="fw-extrabold text-warning mb-0"><?= htmlspecialchars($stats['today_jobs'] ?? 0) ?> Task</h3>
              </div>
              <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                <i class="bi bi-calendar-check-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small font-weight-bold text-uppercase d-block">Pekerjaan Aktif</span>
                <h3 class="fw-extrabold text-primary mb-0"><?= htmlspecialchars($stats['active_jobs'] ?? 0) ?> Task</h3>
              </div>
              <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                <i class="bi bi-briefcase-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted extra-small font-weight-bold text-uppercase d-block">Pekerjaan Selesai</span>
                <h3 class="fw-extrabold text-success mb-0"><?= htmlspecialchars($stats['completed_jobs'] ?? 0) ?> Task</h3>
              </div>
              <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                <i class="bi bi-check-circle-fill"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row gy-4 mb-4">
        <!-- Assigned Jobs List -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
              <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-card-checklist me-2 text-primary"></i> Tugas Lapangan Aktif
              </h5>
              <a href="<?= BASE_URL ?>/index.php?url=technician/jobs" class="btn btn-sm btn-outline-primary rounded-pill">
                Lihat Semua Tugas
              </a>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light extra-small text-uppercase text-muted">
                    <tr>
                      <th class="ps-4">No. Tiket</th>
                      <th>Layanan Jasa & Customer</th>
                      <th>Status Job</th>
                      <th class="pe-4 text-end">Aksi Pekerjaan</th>
                    </tr>
                  </thead>
                  <tbody class="small">
                    <?php if (!empty($stats['assigned_jobs'])): ?>
                      <?php foreach ($stats['assigned_jobs'] as $job): ?>
                        <tr>
                          <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($job['order_code']) ?></td>
                          <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($job['service_name']) ?></div>
                            <div class="extra-small text-muted"><?= htmlspecialchars($job['customer_name']) ?></div>
                          </td>
                          <td>
                            <span class="badge bg-warning text-dark px-2 py-1">
                              <?= htmlspecialchars($job['status']) ?>
                            </span>
                          </td>
                          <td class="pe-4 text-end">
                            <a href="<?= BASE_URL ?>/index.php?url=technician/detail/<?= $job['id'] ?>" class="btn btn-sm btn-primary rounded-pill">
                              Buka Detail
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada tugas lapangan aktif.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Today's Schedule Agenda Widget -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="fw-bold text-dark mb-0"><i class="bi bi-calendar-range me-2 text-info"></i> Agenda Waktu Kerja</h5>
              <a href="<?= BASE_URL ?>/index.php?url=technician/schedule" class="btn btn-sm btn-outline-info rounded-pill">Lihat Agenda</a>
            </div>

            <div class="schedule-list">
              <?php if (!empty($stats['schedules'])): ?>
                <?php foreach ($stats['schedules'] as $sch): ?>
                  <div class="p-3 bg-light rounded-3 border mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="fw-bold text-dark extra-small"><i class="bi bi-clock me-1 text-primary"></i> <?= substr($sch['start_time'], 0, 5) ?> - <?= substr($sch['end_time'], 0, 5) ?> WIB</span>
                      <span class="badge bg-primary extra-small"><?= date('d M Y', strtotime($sch['work_date'])) ?></span>
                    </div>
                    <div class="fw-semibold text-primary extra-small"><?= htmlspecialchars($sch['order_code']) ?> - <?= htmlspecialchars($sch['service_name']) ?></div>
                    <div class="extra-small text-muted"><i class="bi bi-person me-1"></i> Customer: <?= htmlspecialchars($sch['customer_name']) ?></div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-muted extra-small text-center py-4">Belum ada agenda jadwal kerja tersimpan.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>
