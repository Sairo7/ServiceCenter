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
            <span class="badge bg-secondary text-white px-3 py-2 rounded-pill fw-bold text-uppercase mb-2">
              <i class="bi bi-cpu-fill me-1"></i> Operator Inventaris IT
            </span>
            <h2 class="fw-extrabold mb-1">Manajemen Suku Cadang & Sparepart</h2>
            <p class="text-light opacity-75 mb-0 small">Kelola stok hardware, kabel LAN, konektor, kamera CCTV, dan komponen perbaikan.</p>
          </div>
          <div>
            <button type="button" class="btn btn-primary-gradient text-white btn-lg rounded-pill shadow-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddSparepart">
              <i class="bi bi-plus-circle-fill me-2"></i> Tambah Sparepart Baru
            </button>
          </div>
        </div>
      </div>

      <!-- Search & Filter Bar -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <form action="<?= BASE_URL ?>/index.php" method="GET" class="row g-2 align-items-center">
          <input type="hidden" name="url" value="sparepart/manage">

          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" name="q" class="form-control bg-light border-start-0 ps-0" placeholder="Cari kode atau nama sparepart..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
          </div>

          <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 rounded-3">Cari Sparepart</button>
            <?php if (!empty($search)): ?>
              <a href="<?= BASE_URL ?>/index.php?url=sparepart/manage" class="btn btn-outline-secondary rounded-3">Reset</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- Data Table -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-boxes me-2 text-primary"></i> Master Inventaris Sparepart (<?= count($spareparts ?? []) ?> Item)
          </h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th class="ps-4">Kode Part</th>
                  <th>Nama Sparepart</th>
                  <th>Kategori</th>
                  <th>Stok & Satuan</th>
                  <th>Harga Jual (Rp)</th>
                  <th>Status Stok</th>
                  <th class="pe-4 text-end">Aksi Operator</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php if (!empty($spareparts)): ?>
                  <?php foreach ($spareparts as $sp): 
                    $isLowStock = ($sp['stock_qty'] <= ($sp['min_stock'] ?? 5));
                  ?>
                    <tr>
                      <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($sp['part_code']) ?></td>
                      <td class="fw-bold text-dark"><?= htmlspecialchars($sp['name']) ?></td>
                      <td>
                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                          <?= htmlspecialchars($sp['category']) ?>
                        </span>
                      </td>
                      <td>
                        <strong class="fs-6 <?= $isLowStock ? 'text-danger' : 'text-dark' ?>"><?= htmlspecialchars($sp['stock_qty']) ?></strong> <?= htmlspecialchars($sp['unit'] ?? 'pcs') ?>
                      </td>
                      <td class="fw-bold text-success">
                        Rp <?= number_format($sp['selling_price'], 0, ',', '.') ?>
                      </td>
                      <td>
                        <?php if ($isLowStock): ?>
                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Menipis (<= <?= $sp['min_stock'] ?>)
                          </span>
                        <?php else: ?>
                          <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            <i class="bi bi-check-circle-fill me-1"></i> Tersedia
                          </span>
                        <?php endif; ?>
                      </td>
                      <td class="pe-4 text-end">
                        <div class="btn-group btn-group-sm">
                          <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditSparepart<?= $sp['id'] ?>" title="Edit Sparepart">
                            <i class="bi bi-pencil-square"></i> Edit
                          </button>
                          <a href="<?= BASE_URL ?>/index.php?url=sparepart/delete/<?= $sp['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus sparepart ini?')" title="Hapus Sparepart">
                            <i class="bi bi-trash"></i>
                          </a>
                        </div>

                        <!-- Modal Edit Sparepart -->
                        <div class="modal fade text-start" id="modalEditSparepart<?= $sp['id'] ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4 border-0">
                              <form action="<?= BASE_URL ?>/index.php?url=sparepart/edit/<?= $sp['id'] ?>" method="POST">
                                <div class="modal-header bg-dark text-white">
                                  <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Data Sparepart</h5>
                                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                  <div class="row g-3">
                                    <div class="col-md-4">
                                      <label class="form-label fw-semibold">Kode Part</label>
                                      <input type="text" name="part_code" class="form-control" value="<?= htmlspecialchars($sp['part_code']) ?>" required>
                                    </div>
                                    <div class="col-md-8">
                                      <label class="form-label fw-semibold">Nama Sparepart</label>
                                      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($sp['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label fw-semibold">Kategori</label>
                                      <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($sp['category']) ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                      <label class="form-label fw-semibold">Jumlah Stok</label>
                                      <input type="number" name="stock_qty" class="form-control" value="<?= htmlspecialchars($sp['stock_qty']) ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                      <label class="form-label fw-semibold">Satuan (pcs/meter)</label>
                                      <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($sp['unit'] ?? 'pcs') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label fw-semibold">Harga Modal (Rp)</label>
                                      <input type="number" name="unit_cost" class="form-control" value="<?= htmlspecialchars($sp['unit_cost'] ?? 0) ?>">
                                    </div>
                                    <div class="col-md-6">
                                      <label class="form-label fw-semibold">Harga Jual / Tagihan (Rp) <span class="text-danger">*</span></label>
                                      <input type="number" name="selling_price" class="form-control" value="<?= htmlspecialchars($sp['selling_price']) ?>" required>
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
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data sparepart.</td>
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

<!-- Modal Add Sparepart -->
<div class="modal fade" id="modalAddSparepart" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 border-0">
      <form action="<?= BASE_URL ?>/index.php?url=sparepart/create" method="POST">
        <div class="modal-header bg-dark-gradient text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Sparepart / Suku Cadang Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Kode Part</label>
              <input type="text" name="part_code" class="form-control" placeholder="PRT-SSD-512G" required>
            </div>
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nama Sparepart</label>
              <input type="text" name="name" class="form-control" placeholder="Contoh: SSD NVMe 512GB High Speed" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kategori</label>
              <input type="text" name="category" class="form-control" placeholder="Storage SSD / Kabel & Connector" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Stok Awal</label>
              <input type="number" name="stock_qty" class="form-control" placeholder="10" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Satuan (pcs/meter)</label>
              <input type="text" name="unit" class="form-control" placeholder="pcs" value="pcs" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Harga Modal (Rp)</label>
              <input type="number" name="unit_cost" class="form-control" placeholder="400000">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Harga Jual / Tagihan (Rp) <span class="text-danger">*</span></label>
              <input type="number" name="selling_price" class="form-control" placeholder="550000" required>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-gradient text-white rounded-pill px-4 fw-bold">Tambah Sparepart</button>
        </div>
      </form>
    </div>
  </div>
</div>
