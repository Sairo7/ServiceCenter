<div class="container py-5 my-auto text-center">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      
      <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white overflow-hidden position-relative">
        <div class="position-absolute top-0 start-0 w-100 bg-danger" style="height: 6px;"></div>
        
        <div class="display-1 text-danger mb-3 opacity-90">
          <i class="bi bi-shield-slash-fill"></i>
        </div>
        
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold text-uppercase mb-3" style="letter-spacing: 1px;">
          HTTP 403 Forbidden Access
        </span>
        
        <h2 class="fw-extrabold text-dark mb-2">Akses Ditolak / Unauthorized</h2>
        
        <p class="text-muted lead fs-6 mb-4">
          Maaf, akun Anda saat ini terdaftar sebagai role <span class="badge bg-primary text-uppercase"><?= str_replace('_', ' ', Auth::role() ?? 'Guest') ?></span>. Anda tidak memiliki wewenang untuk membuka halaman atau rute yang Anda minta.
        </p>

        <div class="alert alert-light border text-start small mb-4">
          <div class="fw-bold text-dark mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Kebijakan Keamanan RBAC:</div>
          <div>Sistem membatasi navigasi agar setiap pengguna hanya dapat mengelola data sesuai dengan peran resminya.</div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-center">
          <?php if (Auth::check()): ?>
            <a href="<?= BASE_URL ?>/index.php?url=dashboard" class="btn btn-primary-gradient px-4 py-2 rounded-pill text-white fw-bold shadow-sm">
              <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard Saya
            </a>
          <?php else: ?>
            <a href="<?= BASE_URL ?>/index.php?url=auth/login" class="btn btn-primary px-4 py-2 rounded-pill font-weight-bold">
              <i class="bi bi-box-arrow-in-right me-1"></i> Silakan Login Terlebih Dahulu
            </a>
          <?php endif; ?>
          
          <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
            <i class="bi bi-house me-1"></i> Beranda Publik
          </a>
        </div>

      </div>

    </div>
  </div>
</div>
