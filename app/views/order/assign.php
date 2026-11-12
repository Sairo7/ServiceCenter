<div class="container py-4 my-auto">
  
  <div class="mb-3">
    <a href="<?= BASE_URL ?>/index.php?url=order/manage" class="text-decoration-none text-secondary small">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Panel Pengelolaan Order
    </a>
  </div>

  <?php Flasher::flash(); ?>

  <div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
      
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-dark-gradient text-white p-4 border-0">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="badge bg-warning text-dark px-3 py-1 rounded-pill extra-small fw-bold text-uppercase mb-2">
                <i class="bi bi-person-badge me-1"></i> Operator Assign Work
              </span>
              <h4 class="fw-extrabold mb-0">Form Penugasan & Penjadwalan Teknisi</h4>
            </div>
            <div class="badge bg-primary fs-6 px-3 py-2 rounded-pill">
              Tiket: <?= htmlspecialchars($order['order_code']) ?>
            </div>
          </div>
        </div>

        <div class="card-body p-4 p-md-5">

          <!-- Order Summary Card -->
          <div class="p-3 bg-light rounded-4 border mb-4">
            <div class="row g-2 text-dark small">
              <div class="col-md-6">
                <strong>Jenis Layanan Jasa:</strong> <?= htmlspecialchars($order['service_name']) ?>
              </div>
              <div class="col-md-6">
                <strong>Pemesan:</strong> <?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)
              </div>
              <div class="col-md-12">
                <strong>Alamat Lokasi:</strong> <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($order['service_address']) ?>
              </div>
            </div>
          </div>

          <!-- Schedule Conflict Protection Alert Banner -->
          <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 rounded-3 mb-4">
            <div class="fs-2 text-warning"><i class="bi bi-shield-exclamation"></i></div>
            <div class="extra-small text-dark">
              <strong>Deteksi Konflik Jadwal Otomatis:</strong> Sistem akan memeriksa apakah teknisi yang Anda pilih sudah memiliki agenda tugas lain pada tanggal & rentang jam kerja yang sama. Jika terjadi tabrakan jadwal, penugasan akan diblokir untuk menjaga kualitas kerja teknisi.
            </div>
          </div>

          <form action="<?= BASE_URL ?>/index.php?url=order/processAssign/<?= $order['id'] ?>" method="POST">

            <!-- 1. Pilih Teknisi Siswa -->
            <div class="mb-4">
              <label for="technician_id" class="form-label fw-bold text-dark">
                <i class="bi bi-person-gear text-primary me-1"></i> Pilih Teknisi Siswa TKJ <span class="text-danger">*</span>
              </label>
              <select name="technician_id" id="technician_id" class="form-select form-select-lg fs-6 rounded-3" required>
                <option value="">-- Pilih Teknisi Siswa Berdasarkan Spesialisasi --</option>
                <?php if (!empty($technicians)): ?>
                  <?php foreach ($technicians as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($order['technician_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($t['name']) ?> (NIS: <?= htmlspecialchars($t['nis_nip'] ?? '-') ?> | Spec: <?= htmlspecialchars($t['specialization'] ?? 'TKJ') ?>) - Status: <?= strtoupper($t['status'] ?? 'available') ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

            <!-- 2. Tanggal Kerja & Rentang Jam -->
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label for="work_date" class="form-label fw-semibold text-dark">
                  <i class="bi bi-calendar-check-fill text-warning me-1"></i> Tanggal Pengerjaan <span class="text-danger">*</span>
                </label>
                <input type="date" name="work_date" id="work_date" class="form-control rounded-3" value="<?= htmlspecialchars($order['scheduled_date'] ?? date('Y-m-d')) ?>" required>
              </div>
              <div class="col-md-3">
                <label for="start_time" class="form-label fw-semibold text-dark">Jam Mulai <span class="text-danger">*</span></label>
                <input type="time" name="start_time" id="start_time" class="form-control rounded-3" value="09:00" required>
              </div>
              <div class="col-md-3">
                <label for="end_time" class="form-label fw-semibold text-dark">Jam Selesai <span class="text-danger">*</span></label>
                <input type="time" name="end_time" id="end_time" class="form-control rounded-3" value="12:00" required>
              </div>
            </div>

            <!-- 3. Catatan / Instruksi Operator -->
            <div class="mb-4">
              <label for="notes" class="form-label fw-semibold text-dark">
                Catatan & Instruksi Khusus Operator Untuk Teknisi
              </label>
              <textarea name="notes" id="notes" class="form-control rounded-3" rows="3" placeholder="Contoh: Bawa bor listrik, kabel UTP Cat6 30 meter, crimping tool, & BNC connector..."></textarea>
            </div>

            <div class="d-flex gap-2">
              <a href="<?= BASE_URL ?>/index.php?url=order/manage" class="btn btn-outline-secondary rounded-3 px-4">
                Batal
              </a>
              <button type="submit" class="btn btn-primary-gradient btn-lg text-white rounded-3 fw-bold shadow-sm flex-grow-1">
                <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi & Tugaskan Teknisi
              </button>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>
