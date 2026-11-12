<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    <!-- Sidebar Area -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Content Area -->
    <div class="col-lg-9 col-xl-10">
      
      <?php Flasher::flash(); ?>

      <!-- Header Banner -->
      <div class="card border-0 bg-dark-gradient text-white rounded-4 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase">
              <i class="bi bi-people-fill me-1"></i> Data Pengguna
            </span>
            <span class="text-white-50 extra-small"><i class="bi bi-shield-lock me-1"></i> Akses Khusus Operator TKJ</span>
          </div>
          <h2 class="fw-extrabold mb-1">Daftar Akun & Total Pengguna</h2>
          <p class="text-light opacity-75 mb-0 small">Monitoring seluruh akun pengguna terdaftar pada sistem Unit Bisnis Jasa TKJ Service Center.</p>
        </div>
      </div>

      <!-- Quick Summary Cards -->
      <div class="row gy-3 mb-4">
        <div class="col-6 col-md-4 col-xl">
          <a href="<?= BASE_URL ?>/index.php?url=user" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-4 <?= empty($selected_role) ? 'border-primary shadow' : 'border-secondary' ?>">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Semua Pengguna</span>
              <h3 class="fw-extrabold text-primary mb-0"><?= $role_counts['all'] ?? 0 ?></h3>
              <small class="text-muted extra-small">Total Akun</small>
            </div>
          </a>
        </div>

        <div class="col-6 col-md-4 col-xl">
          <a href="<?= BASE_URL ?>/index.php?url=user&role=customer" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-4 <?= $selected_role === 'customer' ? 'border-success shadow' : 'border-success-subtle' ?>">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Customer</span>
              <h3 class="fw-extrabold text-success mb-0"><?= $role_counts['customer'] ?? 0 ?></h3>
              <small class="text-muted extra-small">Pelanggan</small>
            </div>
          </a>
        </div>

        <div class="col-6 col-md-4 col-xl">
          <a href="<?= BASE_URL ?>/index.php?url=user&role=teknisi" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-4 <?= $selected_role === 'teknisi' ? 'border-info shadow' : 'border-info-subtle' ?>">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Teknisi Siswa</span>
              <h3 class="fw-extrabold text-info mb-0"><?= $role_counts['teknisi'] ?? 0 ?></h3>
              <small class="text-muted extra-small">Pelaksana TEFA</small>
            </div>
          </a>
        </div>

        <div class="col-6 col-md-4 col-xl">
          <a href="<?= BASE_URL ?>/index.php?url=user&role=operator" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-4 <?= $selected_role === 'operator' ? 'border-warning shadow' : 'border-warning-subtle' ?>">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Operator</span>
              <h3 class="fw-extrabold text-warning mb-0"><?= $role_counts['operator'] ?? 0 ?></h3>
              <small class="text-muted extra-small">Pengelola Sistem</small>
            </div>
          </a>
        </div>

        <div class="col-6 col-md-4 col-xl">
          <a href="<?= BASE_URL ?>/index.php?url=user&role=kepala_sekolah" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 text-center border-start border-4 <?= $selected_role === 'kepala_sekolah' ? 'border-danger shadow' : 'border-danger-subtle' ?>">
              <span class="text-muted extra-small font-weight-bold text-uppercase d-block mb-1">Kepala Sekolah</span>
              <h3 class="fw-extrabold text-danger mb-0"><?= $role_counts['kepala_sekolah'] ?? 0 ?></h3>
              <small class="text-muted extra-small">Pimpinan / Eksekutif</small>
            </div>
          </a>
        </div>
      </div>

      <!-- Main Card: Table & Filter -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-4">
          
          <!-- Search & Filter Bar -->
          <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-2 mb-4 align-items-center">
            <input type="hidden" name="url" value="user">
            
            <div class="col-md-5">
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Cari nama, username, email..." value="<?= htmlspecialchars($search ?? '') ?>">
              </div>
            </div>

            <div class="col-md-4">
              <select name="role" class="form-select bg-light">
                <option value="">Semua Role Pengguna</option>
                <option value="customer" <?= ($selected_role ?? '') === 'customer' ? 'selected' : '' ?>>Customer (Pelanggan)</option>
                <option value="teknisi" <?= ($selected_role ?? '') === 'teknisi' ? 'selected' : '' ?>>Teknisi Siswa</option>
                <option value="operator" <?= ($selected_role ?? '') === 'operator' ? 'selected' : '' ?>>Operator</option>
                <option value="kepala_sekolah" <?= ($selected_role ?? '') === 'kepala_sekolah' ? 'selected' : '' ?>>Kepala Sekolah</option>
              </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
              <button type="submit" class="btn btn-primary px-3 rounded-3 flex-grow-1">
                <i class="bi bi-funnel-fill me-1"></i> Filter
              </button>
              <?php if (!empty($search) || !empty($selected_role)): ?>
                <a href="<?= BASE_URL ?>/index.php?url=user" class="btn btn-outline-secondary rounded-3" title="Reset filter">
                  <i class="bi bi-x-circle"></i>
                </a>
              <?php endif; ?>
            </div>
          </form>

          <!-- Users Table -->
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-muted extra-small text-uppercase">
                <tr>
                  <th scope="col" style="width: 50px;">No</th>
                  <th scope="col">Nama Lengkap & Kontak</th>
                  <th scope="col">Username</th>
                  <th scope="col">Role</th>
                  <th scope="col">Status Akun</th>
                  <th scope="col">Terdaftar Sejak</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (empty($users)): ?>
                  <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                      <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                      Tidak ada data pengguna yang sesuai dengan pencarian atau filter.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($users as $index => $u): 
                    $roleBadgeClass = 'bg-secondary';
                    $roleIcon = 'bi-person';
                    $rName = $u['role'] ?? '';
                    
                    if ($rName === 'customer') {
                      $roleBadgeClass = 'bg-success-subtle text-success border border-success-subtle';
                      $roleIcon = 'bi-cart-check';
                    } elseif ($rName === 'teknisi') {
                      $roleBadgeClass = 'bg-info-subtle text-info border border-info-subtle';
                      $roleIcon = 'bi-tools';
                    } elseif ($rName === 'operator') {
                      $roleBadgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                      $roleIcon = 'bi-headset';
                    } elseif ($rName === 'kepala_sekolah') {
                      $roleBadgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                      $roleIcon = 'bi-award';
                    }
                  ?>
                    <tr>
                      <td class="text-muted fw-bold"><?= $index + 1 ?></td>
                      <td>
                        <div class="d-flex align-items-center gap-3">
                          <div class="avatar bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.95rem;">
                            <?= strtoupper(substr($u['full_name'] ?? $u['username'] ?? 'U', 0, 1)) ?>
                          </div>
                          <div>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($u['full_name'] ?? '-') ?></div>
                            <div class="text-muted extra-small">
                              <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($u['email'] ?? '-') ?>
                              <?php if (!empty($u['phone']) && $u['phone'] !== '-'): ?>
                                <span class="ms-2"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($u['phone']) ?></span>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td>
                        <code class="text-dark bg-light px-2 py-1 rounded fw-semibold">@<?= htmlspecialchars($u['username'] ?? '-') ?></code>
                      </td>
                      <td>
                        <span class="badge rounded-pill px-3 py-1 extra-small <?= $roleBadgeClass ?>">
                          <i class="bi <?= $roleIcon ?> me-1"></i><?= htmlspecialchars($u['role_display'] ?? ucfirst(str_replace('_', ' ', $rName))) ?>
                        </span>
                      </td>
                      <td>
                        <?php if (isset($u['is_active']) && $u['is_active'] == 1): ?>
                          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 extra-small">
                            <i class="bi bi-check-circle-fill me-1"></i>Aktif
                          </span>
                        <?php else: ?>
                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 extra-small">
                            <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
                          </span>
                        <?php endif; ?>
                      </td>
                      <td class="text-muted">
                        <?php 
                          $regDate = !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-';
                          echo htmlspecialchars($regDate);
                        ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="mt-3 d-flex justify-content-between align-items-center text-muted extra-small">
            <span>Menampilkan <?= count($users) ?> pengguna</span>
            <span><i class="bi bi-info-circle me-1"></i>Data password, API token, dan kode OTP diamankan dari tampilan.</span>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
