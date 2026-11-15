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
                <a href="javascript:void(0)" class="text-decoration-none small text-primary fw-semibold" id="btnOpenForgotPassword">
                  Lupa Kata Sandi?
                </a>
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

            <button type="submit" class="btn btn-primary-gradient btn-lg w-100 rounded-3 fw-bold text-white shadow-sm mb-0">
              <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sistem
            </button>
          </form>

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

<!-- Modal Lupa Kata Sandi dengan Verifikasi OTP WhatsApp Langsung -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      
      <!-- Modal Header -->
      <div class="modal-header bg-dark-gradient text-white py-3 px-4 border-0">
        <div class="d-flex align-items-center gap-2">
          <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: linear-gradient(135deg, #10b981, #06b6d4);">
            <i class="bi bi-shield-lock-fill fs-5"></i>
          </div>
          <div>
            <h6 class="modal-title fw-bold text-white mb-0" id="forgotPasswordModalLabel">Lupa Kata Sandi</h6>
            <span class="extra-small" style="color: #94a3b8;">Verifikasi OTP via WhatsApp</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btnCloseForgotModal"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-4 bg-white">
        <!-- Alert Box Feedback -->
        <div id="forgotAlertBox" class="alert d-none rounded-3 py-2 px-3 small mb-3"></div>

        <!-- STEP 1: INPUT IDENTITAS & REQUEST OTP -->
        <div id="forgotStep1">
          <p class="text-muted small mb-3">
            Masukkan <strong>Username</strong> atau <strong>Nomor WhatsApp</strong> akun Anda. Sistem akan mengirimkan kode 6 digit OTP verifikasi langsung ke WhatsApp Anda.
          </p>
          <div class="mb-3">
            <label class="form-label fw-semibold text-dark small">Username atau Nomor WhatsApp</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">
                <i class="bi bi-person-badge"></i>
              </span>
              <input type="text" id="forgotIdentityInput" class="form-control rounded-end-3" placeholder="Contoh: operator atau 081234567890" autofocus>
            </div>
          </div>
          <button type="button" id="btnForgotRequestOtp" class="btn btn-primary-gradient w-100 rounded-3 fw-bold text-white shadow-sm py-2">
            <i class="bi bi-whatsapp me-2"></i> Kirim Kode OTP ke WhatsApp
          </button>
        </div>

        <!-- STEP 2: VERIFIKASI 6 DIGIT OTP -->
        <div id="forgotStep2" class="d-none">
          <div class="text-center mb-3">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small">
              <i class="bi bi-whatsapp me-1"></i> OTP Terkirim ke: <strong id="forgotMaskedPhoneDisplay">-</strong>
            </span>
            <p class="extra-small text-muted mt-2 mb-0">
              Masa berlaku kode: <strong>5 Menit</strong> &bull; Maksimal percobaan: <strong>3x</strong>
            </p>
          </div>

          <label class="form-label fw-semibold text-dark small d-block text-center mb-2">
            Masukkan 6 Digit Kode OTP
          </label>
          <div class="d-flex justify-content-between gap-2 mb-3">
            <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-5 fw-bold forgot-digit rounded-3" id="f-digit-1" inputmode="numeric">
            <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-5 fw-bold forgot-digit rounded-3" id="f-digit-2" inputmode="numeric">
            <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-5 fw-bold forgot-digit rounded-3" id="f-digit-3" inputmode="numeric">
            <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-5 fw-bold forgot-digit rounded-3" id="f-digit-4" inputmode="numeric">
            <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-5 fw-bold forgot-digit rounded-3" id="f-digit-5" inputmode="numeric">
            <input type="text" maxlength="1" class="form-control form-control-lg text-center fs-5 fw-bold forgot-digit rounded-3" id="f-digit-6" inputmode="numeric">
          </div>

          <button type="button" id="btnForgotVerifyOtp" class="btn btn-primary-gradient w-100 rounded-3 fw-bold text-white shadow-sm py-2 mb-3">
            <i class="bi bi-patch-check-fill me-2"></i> Verifikasi Kode OTP
          </button>

          <div class="text-center pt-2 border-top">
            <span class="small text-muted me-1">Tidak menerima kode?</span>
            <button type="button" id="btnForgotResendOtp" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold text-primary">
              <span id="forgotResendText">Kirim Ulang</span> <span id="forgotCountdownTimer" class="d-none"></span>
            </button>
          </div>
        </div>

        <!-- STEP 3: RESET PASSWORD BARU -->
        <div id="forgotStep3" class="d-none">
          <p class="text-muted small mb-3">
            <i class="bi bi-shield-check text-success me-1"></i> Identitas berhasil diverifikasi! Silakan buat kata sandi baru Anda.
          </p>
          <div class="mb-3">
            <label class="form-label fw-semibold text-dark small">Kata Sandi Baru</label>
            <input type="password" id="forgotNewPassword" class="form-control rounded-3" placeholder="Minimal 6 karakter" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold text-dark small">Konfirmasi Kata Sandi Baru</label>
            <input type="password" id="forgotConfirmPassword" class="form-control rounded-3" placeholder="Ulangi kata sandi baru" required>
          </div>
          <button type="button" id="btnForgotSubmitReset" class="btn btn-success w-100 rounded-3 fw-bold text-white shadow-sm py-2">
            <i class="bi bi-check2-circle me-2"></i> Simpan Kata Sandi Baru
          </button>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-footer bg-light py-2 px-4 border-top-0 d-flex justify-content-between">
        <span class="extra-small text-muted">
          <i class="bi bi-lock me-1"></i> Keamanan Enkripsi End-to-End
        </span>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
          Tutup
        </button>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?= BASE_URL ?>';
    const forgotModalEl = document.getElementById('forgotPasswordModal');
    let forgotModal = null;
    if (forgotModalEl && typeof bootstrap !== 'undefined') {
        forgotModal = new bootstrap.Modal(forgotModalEl);
    }

    const btnOpenForgot = document.getElementById('btnOpenForgotPassword');
    const alertBox = document.getElementById('forgotAlertBox');

    // Steps
    const step1 = document.getElementById('forgotStep1');
    const step2 = document.getElementById('forgotStep2');
    const step3 = document.getElementById('forgotStep3');

    // Step 1 Elements
    const identityInput = document.getElementById('forgotIdentityInput');
    const btnRequestOtp = document.getElementById('btnForgotRequestOtp');

    // Step 2 Elements
    const maskedPhoneDisplay = document.getElementById('forgotMaskedPhoneDisplay');
    const digitInputs = document.querySelectorAll('.forgot-digit');
    const btnVerifyOtp = document.getElementById('btnForgotVerifyOtp');
    const btnResendOtp = document.getElementById('btnForgotResendOtp');
    const forgotResendText = document.getElementById('forgotResendText');
    const forgotCountdown = document.getElementById('forgotCountdownTimer');

    // Step 3 Elements
    const newPasswordInput = document.getElementById('forgotNewPassword');
    const confirmPasswordInput = document.getElementById('forgotConfirmPassword');
    const btnSubmitReset = document.getElementById('btnForgotSubmitReset');

    let currentPhone = '';
    let countdownInterval = null;

    function showAlert(msg, type = 'danger') {
        alertBox.className = `alert alert-${type} rounded-3 py-2 px-3 small mb-3`;
        alertBox.innerHTML = msg;
        alertBox.classList.remove('d-none');
    }

    function hideAlert() {
        alertBox.classList.add('d-none');
        alertBox.innerHTML = '';
    }

    // Open Modal
    btnOpenForgot?.addEventListener('click', function() {
        hideAlert();
        step1.classList.remove('d-none');
        step2.classList.add('d-none');
        step3.classList.add('d-none');
        identityInput.value = document.getElementById('username')?.value || '';
        if (forgotModal) forgotModal.show();
        setTimeout(() => identityInput.focus(), 400);
    });

    // Step 1: Request OTP
    btnRequestOtp?.addEventListener('click', function() {
        const val = identityInput.value.trim();
        if (!val) {
            showAlert('Silakan masukkan username atau nomor WhatsApp Anda.');
            return;
        }

        btnRequestOtp.disabled = true;
        btnRequestOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memeriksa & Mengirim OTP...';
        hideAlert();

        fetch(baseUrl + '/index.php?url=auth/forgotRequestOtp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ identity: val })
        })
        .then(async res => {
            const isJson = res.headers.get('content-type')?.includes('application/json');
            const data = isJson ? await res.json() : null;
            if (!res.ok) {
                const errorMsg = (data && data.message) ? data.message : 'Respon server (' + res.status + '): Gagal memproses permintaan.';
                throw new Error(errorMsg);
            }
            return data;
        })
        .then(data => {
            btnRequestOtp.disabled = false;
            btnRequestOtp.innerHTML = '<i class="bi bi-whatsapp me-2"></i> Kirim Kode OTP ke WhatsApp';

            if (data && data.success) {
                currentPhone = data.phone;
                maskedPhoneDisplay.innerText = data.masked_phone || data.phone;
                step1.classList.add('d-none');
                step2.classList.remove('d-none');
                showAlert(data.message, 'success');
                startCountdown(data.cooldown || 60);

                digitInputs.forEach(d => d.value = '');
                digitInputs[0].focus();
            } else {
                showAlert((data && data.message) ? data.message : 'Nomor WhatsApp atau akun tidak ditemukan dalam sistem.', 'danger');
            }
        })
        .catch(err => {
            console.error('Forgot Password Request Error:', err);
            btnRequestOtp.disabled = false;
            btnRequestOtp.innerHTML = '<i class="bi bi-whatsapp me-2"></i> Kirim Kode OTP ke WhatsApp';
            showAlert(err.message || 'Terjadi kesalahan komunikasi server. Silakan coba kembali.');
        });
    });

    // OTP 6 Box Digit Auto-Tab Navigation
    digitInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value && index < digitInputs.length - 1) {
                digitInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                digitInputs[index - 1].focus();
            }
            if (e.key === 'Enter') {
                btnVerifyOtp.click();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
            const digits = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
            if (digits) {
                digits.split('').forEach((d, i) => {
                    if (digitInputs[i]) digitInputs[i].value = d;
                });
                const nextIndex = Math.min(digits.length, digitInputs.length - 1);
                digitInputs[nextIndex].focus();
            }
        });
    });

    // Step 2: Verify OTP
    btnVerifyOtp?.addEventListener('click', function() {
        let code = '';
        digitInputs.forEach(d => code += d.value.trim());

        if (code.length < 6) {
            showAlert('Masukkan lengkap 6 digit kode OTP verifikasi.');
            return;
        }

        btnVerifyOtp.disabled = true;
        btnVerifyOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
        hideAlert();

        fetch(baseUrl + '/index.php?url=auth/forgotVerifyOtp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phone: currentPhone, otp: code })
        })
        .then(res => res.json())
        .then(data => {
            btnVerifyOtp.disabled = false;
            btnVerifyOtp.innerHTML = '<i class="bi bi-patch-check-fill me-2"></i> Verifikasi Kode OTP';

            if (data.success) {
                clearInterval(countdownInterval);
                step2.classList.add('d-none');
                step3.classList.remove('d-none');
                showAlert(data.message, 'success');
                setTimeout(() => newPasswordInput.focus(), 300);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            btnVerifyOtp.disabled = false;
            btnVerifyOtp.innerHTML = '<i class="bi bi-patch-check-fill me-2"></i> Verifikasi Kode OTP';
            showAlert('Terjadi kendala saat verifikasi OTP. Silakan coba lagi.');
        });
    });

    // Resend OTP in Step 2
    btnResendOtp?.addEventListener('click', function() {
        if (btnResendOtp.disabled) return;
        btnResendOtp.disabled = true;
        forgotResendText.innerText = 'Mengirim...';

        fetch(baseUrl + '/index.php?url=auth/forgotRequestOtp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ identity: currentPhone })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                startCountdown(data.cooldown || 60);
            } else {
                showAlert(data.message, 'warning');
                btnResendOtp.disabled = false;
                forgotResendText.innerText = 'Kirim Ulang';
            }
        })
        .catch(err => {
            btnResendOtp.disabled = false;
            forgotResendText.innerText = 'Kirim Ulang';
            showAlert('Gagal meminta ulang kode OTP.');
        });
    });

    function startCountdown(seconds) {
        clearInterval(countdownInterval);
        btnResendOtp.disabled = true;
        forgotCountdown.classList.remove('d-none');

        let remaining = seconds;
        forgotCountdown.innerText = `(${remaining}s)`;

        countdownInterval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                btnResendOtp.disabled = false;
                forgotResendText.innerText = 'Kirim Ulang';
                forgotCountdown.classList.add('d-none');
            } else {
                forgotCountdown.innerText = `(${remaining}s)`;
            }
        }, 1000);
    }

    // Step 3: Reset Password
    btnSubmitReset?.addEventListener('click', function() {
        const newPass = newPasswordInput.value.trim();
        const confPass = confirmPasswordInput.value.trim();

        if (newPass.length < 6) {
            showAlert('Kata sandi baru minimal 6 karakter.');
            return;
        }

        if (newPass !== confPass) {
            showAlert('Konfirmasi kata sandi tidak cocok.');
            return;
        }

        btnSubmitReset.disabled = true;
        btnSubmitReset.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan Kata Sandi...';
        hideAlert();

        fetch(baseUrl + '/index.php?url=auth/forgotResetPassword', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                new_password: newPass,
                confirm_password: confPass
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1200);
            } else {
                btnSubmitReset.disabled = false;
                btnSubmitReset.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Simpan Kata Sandi Baru';
                showAlert(data.message, 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            btnSubmitReset.disabled = false;
            btnSubmitReset.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Simpan Kata Sandi Baru';
            showAlert('Terjadi kesalahan saat menyimpan kata sandi baru.');
        });
    });
});
</script>
