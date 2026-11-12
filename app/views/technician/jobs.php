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
              <i class="bi bi-briefcase-fill me-1"></i> Workspace Teknisi Siswa
            </span>
            <span class="text-white-50 extra-small"><i class="bi bi-person-workspace me-1"></i> Teaching Factory TKJ</span>
          </div>
          <h2 class="fw-extrabold mb-1">Daftar Tugas Lapangan Saya</h2>
          <p class="text-light opacity-75 mb-0 small">Terima penugasan baru dari operator, lihat alamat customer, & perbarui status pengerjaan secara realtime.</p>
        </div>
      </div>

      <!-- Jobs Grid -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-stars me-2 text-info"></i> Penugasan Pekerjaan Lapangan (<?= count($jobs ?? []) ?> Job)
          </h5>
          <a href="<?= BASE_URL ?>/index.php?url=technician/schedule" class="btn btn-sm btn-outline-info rounded-pill">
            <i class="bi bi-calendar-range me-1"></i> Lihat Agenda Jadwal
          </a>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">No. Tiket</th>
                  <th>Layanan Jasa & Pemesan</th>
                  <th>Alamat Lokasi</th>
                  <th>Jadwal Kerja</th>
                  <th>Status Job</th>
                  <th class="pe-4 text-end">Aksi Pekerjaan</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($jobs)): ?>
                  <?php foreach ($jobs as $j): 
                    $st = $j['status'];
                    $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                    if ($st === 'Teknisi Ditugaskan') $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                    else if ($st === 'Dalam Pengerjaan') $badgeClass = 'bg-primary text-white';
                    else if ($st === 'Menunggu Customer/Sparepart') $badgeClass = 'bg-warning text-dark';
                    else if ($st === 'Selesai' || $st === 'Ditutup') $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                  ?>
                    <tr>
                      <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($j['order_code']) ?></td>
                      <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($j['service_name']) ?></div>
                        <div class="extra-small text-muted"><i class="bi bi-person me-1"></i> <?= htmlspecialchars($j['customer_name']) ?> (<?= htmlspecialchars($j['customer_phone']) ?>)</div>
                      </td>
                      <td>
                        <span class="extra-small text-dark d-block" style="max-width: 250px;">
                          <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($j['service_address']) ?>
                        </span>
                      </td>
                      <td>
                        <div class="fw-semibold text-dark"><i class="bi bi-calendar-event text-warning me-1"></i> <?= date('d M Y', strtotime($j['scheduled_date'])) ?></div>
                        <span class="extra-small text-muted">Jam: <?= htmlspecialchars($j['start_time'] ?? '09:00') ?> - <?= htmlspecialchars($j['end_time'] ?? '12:00') ?></span>
                      </td>
                      <td>
                        <span class="badge <?= $badgeClass ?> px-2 py-1">
                          <?= htmlspecialchars($st) ?>
                        </span>
                      </td>
                      <td class="pe-4 text-end">
                        <div class="btn-group btn-group-sm">
                          <?php if ($st === 'Teknisi Ditugaskan'): ?>
                            <a href="<?= BASE_URL ?>/index.php?url=technician/acceptJob/<?= $j['id'] ?>" class="btn btn-success fw-bold">
                              <i class="bi bi-check-lg me-1"></i> Terima Job
                            </a>
                          <?php endif; ?>
                          <a href="<?= BASE_URL ?>/index.php?url=technician/detail/<?= $j['id'] ?>" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> Detail & Update
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada penugasan pekerjaan lapangan.</td>
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
