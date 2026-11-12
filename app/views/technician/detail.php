<div class="container py-4 my-auto">
  
  <div class="mb-3">
    <a href="<?= BASE_URL ?>/index.php?url=technician/jobs" class="text-decoration-none text-secondary small">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tugas Saya
    </a>
  </div>

  <?php Flasher::flash(); ?>

  <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-dark-gradient text-white p-4 p-md-5 border-0">
      <div class="row align-items-center gy-3">
        <div class="col-md-8">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-info text-dark px-3 py-2 rounded-pill font-weight-bold">
              <i class="bi bi-tools me-1"></i> Lapangan: Tiket <?= htmlspecialchars($order['order_code']) ?>
            </span>
            <span class="badge bg-warning text-dark px-2 py-1 extra-small fw-bold">
              Status: <?= htmlspecialchars($order['status']) ?>
            </span>
          </div>
          <h2 class="display-6 fw-extrabold mb-1"><?= htmlspecialchars($order['service_name']) ?></h2>
          <p class="text-white opacity-75 mb-0">Lokasi: <strong><?= htmlspecialchars($order['service_address']) ?></strong></p>
        </div>
        <div class="col-md-4 text-md-end">
          <?php if ($order['status'] === 'Teknisi Ditugaskan'): ?>
            <a href="<?= BASE_URL ?>/index.php?url=technician/acceptJob/<?= $order['id'] ?>" class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm">
              <i class="bi bi-check-circle-fill me-1"></i> Konfirmasi Terima Job
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="card-body p-4 p-md-5">
      <div class="row gy-4">
        
        <!-- Left Side: Order & Location Details -->
        <div class="col-lg-7">
          
          <!-- Customer & Contact Info Card -->
          <div class="p-3 bg-light rounded-4 border mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-bounding-box text-primary me-2"></i> Kontak Customer & Lokasi Pekerjaan</h6>
            <div class="row g-3 small">
              <div class="col-md-6">
                <strong>Nama Customer:</strong>
                <div><?= htmlspecialchars($order['customer_name']) ?></div>
              </div>
              <div class="col-md-6">
                <strong>No. Telepon / WhatsApp:</strong>
                <div>
                  <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $order['customer_phone']) ?>" target="_blank" class="text-success fw-bold text-decoration-none">
                    <i class="bi bi-whatsapp me-1"></i> <?= htmlspecialchars($order['customer_phone']) ?> (Hubungi WA)
                  </a>
                </div>
              </div>
              <div class="col-12">
                <strong>Alamat Lengkap Lokasi Pengerjaan:</strong>
                <div class="text-dark font-weight-medium"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($order['service_address']) ?></div>
              </div>
            </div>
          </div>

          <!-- Description & Operator Notes -->
          <h5 class="fw-bold text-dark mb-2">Deskripsi Masalah Customer</h5>
          <p class="text-secondary small mb-4">
            <?= nl2br(htmlspecialchars($order['problem_description'])) ?>
          </p>

          <!-- WORK REPORT BANNER & DETAILS FOR TECHNICIAN -->
          <div class="card border-success border-opacity-50 rounded-4 p-4 mb-4 bg-success bg-opacity-10">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h5 class="fw-bold text-success mb-0"><i class="bi bi-journal-check me-2"></i> Laporan Pekerjaan Lapangan</h5>
              <?php if ($has_report ?? false): ?>
                <span class="badge bg-success text-white"><i class="bi bi-check-all me-1"></i> Laporan Terisi</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i> Laporan Belum Diisi</span>
              <?php endif; ?>
            </div>
            
            <?php if ($has_report ?? false && !empty($report)): ?>
              <p class="small text-muted mb-3">Rincian laporan hasil pengerjaan resmi yang telah disiapkan & disimpan oleh teknisi:</p>
              
              <div class="bg-white p-3 rounded-3 border mb-3 small">
                <div class="mb-2"><strong>1. Diagnosa:</strong> <?= htmlspecialchars($report['diagnosis'] ?? '-') ?></div>
                <div class="mb-2"><strong>2. Pemeriksaan:</strong> <?= htmlspecialchars($report['inspection'] ?? '-') ?></div>
                <div class="mb-2"><strong>3. Tindakan:</strong> <?= htmlspecialchars($report['action_taken'] ?? '-') ?></div>
                <div class="mb-2"><strong>4. Sparepart:</strong> <?= htmlspecialchars($report['spareparts_used'] ?? '-') ?></div>
                <div class="mb-2"><strong>5. Hasil Akhir:</strong> <?= htmlspecialchars($report['execution_result'] ?? '-') ?></div>
                <div class="mb-0"><strong>6. Rekomendasi:</strong> <?= htmlspecialchars($report['recommendation'] ?? ($report['notes'] ?? '-')) ?></div>
              </div>
            <?php else: ?>
              <p class="small text-muted mb-3">
                Isikan rincian diagnosa masalah, pemeriksaan, tindakan perbaikan, sparepart terpakai, serta foto Sebelum & Sesudah pengerjaan. <strong>Wajib diisi sebelum menyelesaikan status order menjadi Selesai.</strong>
              </p>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/index.php?url=technician/report/<?= $order['id'] ?>" class="btn btn-success rounded-pill fw-bold text-white shadow-sm align-self-start">
              <i class="bi bi-pencil-square me-2"></i> <?= ($has_report ?? false) ? 'Edit / Perbarui Laporan (Asisten AI)' : 'Isi Laporan Pekerjaan (Asisten AI)' ?>
            </a>
          </div>

          <!-- Update Progress Form -->
          <?php if ($order['status'] !== 'Selesai' && $order['status'] !== 'Ditutup' && $order['status'] !== 'Dibatalkan'): ?>
            <div class="card border-primary border-opacity-25 rounded-4 p-4 mb-4 bg-primary bg-opacity-10">
              <h5 class="fw-bold text-primary mb-3"><i class="bi bi-arrow-repeat me-2"></i> Form Perbarui Status Pengerjaan</h5>
              
              <form action="<?= BASE_URL ?>/index.php?url=technician/updateStatus/<?= $order['id'] ?>" method="POST">
                <div class="mb-3">
                  <label class="form-label fw-semibold text-dark">Pilih Status Baru Pekerjaan</label>
                  <select name="status" class="form-select rounded-3" required>
                    <option value="Dalam Pengerjaan" <?= $order['status'] === 'Dalam Pengerjaan' ? 'selected' : '' ?>>1. Dalam Pengerjaan (Sedang Di-eksekusi)</option>
                    <option value="Menunggu Customer/Sparepart" <?= $order['status'] === 'Menunggu Customer/Sparepart' ? 'selected' : '' ?>>2. Menunggu Customer / Sparepart</option>
                    <option value="Selesai" <?= $order['status'] === 'Selesai' ? 'selected' : '' ?>>3. Selesai (Wajib Laporan Terisi)</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold text-dark">Catatan Progres Teknisi</label>
                  <textarea name="notes" class="form-control rounded-3" rows="3" placeholder="Tuliskan tindakan perbaikan yang dilakukan atau keluhan lapangan..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary-gradient text-white rounded-pill px-4 fw-bold shadow-sm">
                  <i class="bi bi-save-fill me-1"></i> Simpan Update Progress
                </button>
              </form>
            </div>
          <?php endif; ?>

        </div>

        <!-- Right Side: Work Logs Audit Timeline -->
        <div class="col-lg-5">
          <div class="card border-0 bg-light p-4 rounded-4 mb-4">
            <h5 class="fw-bold text-dark mb-3">
              <i class="bi bi-clock-history me-2 text-warning"></i> Audit Log `work_logs` Trail
            </h5>

            <div class="timeline-logs">
              <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $l): ?>
                  <div class="p-3 bg-white rounded-3 border mb-2 shadow-xs">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="badge bg-dark extra-small text-capitalize"><?= htmlspecialchars($l['log_type']) ?></span>
                      <span class="extra-small text-muted"><?= date('d M H:i', strtotime($l['logged_at'] ?? $l['created_at'])) ?></span>
                    </div>
                    <div class="fw-semibold text-dark extra-small mb-1"><?= htmlspecialchars($l['user_name'] ?? 'Sistem') ?> (<?= ucfirst($l['user_role'] ?? 'User') ?>)</div>
                    <p class="extra-small text-muted mb-0"><?= nl2br(htmlspecialchars($l['description'])) ?></p>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-muted extra-small text-center py-3">Belum ada riwayat catatan log.</div>
              <?php endif; ?>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

</div>
