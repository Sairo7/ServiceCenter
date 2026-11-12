<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    <!-- Sidebar Area -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Content Area -->
    <div class="col-lg-9 col-xl-10">
      
      <!-- Flash Alert -->
      <?php Flasher::flash(); ?>

      <!-- Welcome Banner Card -->
      <div class="card border-0 bg-dark-gradient text-white rounded-4 shadow-sm mb-4 overflow-hidden position-relative">
        <div class="card-body p-4 p-md-5 position-relative z-1">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary px-3 py-2 rounded-pill text-uppercase font-weight-bold" style="letter-spacing: 0.5px;">
              Role: <?= htmlspecialchars(str_replace('_', ' ', $user_role)) ?>
            </span>
            <span class="text-white-50 extra-small"><i class="bi bi-clock me-1"></i> Sesi Aktif</span>
          </div>
          <h2 class="fw-extrabold mb-2">Selamat Datang, <?= htmlspecialchars($user_name) ?>!</h2>
          <p class="text-light opacity-75 mb-0 max-w-700">
            Anda berada di panel kerja <strong>TKJ Service Business Web App</strong>. Sistem ini membantu pengelolaan pemesanan, penjadwalan teknisi, dan pemantauan kualitatif unit jasa sekolah.
          </p>
        </div>
      </div>

      <!-- Role Specific KPI Summary Cards -->
      <div class="row gy-3 mb-4">

        <?php if ($user_role === 'customer'): ?>
          <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Pesanan Aktif</span>
                  <h3 class="fw-extrabold text-primary mb-0">1</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                  <i class="bi bi-hourglass-split"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Selesai Bergaransi</span>
                  <h3 class="fw-extrabold text-success mb-0">4</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                  <i class="bi bi-check-circle-fill"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Total Transaksi</span>
                  <h3 class="fw-extrabold text-dark mb-0">Rp 450.000</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                  <i class="bi bi-wallet2"></i>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($user_role === 'operator'): ?>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Order Masuk</span>
                  <h3 class="fw-extrabold text-warning mb-0">3</h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                  <i class="bi bi-inbox-fill"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Teknisi Bertugas</span>
                  <h3 class="fw-extrabold text-info mb-0">5 Siswa</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                  <i class="bi bi-people-fill"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Dalam Proses</span>
                  <h3 class="fw-extrabold text-primary mb-0">2</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                  <i class="bi bi-gear-fill"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Omset Bulan Ini</span>
                  <h3 class="fw-extrabold text-success mb-0">Rp 2.450.000</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                  <i class="bi bi-cash-stack"></i>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($user_role === 'teknisi'): ?>
          <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Tugas Aktif Lapangan</span>
                  <h3 class="fw-extrabold text-primary mb-0">2 Job</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                  <i class="bi bi-wrench-adjustable-circle-fill"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Selesai Dikerjakan</span>
                  <h3 class="fw-extrabold text-success mb-0">12 Job</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                  <i class="bi bi-check-all"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Rating Kinerja Siswa</span>
                  <h3 class="fw-extrabold text-warning mb-0">4.9 / 5.0</h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                  <i class="bi bi-star-fill"></i>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($user_role === 'kepala_sekolah'): ?>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Total Unit Jasa Ditangani</span>
                  <h3 class="fw-extrabold text-primary mb-0">158</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                  <i class="bi bi-bar-chart-line-fill"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Siswa Terlibat</span>
                  <h3 class="fw-extrabold text-info mb-0">24 Siswa</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                  <i class="bi bi-mortarboard-fill"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Total Pendapatan TEFA</span>
                  <h3 class="fw-extrabold text-success mb-0">Rp 18.250.000</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                  <i class="bi bi-bank"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <span class="text-muted extra-small d-block fw-semibold text-uppercase">Status Evaluasi</span>
                  <h3 class="fw-extrabold text-danger mb-0">Sangat Baik</h3>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-circle fs-4">
                  <i class="bi bi-award-fill"></i>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <!-- Table Preview Activity -->
      <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-stars me-2 text-primary"></i> Aktivitas & Transaksi Jasa Terkini
          </h5>
          <span class="badge bg-light text-dark border">Realtime Log</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr class="extra-small text-uppercase text-muted">
                  <th class="ps-4">Kode Order</th>
                  <th>Layanan Jasa</th>
                  <th>Pemesan / Lokasi</th>
                  <th>Teknisi Siswa</th>
                  <th>Status</th>
                  <th class="pe-4 text-end">Aksi</th>
                </tr>
              </thead>
              <tbody class="small">
                <tr>
                  <td class="ps-4 fw-bold text-primary">ORD-202608-001</td>
                  <td>
                    <div class="fw-semibold">Instalasi CCTV & Security Camera</div>
                    <span class="extra-small text-muted">Keamanan Jaringan</span>
                  </td>
                  <td>
                    <div>Ahmad Pelanggan</div>
                    <span class="extra-small text-muted">Jl. Merdeka No. 12</span>
                  </td>
                  <td>
                    <span class="badge bg-info-subtle text-info"><i class="bi bi-person me-1"></i> Budi Teknisi</span>
                  </td>
                  <td>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                      <i class="bi bi-gear-fill me-1"></i> In Progress
                    </span>
                  </td>
                  <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i> Detail</button>
                  </td>
                </tr>
                <tr>
                  <td class="ps-4 fw-bold text-primary">ORD-202608-002</td>
                  <td>
                    <div class="fw-semibold">Konfigurasi Router & Bandwidth</div>
                    <span class="extra-small text-muted">Network Management</span>
                  </td>
                  <td>
                    <div>Lab Komputer 1</div>
                    <span class="extra-small text-muted">Gedung B Sekolah</span>
                  </td>
                  <td>
                    <span class="badge bg-info-subtle text-info"><i class="bi bi-person me-1"></i> Budi Teknisi</span>
                  </td>
                  <td>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                      <i class="bi bi-person-check me-1"></i> Assigned
                    </span>
                  </td>
                  <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i> Detail</button>
                  </td>
                </tr>
                <tr>
                  <td class="ps-4 fw-bold text-primary">ORD-202608-003</td>
                  <td>
                    <div class="fw-semibold">Service PC / Laptop Hardware</div>
                    <span class="extra-small text-muted">Maintenance Hardware</span>
                  </td>
                  <td>
                    <div>Siti Perkantoran</div>
                    <span class="extra-small text-muted">Jl. Pemuda No. 45</span>
                  </td>
                  <td>
                    <span class="badge bg-secondary-subtle text-secondary">Belum Ditunjuk</span>
                  </td>
                  <td>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                      <i class="bi bi-clock me-1"></i> Pending
                    </span>
                  </td>
                  <td class="pe-4 text-end">
                    <button class="btn btn-sm btn-light border"><i class="bi bi-eye"></i> Detail</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
