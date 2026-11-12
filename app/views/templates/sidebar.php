<?php
$user = Auth::user();
$role = $user['role'] ?? 'customer';
?>
<div class="sidebar bg-white shadow-sm border-end h-100 p-3 rounded-4">
  <div class="user-profile-widget p-3 mb-3 bg-light rounded-3 d-flex align-items-center gap-3 border">
    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.2rem;">
      <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
    </div>
    <div class="overflow-hidden">
      <h6 class="mb-0 text-truncate font-weight-bold text-dark fs-6"><?= htmlspecialchars($user['name'] ?? 'Guest User') ?></h6>
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 text-capitalize" style="font-size: 0.75rem;">
        <i class="bi bi-shield-check me-1"></i><?= str_replace('_', ' ', $role) ?>
      </span>
    </div>
  </div>

  <div class="text-uppercase text-muted fw-bold mb-2 ps-2" style="font-size: 0.7rem; letter-spacing: 1px;">Navigasi Utama</div>

  <ul class="nav nav-pills flex-column gap-1 mb-auto">
    <li class="nav-item">
      <a href="<?= BASE_URL ?>/index.php?url=dashboard/<?= $role ?>" class="nav-link text-dark <?= ($active_menu ?? '') === 'dashboard' ? 'active-sidebar' : '' ?> d-flex align-items-center py-2 px-3 rounded-3">
        <i class="bi bi-grid-1x2-fill me-2 text-primary"></i> Dashboard Saya
      </a>
    </li>

    <?php if ($role === 'operator'): ?>
    <li class="nav-item">
      <a href="<?= BASE_URL ?>/index.php?url=user" class="nav-link text-dark <?= ($active_menu ?? '') === 'total_users' ? 'active-sidebar' : '' ?> d-flex align-items-center py-2 px-3 rounded-3">
        <i class="bi bi-people-fill me-2 text-primary"></i> Total Pengguna
      </a>
    </li>
    <?php endif; ?>

    <li class="nav-item">
      <a href="<?= BASE_URL ?>/index.php?url=service" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
        <i class="bi bi-tools me-2 text-primary"></i> Katalog Layanan Jasa
      </a>
    </li>

    <?php if ($role === 'customer'): ?>
      <li class="nav-item">
        <span class="nav-link text-muted extra-small py-1 px-3 fw-bold text-uppercase">Menu Customer</span>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=order/create" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-plus-circle-fill me-2 text-primary"></i> Buat Pengajuan Jasa
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=order/my_orders" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-clock-history me-2 text-primary"></i> Status & Riwayat Order
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'operator'): ?>
      <li class="nav-item">
        <span class="nav-link text-muted extra-small py-1 px-3 fw-bold text-uppercase">Menu Operator</span>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=order/manage" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-inbox-fill me-2 text-primary"></i> Kelola Order Masuk
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=sparepart/manage" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-boxes me-2 text-primary"></i> Inventaris Sparepart
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=service/manage" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-wrench-adjustable-circle me-2 text-primary"></i> Master Katalog Jasa
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=report" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-graph-up-arrow me-2 text-primary"></i> Pusat Laporan TEFA
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=order/ratings" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-star-fill me-2 text-warning"></i> Rating & Feedback Customer
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=config" class="nav-link text-dark <?= ($active_menu ?? '') === 'system_config' ? 'active-sidebar' : '' ?> d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-gear-fill me-2 text-primary"></i> Konfigurasi Sistem
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'teknisi'): ?>
      <li class="nav-item">
        <span class="nav-link text-muted extra-small py-1 px-3 fw-bold text-uppercase">Menu Teknisi Siswa</span>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=technician/jobs" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-card-checklist me-2 text-primary"></i> Tugas Lapangan Saya
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=technician/troubleshoot" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-cpu-fill me-2 text-primary"></i> AI Troubleshoot Assistant
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=technician/schedule" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-calendar-range me-2 text-primary"></i> Agenda Jadwal Kerja
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'kepala_sekolah'): ?>
      <li class="nav-item">
        <span class="nav-link text-muted extra-small py-1 px-3 fw-bold text-uppercase">Menu Eksekutif</span>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=report" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-file-earmark-bar-graph-fill me-2 text-primary"></i> Pusat Laporan Eksekutif
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/index.php?url=report&tab=ratings" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
          <i class="bi bi-star-fill me-2 text-warning"></i> Monitoring Rating Layanan
        </a>
      </li>
    <?php endif; ?>

    <hr class="my-2">
    
    <li class="nav-item">
      <a href="<?= BASE_URL ?>/index.php?url=profile" class="nav-link text-dark d-flex align-items-center py-2 px-3 rounded-3">
        <i class="bi bi-person-lines-fill me-2 text-info"></i> Profil Pengguna
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= BASE_URL ?>/index.php" class="nav-link text-muted d-flex align-items-center py-2 px-3 rounded-3">
        <i class="bi bi-globe me-2"></i> Beranda Publik
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= BASE_URL ?>/index.php?url=auth/logout" class="nav-link text-danger d-flex align-items-center py-2 px-3 rounded-3">
        <i class="bi bi-box-arrow-right me-2"></i> Keluar Aplikasi
      </a>
    </li>
  </ul>
</div>
