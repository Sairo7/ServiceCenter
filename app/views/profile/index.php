<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    <!-- Sidebar Area -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Content Area -->
    <div class="col-lg-9 col-xl-10">
      
      <?php Flasher::flash(); ?>

      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-dark-gradient text-white py-4 border-0">
          <div class="d-flex align-items-center gap-3">
            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 64px; height: 64px; font-size: 1.8rem;">
              <?= strtoupper(substr($user['name'] ?? $user['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
              <h3 class="fw-extrabold mb-1"><?= htmlspecialchars($user['name'] ?? $user['full_name'] ?? 'User') ?></h3>
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-uppercase px-3 py-1 rounded-pill">
                  <i class="bi bi-shield-check me-1"></i> Role: <?= str_replace('_', ' ', $user['role']) ?>
                </span>
                <span class="text-white-50 extra-small"><i class="bi bi-circle-fill text-success extra-small me-1"></i> Sesi Aktif</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0">
              <i class="bi bi-person-lines-fill me-2 text-primary"></i> Detail Informasi Akun Pengguna
            </h5>
            <button type="button" class="btn btn-primary-gradient text-white btn-sm px-3 py-2 rounded-pill shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditProfile">
              <i class="bi bi-pencil-square me-1"></i> Edit Profil
            </button>
          </div>

          <div class="row gy-3">
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border">
                <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Nama Lengkap</label>
                <div class="fw-semibold text-dark fs-6"><?= htmlspecialchars($user['name'] ?? $user['full_name'] ?? '-') ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border">
                <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Username</label>
                <div class="fw-semibold text-dark fs-6"><?= htmlspecialchars($user['username'] ?? '-') ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="extra-small text-uppercase text-muted fw-bold d-block mb-0">Alamat Email</label>
                    <span class="badge bg-secondary-subtle text-secondary extra-small rounded-pill">Kontak Utama</span>
                  </div>
                  <div class="fw-semibold text-dark fs-6 text-break"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <label class="extra-small text-uppercase text-muted fw-bold d-block mb-0">Nomor Telepon / WhatsApp</label>
                    <?php if (!empty($_SESSION['otp_verified'])): ?>
                      <span class="badge bg-success-subtle text-success border border-success-subtle extra-small rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi</span>
                    <?php else: ?>
                      <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small rounded-pill"><i class="bi bi-exclamation-circle-fill me-1"></i> Belum Terverifikasi</span>
                    <?php endif; ?>
                  </div>
                  <div class="fw-semibold text-dark fs-6"><?= htmlspecialchars($user['phone'] ?? '-') ?></div>
                </div>
              </div>
            </div>
          </div>

          <h5 class="fw-bold text-dark mt-5 mb-4 pb-2 border-bottom">
            <i class="bi bi-shield-lock-fill me-2 text-warning"></i> Keamanan Sesi & Verifikasi OTP
          </h5>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 rounded-3 h-100 mb-0">
                <div class="fs-2 text-info"><i class="bi bi-check-shield-fill"></i></div>
                <div>
                  <div class="fw-bold text-dark">Password Terenkripsi Aman</div>
                  <div class="extra-small text-muted">Password Anda terlindungi dengan enkripsi Bcrypt hashing standar industri.</div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-bold text-dark small"><i class="bi bi-patch-check-fill text-primary me-1"></i> Status Verifikasi OTP</span>
                    <?php if (!empty($_SESSION['otp_verified'])): ?>
                      <span class="badge bg-success rounded-pill"><i class="bi bi-check2-circle me-1"></i> Terverifikasi</span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i> Belum Diverifikasi</span>
                    <?php endif; ?>
                  </div>
                  <p class="extra-small text-muted mb-2">Verifikasi nomor WhatsApp/HP Anda dengan One-Time Password untuk keamanan akun.</p>
                </div>
                <div>
                  <a href="<?= BASE_URL ?>/index.php?url=otp" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> <?= !empty($_SESSION['otp_verified']) ? 'Uji Ulang Verifikasi OTP' : 'Verifikasi Nomor Sekarang' ?>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/index.php?url=dashboard" class="btn btn-primary-gradient px-4 py-2 rounded-pill text-white fw-bold shadow-sm">
              <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard
            </a>
            <a href="<?= BASE_URL ?>/index.php?url=auth/logout" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-medium">
              <i class="bi bi-box-arrow-right me-1"></i> Logout Akun
            </a>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal Edit Profil Pengguna -->
<div class="modal fade" id="modalEditProfile" tabindex="-1" aria-labelledby="modalEditProfileLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
      <div class="modal-header bg-dark-gradient text-white border-0 py-3">
        <h5 class="modal-title fw-bold" id="modalEditProfileLabel">
          <i class="bi bi-person-gear me-2 text-primary"></i> Edit Profil Pengguna
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= BASE_URL ?>/index.php?url=profile/update" method="POST" id="editProfileForm" novalidate>
        <div class="modal-body p-4 bg-white">
          <div class="alert alert-info border-0 rounded-3 small py-2 px-3 mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div>Perubahan nama dan kontak akan disimpan langsung ke database akun Anda.</div>
          </div>

          <!-- Nama Lengkap / Nama Pengguna (Editable) -->
          <div class="mb-3">
            <label for="profileFullName" class="form-label small fw-bold text-dark text-uppercase mb-1">
              Nama Lengkap <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control" id="profileFullName" name="full_name" value="<?= htmlspecialchars($user['name'] ?? $user['full_name'] ?? '') ?>" required placeholder="Masukkan nama pengguna / lengkap...">
            </div>
            <div class="invalid-feedback" id="nameErrorMsg">Nama pengguna / lengkap wajib diisi.</div>
          </div>

          <!-- Username / ID Pengguna (Editable) -->
          <div class="mb-3">
            <label for="profileUsername" class="form-label small fw-bold text-dark text-uppercase mb-1">
              Username <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="bi bi-at"></i></span>
              <input type="text" class="form-control" id="profileUsername" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required placeholder="contoh: budi_teknisi" pattern="^[a-zA-Z0-9_.-]{3,30}$">
            </div>
            <div class="invalid-feedback" id="usernameErrorMsg">Username minimal 3 karakter (huruf, angka, titik, atau underscore).</div>
          </div>

          <!-- Alamat Email -->
          <div class="mb-3">
            <label for="profileEmail" class="form-label small fw-bold text-dark text-uppercase mb-1">
              Alamat Email <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope-at"></i></span>
              <input type="email" class="form-control" id="profileEmail" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required placeholder="nama@domain.com">
            </div>
            <div class="invalid-feedback" id="emailErrorMsg">Masukkan format alamat email yang valid.</div>
          </div>

          <!-- Nomor Telepon / WhatsApp -->
          <div class="mb-3">
            <label for="profilePhone" class="form-label small fw-bold text-dark text-uppercase mb-1">
              Nomor WhatsApp / Telepon <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="bi bi-whatsapp"></i></span>
              <input type="tel" class="form-control" id="profilePhone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required placeholder="Contoh: 081234567890" pattern="^(\+62|62|0)8[1-9][0-9]{6,12}$">
            </div>
            <div class="invalid-feedback" id="phoneErrorMsg">Nomor harus diawali 08, 628, atau +628 (min. 10 digit).</div>
            <div class="form-text extra-small text-muted">Pastikan nomor aktif dan terhubung ke WhatsApp untuk menerima kode verifikasi OTP Fonnte.</div>
          </div>
        </div>

        <div class="modal-footer bg-light border-0 py-3 px-4">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnSubmitProfile" class="btn btn-primary-gradient text-white fw-bold rounded-pill px-4 shadow-sm">
            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('editProfileForm');
  const nameInput = document.getElementById('profileFullName');
  const usernameInput = document.getElementById('profileUsername');
  const emailInput = document.getElementById('profileEmail');
  const phoneInput = document.getElementById('profilePhone');

  form.addEventListener('submit', function (event) {
    let isValid = true;

    // Full Name validation
    const nameVal = nameInput ? nameInput.value.trim() : '';
    if (!nameVal) {
      if (nameInput) nameInput.classList.add('is-invalid');
      isValid = false;
    } else {
      if (nameInput) {
        nameInput.classList.remove('is-invalid');
        nameInput.classList.add('is-valid');
      }
    }

    // Username validation
    const usernameVal = usernameInput ? usernameInput.value.trim() : '';
    const usernameRegex = /^[a-zA-Z0-9_.-]{3,30}$/;
    if (!usernameRegex.test(usernameVal)) {
      if (usernameInput) usernameInput.classList.add('is-invalid');
      isValid = false;
    } else {
      if (usernameInput) {
        usernameInput.classList.remove('is-invalid');
        usernameInput.classList.add('is-valid');
      }
    }

    // Email validation
    const emailVal = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailVal)) {
      emailInput.classList.add('is-invalid');
      isValid = false;
    } else {
      emailInput.classList.remove('is-invalid');
      emailInput.classList.add('is-valid');
    }

    // Phone validation
    const phoneVal = phoneInput.value.trim();
    const phoneClean = phoneVal.replace(/[^0-9]/g, '');
    const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,12}$/;
    if (!phoneRegex.test(phoneVal) && !(phoneClean.length >= 10 && phoneClean.length <= 15 && (phoneClean.startsWith('08') || phoneClean.startsWith('628')))) {
      phoneInput.classList.add('is-invalid');
      isValid = false;
    } else {
      phoneInput.classList.remove('is-invalid');
      phoneInput.classList.add('is-valid');
    }

    if (!isValid) {
      event.preventDefault();
      event.stopPropagation();
    }
  });

  if (nameInput) {
    nameInput.addEventListener('input', function() {
      this.classList.remove('is-invalid');
    });
  }

  if (usernameInput) {
    usernameInput.addEventListener('input', function() {
      this.classList.remove('is-invalid');
    });
  }

  emailInput.addEventListener('input', function() {
    this.classList.remove('is-invalid');
  });

  phoneInput.addEventListener('input', function() {
    this.classList.remove('is-invalid');
  });
});
</script>
