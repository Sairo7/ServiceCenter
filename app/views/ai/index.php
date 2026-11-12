<?php
$role = Auth::role() ?? 'customer';
$roleName = [
    'customer' => 'Customer',
    'teknisi' => 'Teknisi Siswa',
    'operator' => 'Operator TEFA',
    'kepala_sekolah' => 'Kepala Sekolah'
][$role] ?? 'Customer';
?>
<div class="container py-5 my-auto">
  <div class="row justify-content-center">
    <div class="col-lg-8 text-center">
      
      <!-- Icon Banner -->
      <div class="brand-icon mx-auto mb-4 d-flex align-items-center justify-content-center text-white rounded-circle shadow-lg" style="width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb, #06b6d4);">
        <i class="bi bi-robot fs-1"></i>
      </div>

      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
        <i class="bi bi-stars me-1"></i> Floating AI Live Assistant
      </span>

      <h2 class="display-6 fw-extrabold text-navy mb-3">AI <?= htmlspecialchars($roleName) ?> Assistant</h2>
      <p class="text-slate-muted fs-6 mb-4 mx-auto" style="max-width: 620px;">
        Asisten AI TKJ Service Center kini hadir sebagai <strong>Widget Chat Mengambang (Floating Chatbot)</strong> di pojok kanan bawah seluruh halaman aplikasi. Anda dapat berkonsultasi kapan saja tanpa berpindah halaman!
      </p>

      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 text-start">
        <div class="d-flex align-items-center gap-3">
          <div class="p-3 bg-light rounded-circle text-primary fs-3">
            <i class="bi bi-chat-quote-fill"></i>
          </div>
          <div>
            <h6 class="fw-bold text-navy mb-1">Cara Menggunakan:</h6>
            <p class="text-muted small mb-0">
              Klik ikon tombol chat mengambang berwarna biru di <strong>pojok kanan bawah layar</strong> Anda untuk mulai bertanya, melihat riwayat konsultasi, atau gunakan tombol di bawah ini untuk membukanya langsung.
            </p>
          </div>
        </div>
      </div>

      <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
        <button type="button" class="btn btn-primary-gradient px-4 py-3 rounded-pill fw-bold shadow-sm" onclick="if (window.openTkjChatbot) { window.openTkjChatbot(); }">
          <i class="bi bi-chat-dots-fill me-2"></i> Buka Chatbot Sekarang
        </button>
        <a href="<?= BASE_URL ?>/index.php?url=dashboard" class="btn btn-outline-secondary px-4 py-3 rounded-pill fw-bold">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
      </div>

    </div>
  </div>
</div>
