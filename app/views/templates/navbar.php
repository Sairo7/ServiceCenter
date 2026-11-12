<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm border-bottom border-light-subtle navbar-custom">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center fw-bold fs-5" href="<?= BASE_URL ?>/index.php">
      <div class="brand-icon me-2 d-flex align-items-center justify-content-center text-white rounded-3 shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #2563eb, #06b6d4);">
        <i class="bi bi-hdd-network-fill fs-5"></i>
      </div>
      <span style="color: #0f172a;" class="fw-extrabold">TKJ <span style="color: #2563eb;">Service Center</span></span>
    </a>
    
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item">
          <a class="nav-link <?= ($active_menu ?? '') === 'home' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php">
            <i class="bi bi-house-door me-1"></i> Beranda
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active_menu ?? '') === 'services' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=service">
            <i class="bi bi-tools me-1"></i> Katalog Layanan Jasa
          </a>
        </li>
        <?php if (Auth::check()): ?>
          <?php if (Auth::role() === 'customer'): ?>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'order_create' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=order/create">
                <i class="bi bi-plus-circle-fill me-1 text-primary"></i> Pengajuan Layanan
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'my_orders' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=order/my_orders">
                <i class="bi bi-clock-history me-1 text-primary"></i> Pesanan Saya
              </a>
            </li>
          <?php endif; ?>
          <?php if (Auth::role() === 'operator'): ?>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'manage_order' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=order/manage">
                <i class="bi bi-inbox-fill me-1 text-primary"></i> Kelola Order
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'sparepart' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=sparepart/manage">
                <i class="bi bi-boxes me-1 text-primary"></i> Sparepart
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'transaction_report' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=transaction/report">
                <i class="bi bi-graph-up-arrow me-1 text-primary"></i> Laporan Omset
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'ratings' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=order/ratings">
                <i class="bi bi-star-fill me-1 text-warning"></i> Rating Pelanggan
              </a>
            </li>
          <?php endif; ?>
          <?php if (Auth::role() === 'teknisi'): ?>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'tech_jobs' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=technician/jobs">
                <i class="bi bi-briefcase-fill me-1 text-primary"></i> Tugas Lapangan
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'tech_ai_assistant' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=technician/troubleshoot">
                <i class="bi bi-cpu-fill text-primary me-1"></i> AI Troubleshoot
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'tech_schedule' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=technician/schedule">
                <i class="bi bi-calendar-range me-1 text-primary"></i> Agenda Jadwal
              </a>
            </li>
          <?php endif; ?>
          <?php if (Auth::role() === 'kepala_sekolah'): ?>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'transaction_report' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=transaction/report">
                <i class="bi bi-file-earmark-bar-graph-fill me-1 text-primary"></i> Laporan Omset TEFA
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($active_menu ?? '') === 'ratings' ? 'active fw-bold text-primary' : 'text-dark fw-medium' ?>" href="<?= BASE_URL ?>/index.php?url=order/ratings">
                <i class="bi bi-star-fill me-1 text-warning"></i> Monitoring Rating
              </a>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      
      <div class="d-flex align-items-center gap-2">
        <?php if (Auth::check()): 
          $user = Auth::user();
        ?>
          <!-- NOTIFICATION BELL DROPDOWN -->
          <div class="dropdown me-1">
            <button class="btn btn-outline-secondary btn-sm rounded-circle position-relative p-2 d-flex align-items-center justify-content-center" type="button" id="notifDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="width: 36px; height: 36px;">
              <i class="bi bi-bell-fill fs-6 text-dark"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadgeCounter">
                0
              </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" aria-labelledby="notifDropdownBtn" style="width: 330px; max-height: 480px; overflow: hidden;">
              <div class="p-3 bg-dark text-white d-flex align-items-center justify-content-between">
                <div class="fw-bold small mb-0"><i class="bi bi-bell me-1 text-warning"></i> Notifikasi Saya</div>
                <button type="button" class="btn btn-link text-warning extra-small p-0 text-decoration-none fw-semibold" id="btnMarkAllNav">
                  Tandai Dibaca
                </button>
              </div>
              
              <div class="overflow-auto" style="max-height: 340px;" id="notifListContainer">
                <div class="text-center py-4 text-muted extra-small">
                  <div class="spinner-border spinner-border-sm text-primary mb-1"></div>
                  <div>Memuat notifikasi...</div>
                </div>
              </div>

              <div class="p-2 bg-light text-center border-top">
                <a href="<?= BASE_URL ?>/index.php?url=notification" class="extra-small fw-bold text-primary text-decoration-none">
                  Lihat Semua Notifikasi <i class="bi bi-arrow-right me-1"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- END NOTIFICATION BELL DROPDOWN -->

          <a href="<?= BASE_URL ?>/index.php?url=dashboard" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-medium">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard (<?= ucfirst(str_replace('_', ' ', $user['role'])) ?>)
          </a>
          <a href="<?= BASE_URL ?>/index.php?url=profile" class="btn btn-primary-gradient text-white btn-sm px-3 rounded-pill shadow-sm fw-medium">
            <i class="bi bi-person-circle me-1"></i> Profil Saya
          </a>
          <a href="<?= BASE_URL ?>/index.php?url=auth/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-medium">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/index.php?url=auth/login" class="btn btn-primary-gradient text-white btn-sm px-4 rounded-pill shadow-sm fw-bold">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk / Login
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?= BASE_URL ?>';
    const badge = document.getElementById('notifBadgeCounter');
    const container = document.getElementById('notifListContainer');
    const btnMarkAll = document.getElementById('btnMarkAllNav');

    function loadNavbarNotifications() {
        if (!badge || !container) return;

        fetch(baseUrl + '/index.php?url=notification/unread_api')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const count = data.unread_count || 0;
                if (count > 0) {
                    badge.innerText = count > 99 ? '99+' : count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }

                if (data.items && data.items.length > 0) {
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <a href="${item.link_url}" class="dropdown-item p-3 border-bottom text-wrap notif-nav-item" data-id="${item.id}">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <strong class="extra-small text-dark text-truncate d-inline-block" style="max-width: 190px;">${item.title}</strong>
                                    <span class="text-muted" style="font-size: 0.65rem;">${item.time_ago}</span>
                                </div>
                                <div class="extra-small text-secondary" style="font-size: 0.75rem; line-height: 1.3;">${item.message}</div>
                            </a>
                        `;
                    });
                    container.innerHTML = html;

                    // Bind click event to mark single as read
                    document.querySelectorAll('.notif-nav-item').forEach(el => {
                        el.addEventListener('click', function() {
                            const notifId = this.getAttribute('data-id');
                            if (notifId) {
                                fetch(baseUrl + '/index.php?url=notification/mark_read_api', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ id: notifId })
                                });
                            }
                        });
                    });
                } else {
                    container.innerHTML = '<div class="text-center py-4 text-muted extra-small"><i class="bi bi-check-circle text-success fs-5 d-block mb-1"></i> Tidak ada notifikasi baru</div>';
                }
            }
        })
        .catch(err => console.error(err));
    }

    loadNavbarNotifications();

    btnMarkAll?.addEventListener('click', function(e) {
        e.preventDefault();
        fetch(baseUrl + '/index.php?url=notification/mark_read_api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mark_all: true })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadNavbarNotifications();
            }
        });
    });
});
</script>
