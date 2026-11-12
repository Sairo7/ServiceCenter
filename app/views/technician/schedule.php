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
              <i class="bi bi-calendar-range me-1"></i> Agenda Teknisi Siswa
            </span>
          </div>
          <h2 class="fw-extrabold mb-1">Jadwal Agenda Pekerjaan Saya</h2>
          <p class="text-light opacity-75 mb-0 small">Pantau kalender jadwal waktu pengerjaan untuk menghindari tabrakan tugas lapangan.</p>
        </div>
      </div>

      <!-- Schedule Table -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-calendar-check me-2 text-primary"></i> Agenda Kalender Kerja (<?= count($schedules ?? []) ?> Event)
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">Tanggal Pekerjaan</th>
                  <th>Jam Kerja</th>
                  <th>No. Tiket Order</th>
                  <th>Layanan Jasa & Customer</th>
                  <th>Instruksi / Catatan Operator</th>
                  <th class="pe-4 text-end">Aksi Agenda</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($schedules)): ?>
                  <?php foreach ($schedules as $sch): ?>
                    <tr>
                      <td class="ps-4 fw-bold text-dark"><i class="bi bi-calendar3 me-1 text-warning"></i> <?= date('d F Y', strtotime($sch['work_date'])) ?></td>
                      <td class="fw-semibold text-primary"><i class="bi bi-clock me-1"></i> <?= substr($sch['start_time'], 0, 5) ?> - <?= substr($sch['end_time'], 0, 5) ?> WIB</td>
                      <td class="fw-bold text-primary"><?= htmlspecialchars($sch['order_code']) ?></td>
                      <td>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($sch['service_name']) ?></div>
                        <div class="extra-small text-muted"><?= htmlspecialchars($sch['customer_name']) ?></div>
                      </td>
                      <td class="extra-small text-muted"><?= htmlspecialchars($sch['notes'] ?: 'Tidak ada catatan.') ?></td>
                      <td class="pe-4 text-end">
                        <a href="<?= BASE_URL ?>/index.php?url=technician/detail/<?= $sch['order_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                          Buka Detail
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada agenda jadwal kerja tersimpan.</td>
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
