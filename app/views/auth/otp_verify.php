<div class="container py-5 my-auto">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">

      <!-- Flash Alert Message -->
      <?php Flasher::flash(); ?>

      <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <!-- Header -->
        <div class="card-header bg-dark-gradient text-white text-center py-4 border-0 position-relative">
          <div class="brand-icon mx-auto mb-2 d-flex align-items-center justify-content-center text-white rounded-3 shadow-sm" 
               style="width: 58px; height: 58px; font-size: 1.7rem; background: linear-gradient(135deg, #10b981, #06b6d4);">
            <i class="bi bi-shield-check"></i>
          </div>
          <h4 class="fw-bold mb-1 text-white">Verifikasi OTP</h4>
          <p class="small mb-0 text-white-50">One-Time Password Verification</p>
        </div>

        <!-- Body -->
        <div class="card-body p-4 p-md-5 bg-white">
          <div class="text-center mb-4">
            <p class="text-muted small mb-1">
              Kode verifikasi telah dikirimkan ke tujuan:
            </p>
            <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6 rounded-pill">
              <i class="bi bi-phone-vibrate me-1"></i>
              <span id="maskedIdentifierDisplay"><?= htmlspecialchars($data['masked_identifier'] ?: 'Nomor Anda') ?></span>
            </div>
            <p class="extra-small text-muted mt-2 mb-0">
              Masa berlaku kode: <strong><?= OTP_EXPIRY_MINUTES ?> Menit</strong> &bull; Maksimal percobaan: <strong><?= OTP_MAX_ATTEMPTS ?>x</strong>
            </p>
          </div>

          <!-- Alert Feedback Container (AJAX feedback) -->
          <div id="otpAlertBox" class="alert d-none rounded-3 py-2 px-3 small mb-3"></div>

          <!-- OTP Input Form -->
          <form id="otpForm" action="<?= BASE_URL ?>/index.php?url=otp/verify" method="POST">
            <input type="hidden" name="identifier" id="formIdentifier" value="<?= htmlspecialchars($data['identifier']) ?>">

            <!-- 6 Box Digit OTP Inputs -->
            <label class="form-label fw-semibold text-dark text-center d-block mb-3">
              Masukkan 6 Digit Kode Verifikasi
            </label>
            <div class="d-flex justify-content-between gap-2 mb-4">
              <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-4 fw-bold otp-digit rounded-3" id="digit-1" name="otp_1" autofocus autocomplete="off" inputmode="numeric">
              <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-4 fw-bold otp-digit rounded-3" id="digit-2" name="otp_2" autocomplete="off" inputmode="numeric">
              <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-4 fw-bold otp-digit rounded-3" id="digit-3" name="otp_3" autocomplete="off" inputmode="numeric">
              <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-4 fw-bold otp-digit rounded-3" id="digit-4" name="otp_4" autocomplete="off" inputmode="numeric">
              <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-4 fw-bold otp-digit rounded-3" id="digit-5" name="otp_5" autocomplete="off" inputmode="numeric">
              <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-4 fw-bold otp-digit rounded-3" id="digit-6" name="otp_6" autocomplete="off" inputmode="numeric">
            </div>

            <!-- Single Hidden Field for Full OTP -->
            <input type="hidden" name="otp" id="fullOtpInput" value="">

            <!-- Submit Button -->
            <button type="submit" id="btnVerify" class="btn btn-primary-gradient btn-lg w-100 rounded-3 fw-bold text-white shadow-sm mb-3">
              <i class="bi bi-patch-check-fill me-2"></i> Verifikasi Sekarang
            </button>
          </form>

          <!-- Resend Section & Countdown Timer -->
          <div class="text-center pt-2 border-top border-light-subtle">
            <p class="small text-muted mb-2">Tidak menerima kode verifikasi?</p>
            <form id="resendForm" action="<?= BASE_URL ?>/index.php?url=otp/requestOtp" method="POST" class="d-inline">
              <input type="hidden" name="identifier" value="<?= htmlspecialchars($data['identifier']) ?>">
              <input type="hidden" name="channel" value="<?= htmlspecialchars($data['channel']) ?>">
              <button type="submit" id="btnResend" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-semibold" <?= ($data['cooldown'] > 0) ? 'disabled' : '' ?>>
                <i class="bi bi-arrow-clockwise me-1"></i>
                <span id="resendBtnText">Kirim Ulang Kode</span>
                <span id="countdownBadge" class="<?= ($data['cooldown'] > 0) ? '' : 'd-none' ?>">(<span id="countdownTimer"><?= $data['cooldown'] ?></span>s)</span>
              </button>
            </form>
          </div>

          <!-- Status Card Info -->
          <?php 
            $conn = $data['connection'] ?? ['status' => 'empty_token', 'connected' => false, 'message' => 'Token API belum dikonfigurasi.'];
          ?>
          <div class="mt-4 p-3 bg-light rounded-3 border border-light-subtle text-secondary small">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span><i class="bi bi-broadcast me-1 text-primary"></i> <strong>Gateway WhatsApp:</strong></span>
              <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($data['provider']) ?></span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-1">
              <span><i class="bi bi-shield-check me-1 text-success"></i> <strong>Status Koneksi API:</strong></span>
              <?php if (!empty($conn['connected'])): ?>
                <span class="badge bg-success rounded-pill px-3 py-1">
                  <i class="bi bi-check-circle-fill me-1"></i> Terhubung
                </span>
              <?php elseif (($conn['status'] ?? '') === 'disconnected_device'): ?>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                  <i class="bi bi-exclamation-triangle-fill me-1"></i> Device Disconnect
                </span>
              <?php elseif (($conn['status'] ?? '') === 'invalid_token'): ?>
                <span class="badge bg-danger rounded-pill px-3 py-1">
                  <i class="bi bi-x-circle-fill me-1"></i> Token Tidak Valid
                </span>
              <?php elseif (($conn['status'] ?? '') === 'connection_error'): ?>
                <span class="badge bg-danger rounded-pill px-3 py-1">
                  <i class="bi bi-cloud-slash me-1"></i> Gagal Terhubung
                </span>
              <?php else: ?>
                <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                  <i class="bi bi-dash-circle me-1"></i> Belum Terhubung
                </span>
              <?php endif; ?>
            </div>
            <?php if (!empty($conn['message'])): ?>
              <div class="extra-small text-muted mt-2 pt-2 border-top border-light-subtle">
                <i class="bi bi-info-circle me-1"></i> <?= htmlspecialchars($conn['message']) ?>
              </div>
            <?php endif; ?>
          </div>

        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-light text-center py-3 border-top-0">
          <a href="<?= BASE_URL ?>/index.php?url=auth/login" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Login
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const digits = document.querySelectorAll('.otp-digit');
  const fullOtpInput = document.getElementById('fullOtpInput');
  const btnResend = document.getElementById('btnResend');
  const countdownTimer = document.getElementById('countdownTimer');
  const countdownBadge = document.getElementById('countdownBadge');
  const resendBtnText = document.getElementById('resendBtnText');
  const alertBox = document.getElementById('otpAlertBox');

  // 1. Digit Auto-Focus and Navigation
  digits.forEach((input, index) => {
    input.addEventListener('input', function (e) {
      // sanitize numbers only
      this.value = this.value.replace(/[^0-9]/g, '');

      if (this.value.length === 1 && index < digits.length - 1) {
        digits[index + 1].focus();
      }
      updateFullOtp();
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !this.value && index > 0) {
        digits[index - 1].focus();
      }
    });

    // Handle Paste event (user copies 6-digit code)
    input.addEventListener('paste', function (e) {
      e.preventDefault();
      const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
      if (/^\d{6}$/.test(pastedData)) {
        pastedData.split('').forEach((char, i) => {
          if (digits[i]) digits[i].value = char;
        });
        digits[digits.length - 1].focus();
        updateFullOtp();
      }
    });
  });

  function updateFullOtp() {
    let combined = '';
    digits.forEach(d => combined += d.value);
    fullOtpInput.value = combined;
  }

  // 2. Cooldown Countdown Timer
  let remainingCooldown = parseInt(countdownTimer ? countdownTimer.textContent : 0, 10) || 0;
  if (remainingCooldown > 0) {
    startCooldown(remainingCooldown);
  }

  function startCooldown(seconds) {
    remainingCooldown = seconds;
    btnResend.disabled = true;
    countdownBadge.classList.remove('d-none');
    countdownTimer.textContent = remainingCooldown;

    const interval = setInterval(function () {
      remainingCooldown--;
      if (remainingCooldown <= 0) {
        clearInterval(interval);
        btnResend.disabled = false;
        countdownBadge.classList.add('d-none');
        resendBtnText.textContent = 'Kirim Ulang Kode';
      } else {
        countdownTimer.textContent = remainingCooldown;
      }
    }, 1000);
  }

  // 3. AJAX Verification Submission
  const otpForm = document.getElementById('otpForm');
  otpForm.addEventListener('submit', function (e) {
    updateFullOtp();
    if (fullOtpInput.value.length !== 6) {
      e.preventDefault();
      showAlert('danger', 'Harap masukkan 6 digit kode OTP lengkap.');
      return;
    }
  });

  function showAlert(type, message) {
    alertBox.className = 'alert alert-' + type + ' rounded-3 py-2 px-3 small mb-3';
    alertBox.innerHTML = '<i class="bi bi-info-circle-fill me-1"></i> ' + message;
    alertBox.classList.remove('d-none');
  }
});
</script>
