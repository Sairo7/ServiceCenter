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
              <i class="bi bi-gear-fill me-1"></i> Operator Panel
            </span>
            <h2 class="fw-extrabold mb-1">Konfigurasi Sistem</h2>
            <p class="text-light opacity-75 mb-0 small">Kelola identitas layanan, integrasi gateway notifikasi WhatsApp, dan parameter aplikasi secara dinamis.</p>
          </div>
          <div>
            <a href="<?= BASE_URL ?>/index.php?url=dashboard/operator" class="btn btn-outline-light rounded-pill px-4 py-2">
              <i class="bi bi-speedometer2 me-1"></i> Dashboard Operator
            </a>
          </div>
        </div>
      </div>

      <!-- Form Card -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-sliders me-2 text-primary"></i> Pengaturan Global & Integrasi Gateway
          </h5>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill extra-small">
            Tersimpan di Database
          </span>
        </div>

        <div class="card-body p-4 p-md-5">
          <form action="<?= BASE_URL ?>/index.php?url=config/update" method="POST" id="formConfig">
            <div class="row g-4">
              
              <!-- 1. Nama Layanan -->
              <div class="col-md-6">
                <label for="service_name" class="form-label fw-bold text-dark small text-uppercase">
                  Nama Layanan <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="bi bi-building"></i>
                  </span>
                  <input type="text" class="form-control bg-light border-start-0" id="service_name" name="service_name" 
                         value="<?= htmlspecialchars($configs['service_name'] ?? 'TKJ Service Center') ?>" 
                         placeholder="Contoh: TKJ Service Center" required>
                </div>
                <div class="form-text extra-small text-muted">
                  Nama identitas unit layanan yang ditampilkan pada header pesan notifikasi dan dokumen.
                </div>
              </div>

              <!-- 2. Gateway Provider -->
              <div class="col-md-6">
                <label for="gateway_provider" class="form-label fw-bold text-dark small text-uppercase">
                  Gateway Provider <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="bi bi-broadcast"></i>
                  </span>
                  <select class="form-select bg-light border-start-0" id="gateway_provider" name="gateway_provider" required>
                    <?php 
                      $currProvider = $configs['gateway_provider'] ?? 'Fonnte';
                    ?>
                    <option value="Fonnte" <?= (strcasecmp($currProvider, 'Fonnte') === 0) ? 'selected' : '' ?>>Fonnte (Rekomendasi Resmi)</option>
                    <option value="Wablas" <?= (strcasecmp($currProvider, 'Wablas') === 0) ? 'selected' : '' ?>>Wablas</option>
                    <option value="Twilio" <?= (strcasecmp($currProvider, 'Twilio') === 0) ? 'selected' : '' ?>>Twilio</option>
                  </select>
                </div>
                <div class="form-text extra-small text-muted">
                  Penyedia API gateway untuk pengiriman pesan WhatsApp dan verifikasi OTP.
                </div>
              </div>

              <!-- 3. API Token Fonnte -->
              <div class="col-md-8">
                <label for="api_token" class="form-label fw-bold text-dark small text-uppercase">
                  API Token Gateway <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="bi bi-key-fill"></i>
                  </span>
                  <input type="password" class="form-control bg-light border-start-0 border-end-0 font-monospace" id="api_token" name="api_token" 
                         value="<?= htmlspecialchars($configs['api_token'] ?? 'jmsDKErQCKjFWhycUYqe') ?>" 
                         placeholder="Masukkan API Token Gateway" required>
                  <button class="btn btn-light border border-start-0 text-muted" type="button" id="btnToggleToken">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                  </button>
                </div>
                <div class="form-text extra-small text-muted">
                  Kunci otorisasi rahasia untuk autentikasi ke server Fonnte (tersimpan terenkripsi/aman di database).
                </div>
              </div>

              <!-- 4. Kode Negara Default -->
              <div class="col-md-4">
                <label for="default_country_code" class="form-label fw-bold text-dark small text-uppercase">
                  Kode Negara Default <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="bi bi-telephone-plus"></i>
                  </span>
                  <input type="number" class="form-control bg-light border-start-0" id="default_country_code" name="default_country_code" 
                         value="<?= htmlspecialchars($configs['default_country_code'] ?? '62') ?>" 
                         placeholder="62" required>
                </div>
                <div class="form-text extra-small text-muted">
                  Prefiks nomor internasional untuk nomor lokal (contoh: 62 untuk Indonesia).
                </div>
              </div>

            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
              <a href="<?= BASE_URL ?>/index.php?url=dashboard/operator" class="btn btn-outline-secondary rounded-pill px-4">
                Batal
              </a>
              <button type="submit" class="btn btn-primary-gradient text-white rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Simpan Konfigurasi
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tokenInput = document.getElementById('api_token');
  const btnToggle = document.getElementById('btnToggleToken');
  const toggleIcon = document.getElementById('toggleIcon');

  if (btnToggle && tokenInput) {
    btnToggle.addEventListener('click', function () {
      if (tokenInput.type === 'password') {
        tokenInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
      } else {
        tokenInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
      }
    });
  }
});
</script>

