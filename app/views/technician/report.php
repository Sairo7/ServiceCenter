<div class="container py-4 my-auto">
  
  <div class="mb-3">
    <a href="<?= BASE_URL ?>/index.php?url=technician/detail/<?= $order['id'] ?>" class="text-decoration-none text-secondary small">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke Rincian Tugas Pekerjaan
    </a>
  </div>

  <?php Flasher::flash(); ?>

  <div class="row justify-content-center">
    <div class="col-lg-11 col-xl-10">
      
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-dark-gradient text-white p-4 border-0">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
              <span class="badge bg-info text-dark px-3 py-1 rounded-pill extra-small fw-bold text-uppercase mb-2">
                <i class="bi bi-journal-check me-1"></i> Laporan Pekerjaan Lapangan Teknisi
              </span>
              <h4 class="fw-extrabold mb-0">Formulir & Asisten AI Laporan Hasil Pengerjaan</h4>
            </div>
            <div class="badge bg-primary fs-6 px-3 py-2 rounded-pill shadow-sm">
              Tiket: <?= htmlspecialchars($order['order_code']) ?>
            </div>
          </div>
        </div>

        <div class="card-body p-4 p-md-5">

          <!-- Order Summary Bar -->
          <div class="p-3 bg-light rounded-4 border mb-4">
            <div class="row g-2 text-dark small">
              <div class="col-md-6">
                <strong>Jenis Layanan Jasa:</strong> <?= htmlspecialchars($order['service_name']) ?>
              </div>
              <div class="col-md-6">
                <strong>Pemesan:</strong> <?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)
              </div>
              <div class="col-12">
                <strong>Alamat Pekerjaan:</strong> <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($order['service_address']) ?>
              </div>
            </div>
          </div>

          <!-- AI Report Generator Hero Card -->
          <div class="card border-0 bg-gradient-dark text-white rounded-4 p-4 mb-4 shadow-sm position-relative overflow-hidden style-ai-hero">
            <div class="position-relative z-1">
              <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.4rem;">
                  <i class="bi bi-magic"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-0">✨ Asisten AI Pembuat Laporan Pekerjaan Lapangan</h5>
                  <p class="small text-white-50 mb-0">Masukkan catatan ringkas teknisi (Masalah, Diagnosa, Tindakan, Hasil, Sparepart, Catatan). AI akan merangkum menjadi <strong>Laporan Resmi 7 Bagian Standar</strong> tanpa mengubah fakta pengerjaan.</p>
                </div>
              </div>

              <!-- Quick Helper Bar -->
              <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top border-white border-opacity-10">
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-xs text-primary" id="btnScrollAiForm">
                  <i class="bi bi-pencil-fill me-1"></i> Input Catatan Teknisi
                </button>
                <a href="<?= BASE_URL ?>/index.php?url=technician/troubleshoot/<?= $order['id'] ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold">
                  <i class="bi bi-cpu-fill me-1"></i> AI Troubleshooting Assistant
                </a>
              </div>
            </div>
          </div>

          <form action="<?= BASE_URL ?>/index.php?url=technician/storeReport/<?= $order['id'] ?>" method="POST" enctype="multipart/form-data" id="mainReportForm">
            <input type="hidden" name="ai_report_full" id="ai_report_full" value="<?= htmlspecialchars($report['ai_report_full'] ?? '') ?>">
            <input type="hidden" name="recommendation" id="recommendation" value="<?= htmlspecialchars($report['recommendation'] ?? '') ?>">

            <!-- TAB / SECTION SYSTEM -->
            <ul class="nav nav-pills nav-fill bg-light p-1 rounded-3 mb-4" id="reportTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold py-2 rounded-3" id="tab-input-tab" data-bs-toggle="tab" data-bs-target="#tab-input" type="button" role="tab">
                  <i class="bi bi-input-cursor-text me-1"></i> 1. Input Catatan Teknisi
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold py-2 rounded-3 position-relative" id="tab-ai-tab" data-bs-toggle="tab" data-bs-target="#tab-ai" type="button" role="tab">
                  <i class="bi bi-stars me-1 text-primary"></i> 2. Edit Hasil Laporan AI (7 Bagian)
                  <span class="badge bg-primary text-white rounded-pill ms-1 d-none" id="badgeAiGenerated">Siap</span>
                </button>
              </li>
            </ul>

            <div class="tab-content" id="reportTabsContent">

              <!-- TAB 1: INPUT CATATAN TEKNISI -->
              <div class="tab-pane fade show active" id="tab-input" role="tabpanel">
                
                <div class="alert alert-info border-0 rounded-3 d-flex align-items-center gap-3 mb-4">
                  <i class="bi bi-info-circle-fill fs-4 flex-shrink-0"></i>
                  <div class="small">
                    <strong>Petunjuk:</strong> Tuliskan poin-poin singkat pengerjaan Anda di bawah ini. AI tidak akan menambahkan tindakan yang tidak ditulis atau mengarang sparepart yang tidak dipakai.
                  </div>
                </div>

                <!-- 1. Masalah -->
                <div class="mb-4">
                  <label for="input_masalah" class="form-label fw-bold text-dark">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> A. Masalah / Keluhan Lapangan <span class="text-danger">*</span>
                  </label>
                  <textarea id="input_masalah" class="form-control rounded-3" rows="2" placeholder="Contoh: Kamera CCTV 2 & 3 mati total, indikator DVR berkedip merah..."><?= htmlspecialchars($order['problem_description'] ?? '') ?></textarea>
                  <div class="extra-small text-muted mt-1">Keluhan awal yang ditemui di lokasi pengerjaan.</div>
                </div>

                <!-- 2. Diagnosa -->
                <div class="mb-4">
                  <label for="diagnosis" class="form-label fw-bold text-dark">
                    <i class="bi bi-search text-primary me-1"></i> B. Diagnosa / Pemeriksaan Penyebab Kerusakan <span class="text-danger">*</span>
                  </label>
                  <textarea name="diagnosis" id="diagnosis" class="form-control rounded-3" rows="3" placeholder="Contoh: Power supply central 12V drop menjadi 8V dan 2 unit konektor BNC teroksidasi kendor..." required><?= htmlspecialchars($report['diagnosis'] ?? '') ?></textarea>
                </div>

                <!-- 3. Pemeriksaan Opsional / Tambahan -->
                <div class="mb-4">
                  <label for="inspection" class="form-label fw-bold text-dark">
                    <i class="bi bi-clipboard-check text-info me-1"></i> C. Langkah Pengujian & Pemeriksaan Teknis <span class="text-danger">*</span>
                  </label>
                  <textarea name="inspection" id="inspection" class="form-control rounded-3" rows="2" placeholder="Contoh: Pengujian voltase dengan multimeter dan tes kontinuitas kabel coaxial BNC..." required><?= htmlspecialchars($report['inspection'] ?? '') ?></textarea>
                </div>

                <!-- 4. Tindakan -->
                <div class="mb-4">
                  <label for="action_taken" class="form-label fw-bold text-dark">
                    <i class="bi bi-tools text-success me-1"></i> D. Tindakan / Perbaikan Yang Dieksekusi <span class="text-danger">*</span>
                  </label>
                  <textarea name="action_taken" id="action_taken" class="form-control rounded-3" rows="3" placeholder="Contoh: Mengganti 2 unit BNC jack baru, penyambungan ulang isolasi, & perapihan jalur kabel garasi..." required><?= htmlspecialchars($report['action_taken'] ?? '') ?></textarea>
                </div>

                <!-- 5. Sparepart -->
                <div class="mb-4">
                  <label for="spareparts_used" class="form-label fw-bold text-dark">
                    <i class="bi bi-cpu text-purple me-1"></i> E. Sparepart / Material Yang Digunakan
                  </label>
                  <textarea name="spareparts_used" id="spareparts_used" class="form-control rounded-3" rows="2" placeholder="Contoh: 2 pcs Konektor BNC Cat6, 5 meter Kabel Coaxial, 1 Roll Isolasi 3M. (Kosongkan/tulis 'Tidak ada' jika tanpa part)"><?= htmlspecialchars($report['spareparts_used'] ?? '') ?></textarea>
                  <div class="extra-small text-muted mt-1">Jika tidak ada sparepart terpakai, AI akan secara jujur mencantumkan tanpa mengarang.</div>
                </div>

                <!-- 6. Hasil -->
                <div class="mb-4">
                  <label for="execution_result" class="form-label fw-bold text-dark">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> F. Hasil Pekerjaan & Pengujian Terakhir <span class="text-danger">*</span>
                  </label>
                  <textarea name="execution_result" id="execution_result" class="form-control rounded-3" rows="3" placeholder="Contoh: Seluruh 4 unit CCTV kembali menyala jernih 1080p, remote monitoring smartphone aktif normal..." required><?= htmlspecialchars($report['execution_result'] ?? '') ?></textarea>
                </div>

                <!-- 7. Catatan -->
                <div class="mb-4">
                  <label for="notes" class="form-label fw-bold text-dark">
                    <i class="bi bi-journal-text me-1"></i> G. Catatan Tambahan / Saran Teknisi
                  </label>
                  <textarea name="notes" id="notes" class="form-control rounded-3" rows="2" placeholder="Contoh: Disarankan tidak menumpuk barang berat di dekat box adaptor CCTV..."><?= htmlspecialchars($report['notes'] ?? '') ?></textarea>
                </div>

                <!-- GENERATE BUTTON BAR -->
                <div class="p-3 bg-light rounded-4 border d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                  <div>
                    <h6 class="fw-bold mb-0 text-dark">Siap Membuat Laporan Profesional?</h6>
                    <span class="extra-small text-muted">Klik tombol di kanan untuk memproses input ke format 7 bagian AI resmi.</span>
                  </div>
                  <button type="button" class="btn btn-primary-gradient btn-lg text-white rounded-pill px-4 fw-bold shadow-sm" id="btnGenerateAiReport">
                    <i class="bi bi-magic me-2"></i> ✨ Generate Laporan AI (7 Bagian)
                  </button>
                </div>

              </div>

              <!-- TAB 2: EDIT HASIL LAPORAN AI (7 BAGIAN STANDAR) -->
              <div class="tab-pane fade" id="tab-ai" role="tabpanel">

                <div class="alert alert-success border-0 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill fs-4 text-success flex-shrink-0"></i>
                    <div class="small">
                      <strong>Laporan Profesional 7 Bagian Berhasil Dibuat AI.</strong> Anda dapat mengedit teks setiap bagian secara langsung di bawah ini.
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" id="btnPreviewReportModal">
                    <i class="bi bi-eye-fill me-1"></i> 👁️ Preview Dokumen Resmi
                  </button>
                </div>

                <!-- 7 SECTIONS EDITABLE CARDS -->
                <div class="row g-3 mb-4">

                  <!-- 1. Identitas Pekerjaan -->
                  <div class="col-12">
                    <div class="card border rounded-3 p-3 bg-light">
                      <label class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-primary me-2">1</span> 1. Identitas Pekerjaan
                      </label>
                      <textarea id="ai_sec_identitas" class="form-control rounded-3 bg-white font-monospace extra-small" rows="4" readonly></textarea>
                      <div class="extra-small text-muted mt-1"><i class="bi bi-lock-fill me-1"></i> Otomatis disesuaikan dengan data tiket pelanggan & teknisi.</div>
                    </div>
                  </div>

                  <!-- 2. Permasalahan -->
                  <div class="col-md-6">
                    <div class="card border rounded-3 p-3 bg-white shadow-xs">
                      <label for="ai_sec_permasalahan" class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-warning text-dark me-2">2</span> 2. Permasalahan
                      </label>
                      <textarea id="ai_sec_permasalahan" class="form-control rounded-3" rows="4"></textarea>
                    </div>
                  </div>

                  <!-- 3. Hasil Pemeriksaan -->
                  <div class="col-md-6">
                    <div class="card border rounded-3 p-3 bg-white shadow-xs">
                      <label for="ai_sec_pemeriksaan" class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-info text-dark me-2">3</span> 3. Hasil Pemeriksaan & Diagnosa
                      </label>
                      <textarea id="ai_sec_pemeriksaan" class="form-control rounded-3" rows="4"></textarea>
                    </div>
                  </div>

                  <!-- 4. Tindakan yang Dilakukan -->
                  <div class="col-md-6">
                    <div class="card border rounded-3 p-3 bg-white shadow-xs">
                      <label for="ai_sec_tindakan" class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-success me-2">4</span> 4. Tindakan yang Dilakukan
                      </label>
                      <textarea id="ai_sec_tindakan" class="form-control rounded-3" rows="4"></textarea>
                    </div>
                  </div>

                  <!-- 5. Sparepart / Material -->
                  <div class="col-md-6">
                    <div class="card border rounded-3 p-3 bg-white shadow-xs">
                      <label for="ai_sec_sparepart" class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-secondary me-2">5</span> 5. Sparepart / Material
                      </label>
                      <textarea id="ai_sec_sparepart" class="form-control rounded-3" rows="4"></textarea>
                    </div>
                  </div>

                  <!-- 6. Hasil Akhir -->
                  <div class="col-md-6">
                    <div class="card border rounded-3 p-3 bg-white shadow-xs">
                      <label for="ai_sec_hasil" class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-success me-2">6</span> 6. Hasil Akhir & Pengujian
                      </label>
                      <textarea id="ai_sec_hasil" class="form-control rounded-3" rows="4"></textarea>
                    </div>
                  </div>

                  <!-- 7. Rekomendasi -->
                  <div class="col-md-6">
                    <div class="card border rounded-3 p-3 bg-white shadow-xs">
                      <label for="ai_sec_rekomendasi" class="form-label fw-bold text-dark mb-2">
                        <span class="badge bg-primary me-2">7</span> 7. Rekomendasi & Catatan
                      </label>
                      <textarea id="ai_sec_rekomendasi" class="form-control rounded-3" rows="4"></textarea>
                    </div>
                  </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mb-4">
                  <button type="button" class="btn btn-outline-primary rounded-pill px-4 fw-semibold" id="btnSyncFormFields">
                    <i class="bi bi-arrow-repeat me-1"></i> Sinkronkan Edit AI ke Form Utama
                  </button>
                  <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnOpenModalFromEdit">
                    <i class="bi bi-file-earmark-text me-1"></i> 👁️ Preview Dokumen Laporan
                  </button>
                </div>

              </div>

            </div>

            <!-- FOTO EVIDENCES SECTION -->
            <div class="p-4 bg-light rounded-4 border mb-4">
              <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-camera-fill text-info me-2"></i> Dokumentasi Foto Sebelum & Sesudah Pengerjaan
              </h6>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="photo_before" class="form-label fw-semibold text-dark small">Foto Sebelum Pengerjaan (Before)</label>
                  <input type="file" name="photo_before" id="photo_before" class="form-control" accept="image/jpeg,image/png,image/webp">
                  <?php if (!empty($report['photo_before'])): ?>
                    <div class="extra-small text-success mt-1"><i class="bi bi-image me-1"></i> Foto tersimpan: <?= htmlspecialchars($report['photo_before']) ?></div>
                  <?php else: ?>
                    <div class="extra-small text-muted mt-1">Foto kondisi awal kerusakan sebelum diperbaiki.</div>
                  <?php endif; ?>
                </div>

                <div class="col-md-6">
                  <label for="photo_after" class="form-label fw-semibold text-dark small">Foto Sesudah Pengerjaan (After)</label>
                  <input type="file" name="photo_after" id="photo_after" class="form-control" accept="image/jpeg,image/png,image/webp">
                  <?php if (!empty($report['photo_after'])): ?>
                    <div class="extra-small text-success mt-1"><i class="bi bi-image me-1"></i> Foto tersimpan: <?= htmlspecialchars($report['photo_after']) ?></div>
                  <?php else: ?>
                    <div class="extra-small text-muted mt-1">Foto kondisi hasil perbaikan yang telah rapi.</div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- COMPLETION SWITCH -->
            <div class="form-check form-switch p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 mb-4">
              <input class="form-check-input ms-0 me-2" type="checkbox" name="mark_completed" id="mark_completed" checked>
              <label class="form-check-label fw-bold text-dark" for="mark_completed">
                Tandai Pekerjaan Ini Selesai (Ubah Status Order Menjadi "Selesai")
              </label>
            </div>

            <!-- FORM ACTIONS -->
            <div class="d-flex gap-2">
              <a href="<?= BASE_URL ?>/index.php?url=technician/detail/<?= $order['id'] ?>" class="btn btn-outline-secondary rounded-3 px-4">
                Batal
              </a>
              <button type="button" class="btn btn-outline-primary btn-lg rounded-3 fw-bold px-4" id="btnTriggerPreviewModal">
                <i class="bi bi-eye me-2"></i> Preview Laporan
              </button>
              <button type="submit" class="btn btn-primary-gradient btn-lg text-white rounded-3 fw-bold shadow-sm flex-grow-1">
                <i class="bi bi-send-check-fill me-2"></i> Kirim Laporan Hasil Pekerjaan
              </button>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- OFFICIAL REPORT DOCUMENT PREVIEW MODAL -->
<div class="modal fade" id="previewReportModal" tabindex="-1" aria-labelledby="previewReportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      
      <div class="modal-header bg-dark text-white p-3 border-0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill text-warning fs-4"></i>
          <div>
            <h6 class="modal-title fw-bold text-white mb-0" id="previewReportModalLabel">Dokumen Resmi Laporan Hasil Pekerjaan Lapangan</h6>
            <span class="extra-small text-white-50">Standard Format 7 Bagian - TKJ Service Center</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4 p-md-5 bg-white position-relative" id="printableReportArea">
        
        <!-- DOCUMENT LETTERHEAD HEADER -->
        <div class="text-center pb-3 mb-4 border-bottom border-2 border-dark style-doc-header">
          <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 1.25rem;">
              TKJ
            </div>
            <div class="text-start">
              <h4 class="fw-extrabold mb-0 text-uppercase tracking-wide text-dark">TKJ SERVICE CENTER</h4>
              <p class="extra-small text-muted mb-0">Unit Produksi & TEFA Teknik Komputer dan Jaringan SMKN</p>
            </div>
          </div>
          <div class="badge bg-dark text-white px-3 py-1 rounded-pill extra-small text-uppercase mt-2">
            LAPORAN RESMI HASIL PEKERJAAN LAPANGAN
          </div>
        </div>

        <!-- 7 SECTIONS RENDERED -->
        <div class="doc-body small text-dark">

          <!-- 1. Identitas -->
          <div class="mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-1"><i class="bi bi-journal-text me-2"></i> 1. Identitas Pekerjaan</h6>
            <div class="p-3 bg-light rounded-3 font-monospace extra-small lh-base" id="pv_sec_identitas">
              - Loading Identitas -
            </div>
          </div>

          <!-- 2. Permasalahan -->
          <div class="mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-1"><i class="bi bi-exclamation-octagon me-2"></i> 2. Permasalahan</h6>
            <p class="mb-0 text-justify" id="pv_sec_permasalahan">-</p>
          </div>

          <!-- 3. Hasil Pemeriksaan -->
          <div class="mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-1"><i class="bi bi-search me-2"></i> 3. Hasil Pemeriksaan</h6>
            <p class="mb-0 text-justify" id="pv_sec_pemeriksaan">-</p>
          </div>

          <!-- 4. Tindakan yang Dilakukan -->
          <div class="mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-1"><i class="bi bi-tools me-2"></i> 4. Tindakan yang Dilakukan</h6>
            <p class="mb-0 text-justify" id="pv_sec_tindakan">-</p>
          </div>

          <!-- 5. Sparepart/Material -->
          <div class="mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-1"><i class="bi bi-cpu me-2"></i> 5. Sparepart / Material</h6>
            <p class="mb-0 text-justify" id="pv_sec_sparepart">-</p>
          </div>

          <!-- 6. Hasil Akhir -->
          <div class="mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-1"><i class="bi bi-check-circle me-2"></i> 6. Hasil Akhir</h6>
            <p class="mb-0 text-justify" id="pv_sec_hasil">-</p>
          </div>

          <!-- 7. Rekomendasi -->
          <div class="mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-1"><i class="bi bi-lightbulb me-2"></i> 7. Rekomendasi</h6>
            <p class="mb-0 text-justify" id="pv_sec_rekomendasi">-</p>
          </div>

          <!-- SIGNATURE BLOCK -->
          <div class="row pt-4 mt-4 border-top text-center extra-small">
            <div class="col-6">
              <p class="mb-5 text-muted">Pelanggan / Penerima Layanan,</p>
              <strong class="text-dark border-bottom border-secondary pb-1 d-inline-block"><?= htmlspecialchars($order['customer_name']) ?></strong>
            </div>
            <div class="col-6">
              <p class="mb-5 text-muted">Teknisi Siswa Pelaksana,</p>
              <strong class="text-dark border-bottom border-secondary pb-1 d-inline-block"><?= htmlspecialchars($user['name'] ?? 'Teknisi TKJ') ?></strong>
            </div>
          </div>

        </div>

      </div>

      <div class="modal-footer bg-light border-0 p-3">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-bold" onclick="window.print()">
          <i class="bi bi-printer me-1"></i> Cetak Dokumen
        </button>
        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="btnSubmitFromModal">
          <i class="bi bi-check-circle-fill me-1"></i> Simpan Laporan
        </button>
      </div>

    </div>
  </div>
</div>

<style>
.style-ai-hero {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
}
.style-doc-header {
  letter-spacing: 0.5px;
}
@media print {
  body * {
    visibility: hidden;
  }
  #printableReportArea, #printableReportArea * {
    visibility: visible;
  }
  #printableReportArea {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = <?= intval($order['id']) ?>;
    const baseUrl = '<?= BASE_URL ?>';

    const btnGenerate = document.getElementById('btnGenerateAiReport');
    const tabAiButton = document.getElementById('tab-ai-tab');
    const badgeAi = document.getElementById('badgeAiGenerated');
    const modalElem = new bootstrap.Modal(document.getElementById('previewReportModal'));

    // Input fields
    const inMasalah   = document.getElementById('input_masalah');
    const inDiagnosis = document.getElementById('diagnosis');
    const inInspect   = document.getElementById('inspection');
    const inAction    = document.getElementById('action_taken');
    const inSparepart = document.getElementById('spareparts_used');
    const inHasil     = document.getElementById('execution_result');
    const inNotes     = document.getElementById('notes');

    // AI Edit Section Fields
    const aiIdentitas   = document.getElementById('ai_sec_identitas');
    const aiPermasalahan= document.getElementById('ai_sec_permasalahan');
    const aiPemeriksaan = document.getElementById('ai_sec_pemeriksaan');
    const aiTindakan    = document.getElementById('ai_sec_tindakan');
    const aiSparepart   = document.getElementById('ai_sec_sparepart');
    const aiHasil       = document.getElementById('ai_sec_hasil');
    const aiRekomendasi = document.getElementById('ai_sec_rekomendasi');

    // Hidden Inputs
    const hiddenAiFull = document.getElementById('ai_report_full');
    const hiddenAiRec  = document.getElementById('recommendation');

    // Scroll helper
    document.getElementById('btnScrollAiForm')?.addEventListener('click', function() {
        inMasalah.focus();
        inMasalah.scrollIntoView({ behavior: 'smooth' });
    });

    // GENERATE AI REPORT AJAX ACTION
    btnGenerate?.addEventListener('click', function() {
        const masalahVal   = inMasalah.value.trim();
        const diagVal      = inDiagnosis.value.trim();
        const actVal       = inAction.value.trim();
        const hasilVal     = inHasil.value.trim();
        const sparepartVal = inSparepart.value.trim();
        const notesVal     = inNotes.value.trim();

        if (!diagVal && !actVal && !hasilVal && !masalahVal) {
            alert('Harap isi setidaknya Masalah, Diagnosa, Tindakan, atau Hasil Pekerjaan terlebih dahulu.');
            inDiagnosis.focus();
            return;
        }

        const originalText = btnGenerate.innerHTML;
        btnGenerate.disabled = true;
        btnGenerate.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> AI Sedang Menyusun Laporan 7 Bagian...';

        fetch(baseUrl + '/index.php?url=technician/ai_generate_report_api', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                order_id: orderId,
                masalah: masalahVal,
                diagnosa: diagVal,
                tindakan: actVal,
                hasil: hasilVal,
                sparepart: sparepartVal,
                catatan: notesVal
            })
        })
        .then(response => response.json())
        .then(data => {
            btnGenerate.disabled = false;
            btnGenerate.innerHTML = originalText;

            if (data.success && data.sections) {
                const s = data.sections;
                aiIdentitas.value    = s.identitas || '';
                aiPermasalahan.value = s.permasalahan || '';
                aiPemeriksaan.value  = s.hasil_pemeriksaan || '';
                aiTindakan.value     = s.tindakan_dilakukan || '';
                aiSparepart.value    = s.sparepart_material || '';
                aiHasil.value        = s.hasil_akhir || '';
                aiRekomendasi.value  = s.rekomendasi || '';

                hiddenAiFull.value = data.full_report || '';
                hiddenAiRec.value  = s.rekomendasi || '';

                // Show badge & switch to tab AI
                badgeAi.classList.remove('d-none');
                const triggerTab = new bootstrap.Tab(tabAiButton);
                triggerTab.show();

                alert('✨ Laporan Pekerjaan Resmi 7 Bagian Berhasil Dibuat oleh AI!\nSilakan tinjau dan sesuaikan teks di Tab 2 jika diperlukan.');
            } else {
                alert('Gagal menghasilkan laporan: ' + (data.error || 'Terjadi kesalahan sistem.'));
            }
        })
        .catch(err => {
            btnGenerate.disabled = false;
            btnGenerate.innerHTML = originalText;
            console.error(err);
            alert('Terjadi kesalahan koneksi saat memanggil AI Service.');
        });
    });

    // SYNC EDITED AI FIELDS BACK TO FORM & HIDDEN INPUTS
    function syncFields() {
        if (aiPemeriksaan.value) inDiagnosis.value = aiPemeriksaan.value;
        if (aiTindakan.value)    inAction.value    = aiTindakan.value;
        if (aiSparepart.value)   inSparepart.value = aiSparepart.value;
        if (aiHasil.value)       inHasil.value     = aiHasil.value;
        if (aiRekomendasi.value) {
            inNotes.value = aiRekomendasi.value;
            hiddenAiRec.value = aiRekomendasi.value;
        }

        // Rebuild full string
        hiddenAiFull.value = "=== LAPORAN HASIL PEKERJAAN RESMI ===\n" +
            "TKJ SERVICE CENTER - LAPANGAN\n\n" +
            "1. IDENTITAS PEKERJAAN\n" + aiIdentitas.value + "\n\n" +
            "2. PERMASALAHAN\n" + aiPermasalahan.value + "\n\n" +
            "3. HASIL PEMERIKSAAN\n" + aiPemeriksaan.value + "\n\n" +
            "4. TINDAKAN YANG DILAKUKAN\n" + aiTindakan.value + "\n\n" +
            "5. SPAREPART / MATERIAL\n" + aiSparepart.value + "\n\n" +
            "6. HASIL AKHIR\n" + aiHasil.value + "\n\n" +
            "7. REKOMENDASI\n" + aiRekomendasi.value;
    }

    document.getElementById('btnSyncFormFields')?.addEventListener('click', function() {
        syncFields();
        alert('Teks hasil edit AI berhasil disinkronkan ke formulir utama.');
    });

    // PREVIEW MODAL RENDERING
    function updatePreviewModal() {
        syncFields();

        document.getElementById('pv_sec_identitas').innerText    = aiIdentitas.value || `Kode Tiket: Tiket <?= htmlspecialchars($order['order_code']) ?>\nLayanan: <?= htmlspecialchars($order['service_name']) ?>\nPelanggan: <?= htmlspecialchars($order['customer_name']) ?>`;
        document.getElementById('pv_sec_permasalahan').innerText = aiPermasalahan.value || inMasalah.value || 'Pemeriksaan kendala lapangan.';
        document.getElementById('pv_sec_pemeriksaan').innerText  = aiPemeriksaan.value || inDiagnosis.value || '-';
        document.getElementById('pv_sec_tindakan').innerText     = aiTindakan.value || inAction.value || '-';
        document.getElementById('pv_sec_sparepart').innerText    = aiSparepart.value || inSparepart.value || 'Tidak menggunakan sparepart tambahan.';
        document.getElementById('pv_sec_hasil').innerText        = aiHasil.value || inHasil.value || '-';
        document.getElementById('pv_sec_rekomendasi').innerText  = aiRekomendasi.value || inNotes.value || 'Disarankan pemeliharaan rutin.';
    }

    document.getElementById('btnPreviewReportModal')?.addEventListener('click', function() {
        updatePreviewModal();
        modalElem.show();
    });

    document.getElementById('btnOpenModalFromEdit')?.addEventListener('click', function() {
        updatePreviewModal();
        modalElem.show();
    });

    document.getElementById('btnTriggerPreviewModal')?.addEventListener('click', function() {
        updatePreviewModal();
        modalElem.show();
    });

    document.getElementById('btnSubmitFromModal')?.addEventListener('click', function() {
        syncFields();
        modalElem.hide();
        document.getElementById('mainReportForm').submit();
    });
});
</script>
