<div class="container py-4 my-auto">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
      
      <div class="mb-3">
        <a href="<?= BASE_URL ?>/index.php?url=service" class="text-decoration-none text-secondary small">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog Layanan Jasa
        </a>
      </div>

      <?php Flasher::flash(); ?>

      <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-dark-gradient text-white p-4 border-0">
          <div class="d-flex align-items-center gap-3">
            <div class="brand-icon bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
              <i class="bi bi-file-earmark-plus-fill"></i>
            </div>
            <div>
              <h4 class="fw-bold mb-1">Formulir Pengajuan Layanan Jasa Teknis</h4>
              <p class="text-white-50 small mb-0">Isi rincian kebutuhan perbaikan atau instalasi perangkat IT Anda</p>
            </div>
          </div>
        </div>

        <div class="card-body p-4 p-md-5">
          <form action="<?= BASE_URL ?>/index.php?url=order/store" method="POST" enctype="multipart/form-data">
            
            <!-- 1. Pilihan Jenis Jasa -->
            <div class="mb-4">
              <label for="service_id" class="form-label fw-bold text-dark">
                <i class="bi bi-tools text-primary me-1"></i> Jenis Layanan Jasa <span class="text-danger">*</span>
              </label>
              <select name="service_id" id="service_id" class="form-select form-select-lg fs-6 rounded-3" required>
                <option value="">-- Pilih Jenis Layanan Jasa Teknis TKJ --</option>
                <?php if (!empty($services)): ?>
                  <?php foreach ($services as $svc): ?>
                    <option value="<?= $svc['id'] ?>" <?= ($selected_service['id'] ?? '') == $svc['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($svc['title'] ?? $svc['name']) ?> (Est. Rp <?= number_format($svc['price_estimate'] ?? $svc['base_price'] ?? 0, 0, ',', '.') ?>)
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

            <!-- 2. Judul / Subjek -->
            <div class="mb-3">
              <label for="subject" class="form-label fw-semibold text-dark">
                Judul / Subjek Pengajuan <span class="text-danger">*</span>
              </label>
              <input type="text" name="subject" id="subject" class="form-control form-control-lg fs-6 rounded-3" placeholder="Contoh: Pasang 4 Kamera CCTV di Garasi & Toko" required>
            </div>

            <!-- 3. Deskripsi Masalah / Kebutuhan -->
            <div class="mb-4">
              <label for="problem_description" class="form-label fw-semibold text-dark">
                Deskripsi Masalah / Rincian Kebutuhan <span class="text-danger">*</span>
              </label>
              <textarea name="problem_description" id="problem_description" class="form-control rounded-3" rows="4" placeholder="Jelaskan kondisi perangkat, gejala kerusakan, atau kebutuhan spesifikasi penarikan kabel/jaringan..." required></textarea>
            </div>

            <div class="row g-3 mb-4">
              <!-- 4. Lokasi Pekerjaan -->
              <div class="col-md-7">
                <label for="service_address" class="form-label fw-semibold text-dark">
                  <i class="bi bi-geo-alt-fill text-danger me-1"></i> Lokasi Pekerjaan / Alamat Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text" name="service_address" id="service_address" class="form-control rounded-3" placeholder="Jl. Merdeka No. 12, Ruko B3..." value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
              </div>

              <!-- 5. Nomor Telepon -->
              <div class="col-md-5">
                <label for="phone" class="form-label fw-semibold text-dark">
                  <i class="bi bi-whatsapp text-success me-1"></i> Nomor Telepon / WhatsApp <span class="text-danger">*</span>
                </label>
                <input type="text" name="phone" id="phone" class="form-control rounded-3" placeholder="081234567890" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <!-- 6. Tanggal / Jadwal Diinginkan -->
              <div class="col-md-6">
                <label for="scheduled_date" class="form-label fw-semibold text-dark">
                  <i class="bi bi-calendar-event-fill text-warning me-1"></i> Tanggal / Jadwal yang Diinginkan <span class="text-danger">*</span>
                </label>
                <input type="date" name="scheduled_date" id="scheduled_date" class="form-control rounded-3" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
              </div>

              <!-- Priority selection -->
              <div class="col-md-6">
                <label for="priority" class="form-label fw-semibold text-dark">Prioritas Penanganan</label>
                <select name="priority" id="priority" class="form-select rounded-3">
                  <option value="low">Rendah (Santai / Maintenance)</option>
                  <option value="medium" selected>Sedang (Standar Pengerjaan)</option>
                  <option value="high">Tinggi (Mendesak / Butuh Cepat)</option>
                </select>
              </div>
            </div>

            <!-- 7. Upload Foto / Dokumentasi -->
            <div class="mb-4 p-3 bg-light rounded-3 border">
              <label for="photo_evidence" class="form-label fw-semibold text-dark mb-1">
                <i class="bi bi-camera-fill text-info me-1"></i> Upload Foto / Dokumentasi Perangkat (Opsional)
              </label>
              <input type="file" name="photo_evidence" id="photo_evidence" class="form-control mb-1" accept="image/jpeg,image/png,image/webp">
              <div class="extra-small text-muted">
                Dapat berupa foto bentuk fisik komputer mati total, kondisi lokasi CCTV, atau denah jaringan. Maksimal ukuran file 5MB (Format: JPG, PNG, WEBP).
              </div>
            </div>

            <!-- System Auto Banner Note -->
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 rounded-3 mb-4">
              <div class="fs-2 text-info"><i class="bi bi-ticket-perforated-fill"></i></div>
              <div class="extra-small text-dark">
                <strong>Sistem Otomatis:</strong> Saat Anda menekan tombol di bawah, sistem akan langsung menerbitkan <strong>Nomor Tiket Order Unik</strong> dengan status awal <strong>"Diajukan"</strong> yang dapat Anda pantau progresnya secara realtime.
              </div>
            </div>

            <button type="submit" class="btn btn-primary-gradient btn-lg w-100 rounded-3 text-white fw-bold shadow-sm py-3">
              <i class="bi bi-send-fill me-2"></i> Kirim Pengajuan Layanan Jasa
            </button>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>
