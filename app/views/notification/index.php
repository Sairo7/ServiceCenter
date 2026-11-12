<div class="container py-4 my-auto">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
      <span class="badge bg-primary px-3 py-1 rounded-pill extra-small fw-bold text-uppercase mb-1">
        <i class="bi bi-bell-fill me-1"></i> In-App Notification Center
      </span>
      <h3 class="fw-extrabold text-dark mb-0">Pusat Notifikasi Aplikasi</h3>
    </div>
    <div class="d-flex align-items-center gap-2">
      <?php if ($unread_count > 0): ?>
        <button type="button" class="btn btn-outline-primary rounded-pill btn-sm px-3 fw-bold" id="btnMarkAllPage">
          <i class="bi bi-check2-all me-1"></i> Tandai Semua Dibaca (<?= $unread_count ?>)
        </button>
      <?php endif; ?>
    </div>
  </div>

  <?php Flasher::flash(); ?>

  <div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
      
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center justify-content-between">
          <h5 class="fw-bold mb-0 text-white">Riwayat Notifikasi Masuk</h5>
          <span class="badge bg-white text-dark font-weight-bold">Total: <?= count($notifications) ?></span>
        </div>

        <div class="card-body p-0">
          <?php if (!empty($notifications)): ?>
            <div class="list-group list-group-flush" id="notifListHistory">
              <?php foreach ($notifications as $n): ?>
                <?php
                  $isUnread = intval($n['is_read']) === 0;
                  $bgClass  = $isUnread ? 'bg-primary bg-opacity-10 border-start border-primary border-4' : 'bg-white';
                  
                  $icon = 'bi-bell-fill text-primary';
                  if ($n['type'] === 'assignment') $icon = 'bi-person-badge-fill text-warning';
                  elseif ($n['type'] === 'payment') $icon = 'bi-wallet2 text-success';
                  elseif ($n['type'] === 'order_status') $icon = 'bi-gear-wide-connected text-info';
                ?>
                <div class="list-group-item p-4 <?= $bgClass ?> transition-all">
                  <div class="d-flex align-items-start gap-3">
                    <div class="bg-white rounded-circle p-2 shadow-xs border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.2rem;">
                      <i class="bi <?= $icon ?>"></i>
                    </div>

                    <div class="flex-grow-1">
                      <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($n['title']) ?></h6>
                        <span class="extra-small text-muted"><?= date('d M Y, H:i', strtotime($n['created_at'])) ?></span>
                      </div>
                      
                      <p class="small text-secondary mb-2"><?= nl2br(htmlspecialchars($n['message'])) ?></p>

                      <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($n['link_url'])): ?>
                          <a href="<?= BASE_URL ?>/<?= ltrim($n['link_url'], '/') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold font-weight-medium text-white extra-small btn-notif-link" data-id="<?= $n['id'] ?>">
                            <i class="bi bi-arrow-right-circle me-1"></i> Buka Halaman
                          </a>
                        <?php endif; ?>
                        
                        <?php if ($isUnread): ?>
                          <span class="badge bg-danger extra-small rounded-pill">Belum Dibaca</span>
                        <?php else: ?>
                          <span class="extra-small text-muted"><i class="bi bi-check-all text-success me-1"></i> Sudah dibaca</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-5 text-muted p-4">
              <i class="bi bi-bell-slash display-3 d-block text-muted mb-2"></i>
              <h5 class="fw-bold">Belum Ada Notifikasi</h5>
              <p class="small text-muted mb-0">Semua pemberitahuan order, penugasan, dan pembayaran akan muncul di sini.</p>
            </div>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?= BASE_URL ?>';

    // Mark single notification as read when link clicked
    document.querySelectorAll('.btn-notif-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
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

    // Mark all as read button
    document.getElementById('btnMarkAllPage')?.addEventListener('click', function() {
        fetch(baseUrl + '/index.php?url=notification/mark_read_api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mark_all: true })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
});
</script>
