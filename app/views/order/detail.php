<div class="container py-4 my-auto">
  
  <div class="mb-3">
    <?php if (Auth::role() === 'operator'): ?>
      <a href="<?= BASE_URL ?>/index.php?url=order/manage" class="text-decoration-none text-secondary small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Panel Pengelolaan Order Operator
      </a>
    <?php elseif (Auth::role() === 'teknisi'): ?>
      <a href="<?= BASE_URL ?>/index.php?url=technician/jobs" class="text-decoration-none text-secondary small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Tugas Teknisi
      </a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/index.php?url=order/my_orders" class="text-decoration-none text-secondary small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pesanan Saya
      </a>
    <?php endif; ?>
  </div>

  <?php Flasher::flash(); ?>

  <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-dark-gradient text-white p-4 p-md-5 border-0">
      <div class="row align-items-center gy-3">
        <div class="col-md-8">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary px-3 py-2 rounded-pill font-weight-bold">
              <i class="bi bi-ticket-perforated-fill me-1"></i> Tiket Order: <?= htmlspecialchars($order['order_code']) ?>
            </span>
            <span class="badge bg-secondary-subtle text-light border px-2 py-1 extra-small">
              Tgl Pengajuan: <?= date('d M Y H:i', strtotime($order['created_at'])) ?>
            </span>
          </div>
          <h2 class="display-6 fw-extrabold mb-1"><?= htmlspecialchars($order['service_name']) ?></h2>
          <p class="text-white opacity-75 mb-0">Pemesan: <strong><?= htmlspecialchars($order['customer_name']) ?></strong> (<?= htmlspecialchars($order['customer_phone']) ?>)</p>
        </div>
        <div class="col-md-4 text-md-end">
          <div class="bg-white bg-opacity-10 p-3 rounded-3 d-inline-block text-start border border-white border-opacity-25 mb-2">
            <span class="text-white-50 extra-small d-block mb-1">Status Pengerjaan Saat Ini:</span>
            <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill fw-bold">
              <i class="bi bi-gear-wide-connected me-1"></i> <?= htmlspecialchars($order['status']) ?>
            </span>
          </div>
          <div>
            <a href="<?= BASE_URL ?>/index.php?url=transaction/invoice/<?= $order['id'] ?>" class="btn btn-success text-white font-weight-bold rounded-pill shadow-sm px-3">
              <i class="bi bi-receipt me-1"></i> Rincian Tagihan & Invoice
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body p-4 p-md-5">
      
      <!-- 10-Status Interactive Progression Bar -->
      <h6 class="fw-bold text-uppercase text-muted extra-small tracking-wider mb-3">Timeline Progress Alur Order (10 Status)</h6>
      <div class="p-3 bg-light rounded-4 border mb-5 overflow-auto">
        <div class="d-flex align-items-center justify-content-between min-w-700 position-relative py-2">
          <?php
            $statuses = [
              'Diajukan', 'Menunggu Verifikasi', 'Disetujui', 'Teknisi Ditugaskan', 
              'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart', 
              'Selesai', 'Ditutup'
            ];
            $currentStatus = $order['status'];
            $currentIndex = array_search($currentStatus, $statuses);
            if ($currentStatus === 'Dibatalkan') {
              echo '<div class="alert alert-danger w-100 mb-0 py-2 text-center font-weight-bold"><i class="bi bi-x-circle-fill me-1"></i> TIKET INI TELAH DIBATALKAN</div>';
            } else {
              foreach ($statuses as $idx => $stName) {
                $isDone = ($currentIndex !== false && $idx <= $currentIndex);
                $isCurrent = ($currentIndex !== false && $idx === $currentIndex);
                $circleClass = $isCurrent ? 'bg-warning text-dark border border-warning' : ($isDone ? 'bg-success text-white' : 'bg-secondary bg-opacity-25 text-muted');
                echo '<div class="text-center position-relative z-1 px-2" style="flex: 1;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-1 fw-bold ' . $circleClass . '" style="width: 32px; height: 32px; font-size: 0.85rem;">' . ($idx + 1) . '</div>
                        <div class="extra-small ' . ($isCurrent ? 'fw-bold text-primary' : ($isDone ? 'text-dark' : 'text-muted')) . '" style="font-size: 0.7rem;">' . $stName . '</div>
                      </div>';
              }
            }
          ?>
        </div>
      </div>

      <div class="row gy-4 mb-4">
        <!-- Details Left -->
        <div class="col-lg-7">
          <h5 class="fw-bold text-dark mb-3">Rincian Deskripsi & Keluhan</h5>
          <div class="p-3 bg-light rounded-3 border mb-4">
            <p class="text-dark mb-0 fs-6">
              <?= nl2br(htmlspecialchars($order['problem_description'])) ?>
            </p>
          </div>

          <h5 class="fw-bold text-dark mb-3">Informasi Lokasi & Pelaksanaan</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border">
                <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Alamat Pengerjaan</label>
                <div class="fw-semibold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($order['service_address']) ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border">
                <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Jadwal yang Diinginkan</label>
                <div class="fw-semibold text-dark"><i class="bi bi-calendar-event text-warning me-1"></i> <?= date('d F Y', strtotime($order['scheduled_date'])) ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border">
                <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Teknisi Siswa Ditugaskan</label>
                <div class="fw-semibold text-primary"><i class="bi bi-person-badge me-1"></i> <?= htmlspecialchars($order['technician_name'] ?? 'Belum Ditunjuk (Menunggu Operator)') ?></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border">
                <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Prioritas Pengerjaan</label>
                <div class="fw-semibold text-capitalize text-dark"><i class="bi bi-flag-fill text-info me-1"></i> <?= htmlspecialchars($order['priority']) ?></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Side: Operator Actions & Photo Evidence -->
        <div class="col-lg-5">
          
          <!-- Operator Verification & Assign Card -->
          <?php if (Auth::role() === 'operator'): ?>
            <div class="card border-0 bg-dark-gradient text-white p-4 rounded-4 mb-4">
              <h5 class="fw-bold mb-3"><i class="bi bi-shield-check text-warning me-2"></i> Operator Actions & Verifikasi</h5>
              
              <?php if ($order['status'] === 'Diajukan'): ?>
                <form action="<?= BASE_URL ?>/index.php?url=order/verify/<?= $order['id'] ?>" method="POST" class="mb-3">
                  <div class="mb-2">
                    <label class="form-label extra-small text-white-50">Catatan Verifikasi Operator</label>
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Catatan verifikasi...">
                  </div>
                  <div class="d-flex gap-2">
                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm w-100 font-weight-bold">
                      <i class="bi bi-check-circle me-1"></i> Setujui Order
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm w-100">
                      Tolak Order
                    </button>
                  </div>
                </form>
              <?php endif; ?>

              <a href="<?= BASE_URL ?>/index.php?url=order/assign/<?= $order['id'] ?>" class="btn btn-warning btn-lg w-100 rounded-pill text-dark fw-bold shadow-sm mb-2">
                <i class="bi bi-person-plus-fill me-2"></i> Penugasan & Jadwal Teknisi
              </a>

              <a href="<?= BASE_URL ?>/index.php?url=transaction/invoice/<?= $order['id'] ?>" class="btn btn-outline-light w-100 rounded-pill">
                <i class="bi bi-calculator me-1"></i> Kelola Tagihan Invoice
              </a>
            </div>
          <?php endif; ?>

          <div class="card border-0 bg-light p-4 rounded-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-camera-fill text-info me-1"></i> Dokumentasi Foto Ter-upload</h5>
            
            <?php if (!empty($order['photo_evidence'])): ?>
              <div class="mb-3 text-center">
                <img src="<?= BASE_URL ?>/assets/uploads/orders/<?= htmlspecialchars($order['photo_evidence']) ?>" alt="Foto Dokumentasi" class="img-fluid rounded-3 border shadow-sm max-h-250" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250?text=Foto+Dokumentasi+Perangkat';">
              </div>
            <?php else: ?>
              <div class="p-4 bg-white rounded-3 border text-center text-muted mb-3">
                <i class="bi bi-image text-muted display-4 d-block mb-1"></i>
                <div class="extra-small">Tidak ada foto dokumentasi pengajuan.</div>
              </div>
            <?php endif; ?>

            <?php if (Auth::role() === 'customer' && in_array($order['status'], ['Diajukan', 'Menunggu Verifikasi', 'Disetujui'])): ?>
              <hr class="my-3">
              <a href="<?= BASE_URL ?>/index.php?url=order/cancel/<?= $order['id'] ?>" class="btn btn-outline-danger w-100 rounded-pill" onclick="return confirm('Apakah Anda yakin ingin membatalkan tiket order ini?')">
                <i class="bi bi-x-circle me-1"></i> Batalkan Pengajuan Order
              </a>
            <?php endif; ?>

          </div>
        </div>

      </div>

      <!-- TECHNICIAN WORK REPORT CARD -->
      <?php if (!empty($report) && ($has_report ?? false)): ?>
        <div class="card border-success rounded-4 overflow-hidden mb-4 shadow-sm">
          <div class="card-header bg-success text-white p-4 border-0 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0">
              <i class="bi bi-journal-check me-2"></i> Laporan Hasil Pekerjaan Lapangan Teknisi
            </h5>
            <span class="badge bg-white text-success fw-bold">Verifikasi Lapangan</span>
          </div>

          <div class="card-body p-4 p-md-5 bg-white">
            
            <div class="row gy-4 mb-4">
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                  <span class="text-uppercase extra-small text-muted fw-bold d-block mb-1"><i class="bi bi-search text-primary me-1"></i> Diagnosa Masalah:</span>
                  <div class="fw-semibold text-dark"><?= nl2br(htmlspecialchars($report['diagnosis'])) ?></div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                  <span class="text-uppercase extra-small text-muted fw-bold d-block mb-1"><i class="bi bi-clipboard-check text-warning me-1"></i> Pemeriksaan Yang Dilakukan:</span>
                  <div class="fw-semibold text-dark"><?= nl2br(htmlspecialchars($report['inspection'])) ?></div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                  <span class="text-uppercase extra-small text-muted fw-bold d-block mb-1"><i class="bi bi-tools text-success me-1"></i> Tindakan / Perbaikan:</span>
                  <div class="fw-semibold text-dark"><?= nl2br(htmlspecialchars($report['action_taken'])) ?></div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                  <span class="text-uppercase extra-small text-muted fw-bold d-block mb-1"><i class="bi bi-cpu text-info me-1"></i> Sparepart / Komponen Terpakai:</span>
                  <div class="fw-semibold text-dark"><?= nl2br(htmlspecialchars($report['spareparts_used'])) ?></div>
                </div>
              </div>

              <div class="col-12">
                <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25">
                  <span class="text-uppercase extra-small text-success fw-bold d-block mb-1"><i class="bi bi-check-circle-fill me-1"></i> Hasil Pekerjaan & Pengujian Akhir:</span>
                  <div class="fw-bold text-dark fs-6"><?= nl2br(htmlspecialchars($report['execution_result'])) ?></div>
                </div>
              </div>
            </div>

            <!-- BEFORE & AFTER PHOTOS DISPLAY -->
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-camera-fill text-info me-2"></i> Foto Dokumentasi Pengerjaan (Sebelum & Sesudah)</h6>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border text-center">
                  <span class="badge bg-secondary mb-2 d-inline-block">Sebelum Pengerjaan (Before)</span>
                  <?php if (!empty($report['photo_before'])): ?>
                    <img src="<?= BASE_URL ?>/assets/uploads/orders/<?= htmlspecialchars($report['photo_before']) ?>" alt="Foto Before" class="img-fluid rounded-3 border shadow-sm max-h-200 d-block mx-auto" onerror="this.onerror=null; this.src='https://via.placeholder.com/350x200?text=Foto+Before+Tersimpan';">
                  <?php else: ?>
                    <div class="text-muted extra-small py-4">Foto sebelum pengerjaan terlampir di laporan.</div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border text-center">
                  <span class="badge bg-success mb-2 d-inline-block">Sesudah Pengerjaan (After)</span>
                  <?php if (!empty($report['photo_after'])): ?>
                    <img src="<?= BASE_URL ?>/assets/uploads/orders/<?= htmlspecialchars($report['photo_after']) ?>" alt="Foto After" class="img-fluid rounded-3 border shadow-sm max-h-200 d-block mx-auto" onerror="this.onerror=null; this.src='https://via.placeholder.com/350x200?text=Foto+After+Tersimpan';">
                  <?php else: ?>
                    <div class="text-muted extra-small py-4">Foto sesudah pengerjaan terlampir di laporan.</div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- OPERATOR REPORT VERIFICATION & REVISION ACTIONS -->
            <?php if (Auth::role() === 'operator' && $order['status'] === 'Selesai'): ?>
              <div class="p-4 bg-dark text-white rounded-4 border">
                <h5 class="fw-bold mb-2"><i class="bi bi-shield-check text-warning me-2"></i> Verifikasi Laporan Pekerjaan oleh Operator</h5>
                <p class="text-white-50 small mb-3">Tinjau kelengkapan laporan & foto sebelum/sesudah. Anda dapat memverifikasi laporan ini atau mengembalikannya ke teknisi untuk revisi.</p>

                <form action="<?= BASE_URL ?>/index.php?url=order/verifyReport/<?= $order['id'] ?>" method="POST">
                  <div class="mb-3">
                    <label class="form-label extra-small text-white-50">Catatan Revisi / Instruksi (Wajib Diisi Jika Mengembalikan Laporan)</label>
                    <input type="text" name="notes" class="form-control" placeholder="Contoh: Lampirkan foto sesudah pengerjaan secara lebih jelas...">
                  </div>

                  <div class="d-flex gap-2">
                    <button type="submit" name="action" value="verify" class="btn btn-success btn-lg rounded-pill fw-bold flex-grow-1">
                      <i class="bi bi-check-circle-fill me-1"></i> Verifikasi Laporan & Tutup Tiket
                    </button>
                    <button type="submit" name="action" value="revise" class="btn btn-outline-warning btn-lg rounded-pill">
                      <i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan Ke Teknisi (Revisi)
                    </button>
                  </div>
                </form>
              </div>
            <?php endif; ?>

          </div>
        </div>
      <?php endif; ?>

      <!-- CUSTOMER RATING & FEEDBACK SECTION -->
      <?php if (in_array($order['status'], ['Selesai', 'Ditutup'])): ?>
        
        <?php if (Auth::role() === 'customer' && !($has_rated ?? false)): ?>
          
          <!-- RATING FORM FOR CUSTOMER -->
          <div class="card border-warning border-opacity-50 rounded-4 overflow-hidden mb-4 shadow-sm bg-warning bg-opacity-10">
            <div class="card-header bg-warning text-dark p-4 border-0 d-flex align-items-center justify-content-between">
              <div>
                <span class="badge bg-dark text-white px-3 py-1 rounded-pill extra-small text-uppercase mb-1">
                  <i class="bi bi-star-fill text-warning me-1"></i> Penilaian Layanan Jasa
                </span>
                <h5 class="fw-bold mb-0">Beri Rating & Feedback Untuk Pekerjaan Ini</h5>
              </div>
              <span class="badge bg-white text-dark fw-bold">1 Tiket = 1 Rating</span>
            </div>

            <div class="card-body p-4 p-md-5 bg-white">
              <p class="small text-muted mb-4">
                Terima kasih telah menggunakan jasa <strong>TKJ Service Center</strong>. Pengalaman Anda sangat berarti untuk membantu kami menjaga kualitas kualitatif pengerjaan teknisi siswa.
              </p>

              <form action="<?= BASE_URL ?>/index.php?url=order/storeRating/<?= $order['id'] ?>" method="POST">
                
                <!-- Star Rating Picker -->
                <div class="mb-4 text-center p-3 bg-light rounded-4 border">
                  <label class="form-label fw-bold text-dark d-block mb-2">Pilih Rating Bintang Layanan (1-5 Bintang)</label>
                  <div class="d-inline-flex gap-2 align-items-center fs-2 cursor-pointer" id="starRatingContainer">
                    <input type="hidden" name="stars" id="selected_stars" value="5">
                    <i class="bi bi-star-fill text-warning star-item" data-value="1"></i>
                    <i class="bi bi-star-fill text-warning star-item" data-value="2"></i>
                    <i class="bi bi-star-fill text-warning star-item" data-value="3"></i>
                    <i class="bi bi-star-fill text-warning star-item" data-value="4"></i>
                    <i class="bi bi-star-fill text-warning star-item" data-value="5"></i>
                  </div>
                  <div class="fw-bold text-primary mt-2" id="starRatingLabel">5 / 5 - Sangat Memuaskan ⭐⭐⭐⭐⭐</div>
                </div>

                <!-- Komentar -->
                <div class="mb-4">
                  <label for="rating_comment" class="form-label fw-bold text-dark">
                    <i class="bi bi-chat-left-text-fill text-primary me-1"></i> Komentar & Ulasan Pengerjaan <span class="text-danger">*</span>
                  </label>
                  <textarea name="comment" id="rating_comment" class="form-control rounded-3" rows="3" placeholder="Tuliskan pendapat Anda mengenai kerapihan pengerjaan, keramahan teknisi, atau hasil perbaikan..." required></textarea>
                </div>

                <!-- Feedback Layanan -->
                <div class="mb-4">
                  <label for="service_feedback" class="form-label fw-bold text-dark">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Feedback & Saran Pemeliharaan Layanan (Opsional)
                  </label>
                  <textarea name="service_feedback" id="service_feedback" class="form-control rounded-3" rows="2" placeholder="Masukan atau saran perbaikan untuk layanan TKJ Service Center selanjutnya..."></textarea>
                </div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div class="extra-small text-muted">
                    <i class="bi bi-shield-lock-fill me-1"></i> Penilaian bersifat permanen dan hanya dapat diberikan satu kali.
                  </div>
                  <button type="submit" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow-sm text-dark">
                    <i class="bi bi-send-fill me-2"></i> Kirim Rating & Feedback
                  </button>
                </div>

              </form>
            </div>
          </div>

          <script>
          document.addEventListener('DOMContentLoaded', function() {
              const stars = document.querySelectorAll('.star-item');
              const starsInput = document.getElementById('selected_stars');
              const label = document.getElementById('starRatingLabel');

              const labels = {
                  1: '1 / 5 - Kecewa 😞',
                  2: '2 / 5 - Kurang Memuaskan 🙁',
                  3: '3 / 5 - Cukup Baik 😐',
                  4: '4 / 5 - Bagus & Memuaskan 🙂',
                  5: '5 / 5 - Sangat Memuaskan ⭐⭐⭐⭐⭐'
              };

              stars.forEach(star => {
                  star.addEventListener('click', function() {
                      const val = parseInt(this.getAttribute('data-value'));
                      starsInput.value = val;
                      label.innerText = labels[val];

                      stars.forEach((s, idx) => {
                          if (idx < val) {
                              s.classList.replace('bi-star', 'bi-star-fill');
                              s.classList.add('text-warning');
                          } else {
                              s.classList.replace('bi-star-fill', 'bi-star');
                              s.classList.remove('text-warning');
                          }
                      });
                  });
              });
          });
          </script>

        <?php elseif (!empty($rating)): ?>

          <!-- READ-ONLY RATING CARD FOR CUSTOMER / TECHNICIAN / OPERATOR / KEPSEK -->
          <div class="card border-warning rounded-4 overflow-hidden mb-4 shadow-sm">
            <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-star-fill text-warning fs-4"></i>
                <div>
                  <h5 class="fw-bold mb-0 text-white">Ulasan & Rating Pelanggan</h5>
                  <span class="extra-small text-white-50">Telah Diverifikasi Sistem</span>
                </div>
              </div>
              <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                ⭐ <?= number_format($rating['stars'], 1) ?> / 5.0
              </span>
            </div>

            <div class="card-body p-4 bg-white">
              
              <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.2rem;">
                  <?= strtoupper(substr($rating['customer_name'] ?? 'C', 0, 1)) ?>
                </div>
                <div>
                  <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($rating['customer_name'] ?? 'Customer') ?></h6>
                  <span class="extra-small text-muted">Diberikan pada <?= date('d F Y, H:i', strtotime($rating['created_at'])) ?> WIB</span>
                </div>
                <div class="ms-auto text-warning fs-5">
                  <?php for($i=1; $i<=5; $i++): ?>
                    <i class="bi bi-star<?= $i <= $rating['stars'] ? '-fill' : '' ?>"></i>
                  <?php endfor; ?>
                </div>
              </div>

              <!-- Komentar Ulasan -->
              <div class="mb-3">
                <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1">Komentar Ulasan Pelanggan:</label>
                <div class="p-3 bg-light rounded-3 border text-dark font-weight-medium">
                  <?= nl2br(htmlspecialchars($rating['user_comment'] ?? $rating['comment'])) ?>
                </div>
              </div>

              <!-- Feedback Layanan -->
              <?php if (!empty($rating['service_feedback'])): ?>
                <div class="mb-3">
                  <label class="extra-small text-uppercase text-muted fw-bold d-block mb-1 text-primary"><i class="bi bi-lightbulb-fill me-1"></i> Feedback & Masukan Layanan:</label>
                  <div class="p-3 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25 text-dark">
                    <?= nl2br(htmlspecialchars($rating['service_feedback'])) ?>
                  </div>
                </div>
              <?php endif; ?>

              <?php if (Auth::role() === 'teknisi'): ?>
                <div class="extra-small text-muted mt-2">
                  <i class="bi bi-shield-check text-success me-1"></i> <em>Teknisi tidak dapat mengubah atau menghapus ulasan customer.</em>
                </div>
              <?php endif; ?>

            </div>
          </div>

        <?php endif; ?>

      <?php endif; ?>
      <!-- END CUSTOMER RATING & FEEDBACK SECTION -->

      <!-- Audit Trail work_logs Section -->
      <hr class="my-4">
      <h5 class="fw-bold text-dark mb-3">
        <i class="bi bi-journal-text text-primary me-2"></i> Audit Trail History Log (`work_logs`)
      </h5>
      <div class="timeline-logs bg-light p-4 rounded-4 border">
        <?php if (!empty($logs)): ?>
          <div class="row gy-3">
            <?php foreach ($logs as $l): ?>
              <div class="col-md-6">
                <div class="p-3 bg-white rounded-3 border shadow-xs">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-dark extra-small text-capitalize"><?= htmlspecialchars($l['log_type']) ?></span>
                    <span class="extra-small text-muted"><?= date('d M Y H:i', strtotime($l['logged_at'] ?? $l['created_at'])) ?></span>
                  </div>
                  <div class="fw-semibold text-dark extra-small mb-1"><?= htmlspecialchars($l['user_name'] ?? 'System / Operator') ?> (<?= ucfirst($l['user_role'] ?? 'user') ?>)</div>
                  <p class="extra-small text-muted mb-0"><?= nl2br(htmlspecialchars($l['description'])) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-muted extra-small text-center py-2">Belum ada riwayat audit log.</div>
        <?php endif; ?>
      </div>

    </div>
  </div>

</div>
