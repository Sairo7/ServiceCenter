<footer class="footer-custom mt-auto py-5 border-top border-dark" style="background-color: #0f172a; color: #cbd5e1;">
  <div class="container text-center text-md-start">
    <div class="row gy-4">
      <div class="col-md-5">
        <div class="d-flex align-items-center mb-3">
          <div class="me-2 d-flex align-items-center justify-content-center text-white rounded-3 shadow-sm" style="width: 36px; height: 36px; background: linear-gradient(135deg, #2563eb, #06b6d4);">
            <i class="bi bi-hdd-network-fill fs-6"></i>
          </div>
          <h5 class="fw-bold mb-0 text-white">TKJ <span style="color: #38bdf8;">Service Center</span></h5>
        </div>
        <p class="small mb-0" style="color: #cbd5e1; line-height: 1.6;">
          Platform unit bisnis jasa siswa Teknik Komputer dan Jaringan (TKJ). Memberikan pelayanan teknis profesional, terjangkau, dan berkualitas untuk sekolah dan masyarakat umum.
        </p>
      </div>
      <div class="col-md-3 offset-md-1">
        <h6 class="fw-bold text-white mb-3">Layanan Utama</h6>
        <ul class="list-unstyled small mb-0 d-flex flex-column gap-2" style="color: #cbd5e1;">
          <li><i class="bi bi-chevron-right me-1" style="color: #06b6d4;"></i> <a href="<?= BASE_URL ?>/index.php?url=service">Instalasi CCTV</a></li>
          <li><i class="bi bi-chevron-right me-1" style="color: #06b6d4;"></i> <a href="<?= BASE_URL ?>/index.php?url=service">Jaringan LAN & Wi-Fi</a></li>
          <li><i class="bi bi-chevron-right me-1" style="color: #06b6d4;"></i> <a href="<?= BASE_URL ?>/index.php?url=service">Service PC & Laptop</a></li>
          <li><i class="bi bi-chevron-right me-1" style="color: #06b6d4;"></i> <a href="<?= BASE_URL ?>/index.php?url=service">Setting Router MikroTik</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6 class="fw-bold text-white mb-3">Kontak Workshop</h6>
        <div class="d-flex flex-column gap-2 small" style="color: #cbd5e1;">
          <div><i class="bi bi-geo-alt-fill text-danger me-2"></i> Lab & Workshop TKJ SMK Negeri</div>
          <div><i class="bi bi-whatsapp text-success me-2"></i> +62 812-3456-7890</div>
          <div><i class="bi bi-envelope-fill text-warning me-2"></i> tkjservice@sekolah.sch.id</div>
        </div>
      </div>
    </div>
    <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.1);">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small" style="color: #94a3b8;">
      <div>&copy; <?= date('Y') ?> <strong class="text-white">TKJ Service Business Web App</strong>. All rights reserved.</div>
      <div class="mt-2 mt-md-0">Diampu oleh Tim Siswa TKJ & Guru Pembimbing</div>
    </div>
  </div>
</footer>

<!-- Floating AI Assistant Chatbot Widget (Global: guest + all logged-in roles) -->
<?php require_once dirname(__DIR__) . '/ai/widget.php'; ?>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom Web App Main JS -->
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
