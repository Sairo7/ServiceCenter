<div class="container py-5 my-auto">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
      
      <!-- Flash Alert Message -->
      <?php Flasher::flash(); ?>

      <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-header bg-dark-gradient text-white text-center py-4 border-0">
          <div class="brand-icon mx-auto mb-2 d-flex align-items-center justify-content-center text-white rounded-3 shadow-sm" style="width: 52px; height: 52px; font-size: 1.5rem; background: linear-gradient(135deg, #2563eb, #06b6d4);">
            <i class="bi bi-shield-lock-fill"></i>
          </div>
          <h4 class="fw-bold mb-1 text-white">Masuk ke Sistem</h4>
          <p class="small mb-0" style="color: #94a3b8;">TKJ Service Business Web App</p>
        </div>

        <div class="card-body p-4 p-md-5 bg-white">
          <form action="<?= BASE_URL ?>/index.php?url=auth/processLogin" method="POST">
            <div class="mb-3">
              <label for="username" class="form-label fw-semibold text-dark">
                <i class="bi bi-person me-1 text-primary"></i> Username atau Email
              </label>
              <input type="text" name="username" id="username" class="form-control form-control-lg fs-6 rounded-3" placeholder="Masukkan username..." required autofocus>
            </div>

            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-semibold text-dark mb-0">
                  <i class="bi bi-key me-1 text-primary"></i> Password
                </label>
              </div>
              <input type="password" name="password" id="password" class="form-control form-control-lg fs-6 rounded-3" placeholder="Masukkan password..." required>
            </div>

            <!-- OTP 2FA Option Checkbox -->
            <div class="form-check form-switch mb-4">
              <input class="form-check-input" type="checkbox" role="switch" id="require_otp" name="require_otp" value="1">
              <label class="form-check-label small fw-semibold text-secondary" for="require_otp">
                <i class="bi bi-shield-lock text-primary me-1"></i> Aktifkan Verifikasi OTP (2FA)
              </label>
            </div>

            <button type="submit" class="btn btn-primary-gradient btn-lg w-100 rounded-3 fw-bold text-white shadow-sm mb-4">
              <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sistem
            </button>
          </form>

          <!-- Demo Quick Credentials Picker -->
          <div class="p-3 bg-light rounded-4 border border-light-subtle">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold extra-small text-uppercase text-secondary">
                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Demo Login
              </span>
              <span class="extra-small text-muted">Klik role untuk auto-fill</span>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start py-2 px-2 extra-small rounded-3" onclick="fillDemo('customer')" title="customer / customer123">
                  <i class="bi bi-person-fill me-1"></i> <strong>Customer</strong>
                  <div class="text-muted extra-small" style="font-size: 0.7rem;">Pass: customer123</div>
                </button>
              </div>
              <div class="col-6">
                <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start py-2 px-2 extra-small rounded-3" onclick="fillDemo('operator')" title="operator / operator456">
                  <i class="bi bi-headset me-1"></i> <strong>Operator</strong>
                  <div class="text-muted extra-small" style="font-size: 0.7rem;">Pass: operator456</div>
                </button>
              </div>
              <div class="col-6">
                <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start py-2 px-2 extra-small rounded-3" onclick="fillDemo('teknisi')" title="teknisi / teknisi789">
                  <i class="bi bi-wrench me-1"></i> <strong>Teknisi</strong>
                  <div class="text-muted extra-small" style="font-size: 0.7rem;">Pass: teknisi789</div>
                </button>
              </div>
              <div class="col-6">
                <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start py-2 px-2 extra-small rounded-3" onclick="fillDemo('kepala_sekolah')" title="kepala_sekolah / kepalasekolah101">
                  <i class="bi bi-award-fill me-1"></i> <strong>Kepala Sekolah</strong>
                  <div class="text-muted extra-small" style="font-size: 0.7rem;">Pass: kepalasekolah101</div>
                </button>
              </div>
            </div>
          </div>

        </div>
        <div class="card-footer bg-light text-center py-3 border-top-0">
          <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
function fillDemo(role) {
  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  const demoAccounts = {
    'customer': { user: 'customer', pass: 'customer123' },
    'operator': { user: 'operator', pass: 'operator456' },
    'teknisi': { user: 'teknisi', pass: 'teknisi789' },
    'kepala_sekolah': { user: 'kepala_sekolah', pass: 'kepalasekolah101' },
    'kepalasekolah': { user: 'kepala_sekolah', pass: 'kepalasekolah101' }
  };
  
  if (demoAccounts[role]) {
    usernameInput.value = demoAccounts[role].user;
    passwordInput.value = demoAccounts[role].pass;
  }
}
</script>
