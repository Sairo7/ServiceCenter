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
              <i class="bi bi-gear-fill me-1"></i> Panel Operator TKJ
            </span>
            <h2 class="fw-extrabold mb-1">Manajemen Katalog Jasa Teknis</h2>
            <p class="text-light opacity-75 mb-0 small">Tambah, edit, hapus, dan atur status aktif/nonaktif jenis layanan jasa sekolah.</p>
          </div>
          <div>
            <button type="button" class="btn btn-primary-gradient text-white btn-lg rounded-pill shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddService">
              <i class="bi bi-plus-circle-fill me-2"></i> Tambah Jasa Baru
            </button>
          </div>
        </div>
      </div>

      <!-- Search & Filter Card -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 align-items-center">
          <input type="hidden" name="url" value="service/manage">

          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Cari kode/nama jasa..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
          </div>

          <div class="col-md-4">
            <select name="cat" class="form-select bg-light">
              <option value="">-- Semua Kategori Jasa --</option>
              <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= htmlspecialchars($cat) ?>" <?= ($selected_category ?? '') === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 rounded-3">Filter</button>
            <?php if (!empty($search) || !empty($selected_category)): ?>
              <a href="<?= BASE_URL ?>/index.php?url=service/manage" class="btn btn-outline-secondary rounded-3">Reset</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- Data Table -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-table me-2 text-primary"></i> Daftar Master Jasa Teknis (<?= count($services ?? []) ?> Item)
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">Kode</th>
                  <th>Nama Layanan Jasa</th>
                  <th>Kategori</th>
                  <th>Harga Mulai</th>
                  <th>Est. Waktu</th>
                  <th>Status</th>
                  <th class="pe-4 text-end">Aksi Operator</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($services)): ?>
                  <?php foreach ($services as $s): ?>
                    <tr>
                      <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($s['code'] ?? 'SVC-000') ?></td>
                      <td>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($s['title'] ?? $s['name']) ?></div>
                        <span class="extra-small text-muted line-clamp-1" style="max-width: 280px; display: block;">
                          <?= htmlspecialchars($s['description']) ?>
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                          <?= htmlspecialchars($s['category']) ?>
                        </span>
                      </td>
                      <td class="fw-bold text-success">
                        Rp <?= number_format($s['price_estimate'] ?? $s['base_price'] ?? 0, 0, ',', '.') ?>
                      </td>
                      <td><?= htmlspecialchars($s['estimated_duration_hours'] ?? 2) ?> Jam</td>
                      <td>
                        <?php if (($s['is_active'] ?? 1) == 1): ?>
                          <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                          </span>
                        <?php else: ?>
                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                            <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                          </span>
                        <?php endif; ?>
                      </td>
                      <td class="pe-4 text-end">
                        <div class="btn-group btn-group-sm">
                          <a href="<?= BASE_URL ?>/index.php?url=service/toggle/<?= $s['id'] ?>" class="btn btn-outline-secondary" title="Toggle Aktif/Nonaktif">
                            <i class="bi bi-power"></i>
                          </a>
                          <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditService<?= $s['id'] ?>" title="Edit Jasa">
                            <i class="bi bi-pencil-square"></i>
                          </button>
                          <a href="<?= BASE_URL ?>/index.php?url=service/delete/<?= $s['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus layanan jasa ini?')" title="Hapus Jasa">
                            <i class="bi bi-trash"></i>
                          </a>
                        </div>

                        <!-- Modal Edit Service -->
                        <div class="modal fade text-start" id="modalEditService<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4 border-0">
                              <form action="<?= BASE_URL ?>/index.php?url=service/edit/<?= $s['id'] ?>" method="POST">
                                <div class="modal-header bg-dark text-white">
                                  <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Layanan Jasa</h5>
                                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                  <div class="row g-3">
                                    <div class="col-md-4">
                                      <label class="form-label fw-semibold">Kode Jasa</label>
                                      <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($s['code'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-8">
                                      <label class="form-label fw-semibold">Nama Layanan Jasa</label>
                                      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($s['title'] ?? $s['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label fw-semibold">Kategori</label>
                                      <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($s['category']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label fw-semibold">Icon Bootstrap (misal: bi-wrench)</label>
                                      <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($s['icon'] ?? 'bi-wrench') ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label fw-semibold">Harga Mulai (Rp)</label>
                                      <input type="number" name="base_price" class="form-control" value="<?= htmlspecialchars($s['price_estimate'] ?? $s['base_price'] ?? 0) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label fw-semibold">Estimasi Waktu (Jam)</label>
                                      <input type="number" name="estimated_duration_hours" class="form-control" value="<?= htmlspecialchars($s['estimated_duration_hours'] ?? 2) ?>" required>
                                    </div>
                                    <div class="col-12">
                                      <label class="form-label fw-semibold">Deskripsi Spesifikasi Jasa</label>
                                      <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($s['description']) ?></textarea>
                                    </div>
                                    <div class="col-12">
                                      <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_active_<?= $s['id'] ?>" <?= ($s['is_active'] ?? 1) == 1 ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold" for="edit_active_<?= $s['id'] ?>">Status Aktif Layanan</label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer bg-light">
                                  <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                  <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>

                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data jasa teknis.</td>
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

<!-- Modal Add Service -->
<div class="modal fade" id="modalAddService" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 border-0">
      <form action="<?= BASE_URL ?>/index.php?url=service/create" method="POST">
        <div class="modal-header bg-dark-gradient text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Layanan Jasa Teknis Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Kode Jasa</label>
              <input type="text" name="code" class="form-control" placeholder="SVC-NET-03" required>
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nama Layanan Jasa</label>
              <input type="text" name="name" class="form-control" placeholder="Contoh: Service Printer & Refill Ink" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kategori Jasa</label>
              <input type="text" name="category" class="form-control" placeholder="Maintenance Hardware" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Icon Bootstrap (misal: bi-printer)</label>
              <input type="text" name="icon" class="form-control" placeholder="bi-wrench" value="bi-wrench">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Harga Mulai (Rp)</label>
              <input type="number" name="base_price" class="form-control" placeholder="100000" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Estimasi Waktu (Jam)</label>
              <input type="number" name="estimated_duration_hours" class="form-control" placeholder="2" value="2" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Deskripsi Spesifikasi Jasa</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan cangkupan pengerjaan jasa..." required></textarea>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="add_active" checked>
                <label class="form-check-label fw-semibold" for="add_active">Status Aktif Layanan</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-gradient text-white rounded-pill px-4 fw-bold">Tambah Jasa</button>
        </div>
      </form>
    </div>
  </div>
</div>
