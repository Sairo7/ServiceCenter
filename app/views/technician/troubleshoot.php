<div class="container-fluid px-4 py-4">
  <div class="row gy-4">
    
    <!-- Sidebar Area -->
    <div class="col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../templates/sidebar.php'; ?>
    </div>

    <!-- Main Content Workspace -->
    <div class="col-lg-9 col-xl-10">
      
      <?php Flasher::flash(); ?>

      <!-- Header Banner -->
      <div class="card border-0 bg-dark-gradient text-white rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-4 p-md-5 position-relative">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary-gradient text-white px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
              <i class="bi bi-cpu-fill me-1"></i> Asisten Diagnostik Lapangan
            </span>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
              <i class="bi bi-shield-check me-1"></i> Khusus Teknisi TKJ
            </span>
            <?php if (!empty($order)): ?>
              <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-ticket-perforated me-1"></i> Tiket: <?= htmlspecialchars($order['order_code']) ?>
              </span>
            <?php endif; ?>
          </div>
          
          <h2 class="display-6 fw-extrabold mb-1">AI Technical Troubleshooting Assistant</h2>
          <p class="text-light opacity-75 mb-0 fs-6" style="max-width: 800px;">
            Masukkan spesifikasi perangkat, gejala, kondisi fisik, dan hasil tes awal. AI akan memberikan checklist & rekomendasi langkah troubleshooting umum yang aman bagi Teknisi Siswa.
          </p>
        </div>
      </div>

      <!-- Main Layout: Form (Left) & AI Output (Right) -->
      <div class="row g-4">
        
        <!-- Left Side: Form Input Teknisi -->
        <div class="col-lg-5 col-xl-4">
          <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
              <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-sliders text-primary me-2"></i> Input Diagnosa Lapangan
              </h5>
              <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill extra-small" id="btnClearForm">
                <i class="bi bi-x-circle me-1"></i> Reset Form
              </button>
            </div>

            <div class="card-body p-4">
              
              <!-- Quick Sample Presets -->
              <div class="mb-3">
                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2">
                  <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Contoh Kasus Cepat (Klik untuk Muat)
                </label>
                <div class="d-flex flex-wrap gap-1">
                  <button type="button" class="btn btn-outline-primary btn-xs rounded-pill preset-btn" data-preset="network">
                    <i class="bi bi-ethernet me-1"></i> PC No LAN/Network
                  </button>
                  <button type="button" class="btn btn-outline-info btn-xs rounded-pill preset-btn" data-preset="mikrotik">
                    <i class="bi bi-router me-1"></i> MikroTik RTO/Lemot
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-xs rounded-pill preset-btn" data-preset="cctv">
                    <i class="bi bi-camera-video me-1"></i> CCTV No Video
                  </button>
                  <button type="button" class="btn btn-outline-warning btn-xs rounded-pill preset-btn" data-preset="laptop">
                    <i class="bi bi-laptop me-1"></i> Laptop Overheat
                  </button>
                </div>
              </div>

              <form id="troubleshootForm">
                
                <!-- 1. Jenis Perangkat -->
                <div class="mb-3">
                  <label for="device_type" class="form-label fw-semibold text-dark small">
                    1. Jenis Perangkat <span class="text-danger">*</span>
                  </label>
                  <select name="device_type" id="device_type" class="form-select rounded-3 shadow-xs" required>
                    <option value="">-- Pilih Jenis Perangkat --</option>
                    <option value="PC Desktop / Computer">PC Desktop / Komputer Lab</option>
                    <option value="Laptop / Notebook">Laptop / Notebook</option>
                    <option value="Router MikroTik">Router MikroTik / Firewall</option>
                    <option value="Switch / Hub Jaringan">Switch / Hub / Managed Switch</option>
                    <option value="Access Point Wi-Fi">Access Point / Wi-Fi Hotspot</option>
                    <option value="CCTV Analog / IP Cam & DVR/NVR">CCTV Camera / DVR / NVR</option>
                    <option value="Kabel LAN UTP/STP & Network Infrastructure">Kabel LAN UTP / Patch Panel / Walljack</option>
                    <option value="Printer / Scanner">Printer Inkjet / Laserjet / Thermal</option>
                    <option value="Server / NAS Storage">Server / Storage NAS / UPS</option>
                    <option value="Perangkat IT Lainnya">Perangkat IT Lainnya...</option>
                  </select>
                </div>

                <!-- 2. Gejala Masalah -->
                <div class="mb-3">
                  <label for="symptom" class="form-label fw-semibold text-dark small">
                    2. Gejala Masalah Utama <span class="text-danger">*</span>
                  </label>
                  <textarea name="symptom" id="symptom" class="form-control rounded-3 shadow-xs" rows="3" placeholder="Contoh: PC menyala tetapi tidak dapat terhubung ke jaringan lokal atau internet..." required><?= !empty($order) ? htmlspecialchars($order['problem_description']) : '' ?></textarea>
                </div>

                <!-- 3. Kondisi Fisik Perangkat -->
                <div class="mb-3">
                  <label for="device_condition" class="form-label fw-semibold text-dark small">
                    3. Kondisi Perangkat (Fisik & Indikator)
                  </label>
                  <textarea name="device_condition" id="device_condition" class="form-control rounded-3 shadow-xs" rows="2" placeholder="Contoh: Bodi PC bersih, kipas PSU berputar, namun lampu indikator link LED di port RJ45 mati total..."></textarea>
                </div>

                <!-- 4. Hasil Pemeriksaan Awal -->
                <div class="mb-4">
                  <label for="initial_check" class="form-label fw-semibold text-dark small">
                    4. Hasil Pemeriksaan Awal / Uji Awal
                  </label>
                  <textarea name="initial_check" id="initial_check" class="form-control rounded-3 shadow-xs" rows="2" placeholder="Contoh: Ping 127.0.0.1 success, ping gateway 192.168.1.1 RTO, IP DHCP didapat 169.254.12.89..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary-gradient w-100 text-white rounded-pill py-2 fw-bold shadow-sm" id="btnAnalyze">
                  <i class="bi bi-magic me-2"></i> Analisis dengan AI Assistant
                </button>

              </form>

            </div>
          </div>
        </div>

        <!-- Right Side: AI Output Workspace -->
        <div class="col-lg-7 col-xl-8">
          
          <!-- Empty State (Before Submit) -->
          <div id="aiOutputEmpty" class="card border-0 shadow-sm rounded-4 bg-white text-center py-5 px-4">
            <div class="my-4">
              <div class="brand-icon mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 80px; height: 80px;">
                <i class="bi bi-cpu fs-1"></i>
              </div>
              <h4 class="fw-bold text-dark mb-2">Siap Menganalisis Kendala Teknis Lapangan</h4>
              <p class="text-muted small mx-auto" style="max-width: 500px;">
                Isi form diagnostik di sebelah kiri atau pilih salah satu <strong>Contoh Kasus Cepat</strong> di atas. AI Asisten Teknisi akan menyusun checklist dan rekomendasi langkah troubleshooting yang aman.
              </p>
            </div>
          </div>

          <!-- Loading Spinner State -->
          <div id="aiOutputLoading" class="card border-0 shadow-sm rounded-4 bg-white text-center py-5 px-4 d-none">
            <div class="my-5">
              <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
              </div>
              <h5 class="fw-bold text-dark mb-1">AI Assistant Sedang Menganalisis...</h5>
              <p class="text-muted extra-small mb-0">Mengkorelasi gejala teknis, pola kelistrikan, & langkah isolasi standar TKJ.</p>
            </div>
          </div>

          <!-- Active AI Results Output Container -->
          <div id="aiOutputResult" class="d-none">
            
            <!-- Disclaimer Banner -->
            <div class="card border-warning border-opacity-50 bg-warning bg-opacity-10 rounded-4 p-3 mb-4">
              <div class="d-flex align-items-start gap-3">
                <div class="text-warning fs-3 leading-none">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-warning text-dark font-weight-bold extra-small text-uppercase">Prinsip Penggunaan AI</span>
                    <span class="text-muted extra-small">AI HANYA ALAT BANTU NAVIGASI TEKNIS</span>
                  </div>
                  <p class="extra-small text-dark mb-0 fw-medium" id="aiDisclaimerText">
                    Diagnosa bersifat indikatif dan tidak 100% pasti. Teknisi tetap mengambil keputusan akhir di lapangan.
                  </p>
                </div>
              </div>
            </div>

            <!-- Action Bar Button -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
              <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Analisis Terstruktur Siap
                  </span>
                  <span class="text-muted extra-small">Gunakan tombol di kanan untuk menyimpan/mengedit catatan.</span>
                </div>

                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" id="btnCopyRaw">
                    <i class="bi bi-clipboard me-1"></i> Salin Teks
                  </button>
                  <button type="button" class="btn btn-success btn-sm text-white rounded-pill fw-bold shadow-sm" id="btnUseAiNotes">
                    <i class="bi bi-journal-plus me-1"></i> Gunakan Hasil AI sebagai Catatan Pemeriksaan
                  </button>
                </div>
              </div>
            </div>

            <!-- 1. Kemungkinan Penyebab (Possible Causes) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
              <div class="card-header bg-primary bg-opacity-10 border-0 py-3 px-4">
                <h5 class="fw-bold text-primary mb-0">
                  <i class="bi bi-search me-2"></i> 1. Kemungkinan Penyebab Utama (Indikatif)
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3" id="possibleCausesContainer">
                  <!-- Injected via JS -->
                </div>
              </div>
            </div>

            <!-- 2. Checklist Pemeriksaan (Interactive Inspection Checklist) -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
              <div class="card-header bg-warning bg-opacity-10 border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0">
                  <i class="bi bi-check2-square text-warning me-2"></i> 2. Checklist Pemeriksaan & Pengujian Lapangan
                </h5>
                <span class="extra-small text-muted"><i class="bi bi-info-circle me-1"></i> Centang saat melakukan pengujian</span>
              </div>
              <div class="card-body p-4">
                <ul class="list-group list-group-flush border-0" id="inspectionChecklistContainer">
                  <!-- Injected via JS -->
                </ul>
              </div>
            </div>

            <!-- 3. Langkah Troubleshooting Dasar -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
              <div class="card-header bg-success bg-opacity-10 border-0 py-3 px-4">
                <h5 class="fw-bold text-success mb-0">
                  <i class="bi bi-tools me-2"></i> 3. Langkah Troubleshooting Dasar (Urutan Aman)
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="timeline-steps" id="basicStepsContainer">
                  <!-- Injected via JS -->
                </div>
              </div>
            </div>

            <!-- 4. Hal yang Perlu Diperiksa Berikutnya & 5. Escalation Guidelines -->
            <div class="row g-4 mb-4">
              
              <!-- 4. Hal Perlu Diperiksa Berikutnya -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                  <div class="card-header bg-info bg-opacity-10 border-0 py-3 px-4">
                    <h6 class="fw-bold text-dark mb-0">
                      <i class="bi bi-arrow-right-circle-fill text-info me-2"></i> 4. Hal Yang Perlu Diperiksa Berikutnya
                    </h6>
                  </div>
                  <div class="card-body p-4">
                    <ul class="ps-3 mb-0 text-muted extra-small" id="nextStepsContainer">
                      <!-- Injected via JS -->
                    </ul>
                  </div>
                </div>
              </div>

              <!-- 5. Saran Eskalasi -->
              <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden border-start border-danger border-4">
                  <div class="card-header bg-danger bg-opacity-10 border-0 py-3 px-4">
                    <h6 class="fw-bold text-danger mb-0">
                      <i class="bi bi-shield-exclamation me-2"></i> 5. Saran Eskalasi Ke Teknisi Senior / Operator
                    </h6>
                  </div>
                  <div class="card-body p-4">
                    <p class="small text-dark mb-0" id="escalationContainer">
                      <!-- Injected via JS -->
                    </p>
                  </div>
                </div>
              </div>

            </div>

          </div>

        </div>

      </div>

    </div>
  </div>
</div>

<!-- MODAL EDIT CATATAN PEMERIKSAAN AI -->
<div class="modal fade" id="modalEditAiNotes" tabindex="-1" aria-labelledby="modalEditAiNotesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      
      <div class="modal-header bg-dark-gradient text-white p-4 border-0">
        <h5 class="modal-title fw-extrabold" id="modalEditAiNotesLabel">
          <i class="bi bi-pencil-square text-warning me-2"></i> Edit Catatan Pemeriksaan Hasil AI
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        
        <div class="alert alert-info border-0 rounded-3 small mb-3">
          <i class="bi bi-info-circle-fill me-1"></i> Teknisi dapat **mengedit** dan menambahkan temuan aktual di lapangan sebelum menyalin atau menerapkan catatan ini ke Formulir Laporan Pekerjaan.
        </div>

        <form id="formEditAiNotes">
          
          <div class="mb-3">
            <label for="edit_diagnosis" class="form-label fw-bold text-dark small">Diagnosa & Kemungkinan Penyebab (Dapat Diedit)</label>
            <textarea id="edit_diagnosis" class="form-control rounded-3" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_inspection" class="form-label fw-bold text-dark small">Checklist & Hasil Pemeriksaan (Dapat Diedit)</label>
            <textarea id="edit_inspection" class="form-control rounded-3" rows="4"></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_action_taken" class="form-label fw-bold text-dark small">Langkah Tindakan / Troubleshooting (Dapat Diedit)</label>
            <textarea id="edit_action_taken" class="form-control rounded-3" rows="4"></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_notes" class="form-label fw-bold text-dark small">Saran Eskalasi / Catatan Lanjutan (Dapat Diedit)</label>
            <textarea id="edit_notes" class="form-control rounded-3" rows="2"></textarea>
          </div>

        </form>

      </div>

      <div class="modal-footer bg-light p-3 border-0 rounded-bottom-4 d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
          Batal
        </button>

        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-semibold" id="btnCopyEditedModal">
            <i class="bi bi-clipboard-check me-1"></i> Salin Catatan Yang Sudah Diedit
          </button>

          <?php if (!empty($order)): ?>
            <a href="<?= BASE_URL ?>/index.php?url=technician/report/<?= $order['id'] ?>" class="btn btn-success text-white rounded-pill px-4 fw-bold shadow-sm" id="btnApplyToOrderReport">
              <i class="bi bi-send-check me-1"></i> Terapkan ke Laporan Tiket <?= htmlspecialchars($order['order_code']) ?>
            </a>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const troubleshootForm = document.getElementById('troubleshootForm');
    const aiOutputEmpty    = document.getElementById('aiOutputEmpty');
    const aiOutputLoading  = document.getElementById('aiOutputLoading');
    const aiOutputResult   = document.getElementById('aiOutputResult');
    
    let currentAiResponseData = null;

    // Presets Click Handler
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const presetType = this.getAttribute('data-preset');
            
            if (presetType === 'network') {
                document.getElementById('device_type').value = 'PC Desktop / Computer';
                document.getElementById('symptom').value = 'PC menyala tetapi tidak dapat terhubung ke jaringan lokal atau internet.';
                document.getElementById('device_condition').value = 'Lampu indikator link LED di port RJ45 LAN PC mati total. Kabel terpasang tapi kait pengunci longgar.';
                document.getElementById('initial_check').value = 'Ping 127.0.0.1 success, ping gateway 192.168.1.1 RTO, IP DHCP didapat 169.254.12.89 (APIPA).';
            } else if (presetType === 'mikrotik') {
                document.getElementById('device_type').value = 'Router MikroTik';
                document.getElementById('symptom').value = 'Koneksi internet lambat, latency tinggi >500ms, dan sering ping spike RTO saat jam sibuk lab.';
                document.getElementById('device_condition').value = 'Bodi router terasa hangat, indikator LED port Ether1-Ether5 berkedip sangat cepat.';
                document.getElementById('initial_check').value = 'Ping 8.8.8.8 latency tinggi, CPU usage di Winbox terdeteksi 96%, status route default AS.';
            } else if (presetType === 'cctv') {
                document.getElementById('device_type').value = 'CCTV Analog / IP Cam & DVR/NVR';
                document.getElementById('symptom').value = 'Kamera CCTV Channel 2 & 3 No Video (layar tampilan DVR hitam polos).';
                document.getElementById('device_condition').value = 'Lensa kamera tampak fisik gelap, IR LED saat malam mati, konektor BNC jack garasi tampak korosi.';
                document.getElementById('initial_check').value = 'Pengukuran voltase adaptor DC di titik kamera hanya 8.2V (drop di bawah 12V DC).';
            } else if (presetType === 'laptop') {
                document.getElementById('device_type').value = 'Laptop / Notebook';
                document.getElementById('symptom').value = 'Laptop menyala 10 menit lalu mati mendadak saat membuka aplikasi render/video.';
                document.getElementById('device_condition').value = 'Bodi laptop bagian bawah sangat panas, suara kipas heatsink bising berputar maksimal.';
                document.getElementById('initial_check').value = 'Suhu CPU terdeteksi 97°C di HWMonitor sebelum laptop mati otomatis.';
            }
        });
    });

    // Reset Form
    document.getElementById('btnClearForm')?.addEventListener('click', function() {
        troubleshootForm.reset();
        aiOutputResult.classList.add('d-none');
        aiOutputEmpty.classList.remove('d-none');
    });

    // Form Submit Handler
    troubleshootForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const deviceType = document.getElementById('device_type').value;
        const symptom    = document.getElementById('symptom').value;
        const condition  = document.getElementById('device_condition').value;
        const check      = document.getElementById('initial_check').value;

        if (!deviceType || !symptom) {
            alert('Jenis Perangkat dan Gejala Masalah wajib diisi!');
            return;
        }

        // UI State: Loading
        aiOutputEmpty.classList.add('d-none');
        aiOutputResult.classList.add('d-none');
        aiOutputLoading.classList.remove('d-none');

        // Fetch API via Backend Secure Proxy
        fetch('<?= BASE_URL ?>/index.php?url=technician/ai_troubleshoot_api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                device_type: deviceType,
                symptom: symptom,
                device_condition: condition,
                initial_check: check
            })
        })
        .then(response => response.json())
        .then(data => {
            aiOutputLoading.classList.add('d-none');

            if (data.success && data.result) {
                currentAiResponseData = data.result;
                renderAiOutput(data.result);
                aiOutputResult.classList.remove('d-none');
            } else {
                alert('Gagal mendapatkan respon AI: ' + (data.error || 'Terjadi kesalahan sistem.'));
                aiOutputEmpty.classList.remove('d-none');
            }
        })
        .catch(err => {
            console.error(err);
            aiOutputLoading.classList.add('d-none');
            aiOutputEmpty.classList.remove('d-none');
            alert('Terjadi kesalahan jaringan saat menghubungkan ke AI Backend.');
        });
    });

    // Render AI Output Data into DOM
    function renderAiOutput(res) {
        // Disclaimer
        document.getElementById('aiDisclaimerText').innerText = res.disclaimer || 'Diagnosa bersifat indikatif dan tidak 100% pasti. Teknisi tetap mengambil keputusan akhir di lapangan.';

        // 1. Possible Causes
        const causesContainer = document.getElementById('possibleCausesContainer');
        causesContainer.innerHTML = '';
        if (res.possible_causes && res.possible_causes.length > 0) {
            res.possible_causes.forEach(pc => {
                let probClass = 'bg-warning text-dark';
                if (pc.probability.includes('Tinggi')) probClass = 'bg-danger text-white';
                else if (pc.probability.includes('Rendah')) probClass = 'bg-secondary text-white';

                const cardHtml = `
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge ${probClass} extra-small fw-bold">${pc.probability}</span>
                                <i class="bi bi-info-circle text-muted extra-small"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 small">${pc.cause}</h6>
                            <p class="extra-small text-muted mb-0">${pc.indicative_notes || ''}</p>
                        </div>
                    </div>
                `;
                causesContainer.innerHTML += cardHtml;
            });
        }

        // 2. Checklist Pemeriksaan
        const checkContainer = document.getElementById('inspectionChecklistContainer');
        checkContainer.innerHTML = '';
        if (res.inspection_checklist && res.inspection_checklist.length > 0) {
            res.inspection_checklist.forEach((chk, idx) => {
                const itemHtml = `
                    <li class="list-group-item bg-transparent px-0 py-2 border-bottom-light d-flex align-items-start gap-3">
                        <input class="form-check-input mt-1 flex-shrink-0" type="checkbox" id="chk_${idx}">
                        <label class="form-check-label small text-dark" for="chk_${idx}">
                            ${chk}
                        </label>
                    </li>
                `;
                checkContainer.innerHTML += itemHtml;
            });
        }

        // 3. Basic Steps
        const stepsContainer = document.getElementById('basicStepsContainer');
        stepsContainer.innerHTML = '';
        if (res.basic_steps && res.basic_steps.length > 0) {
            res.basic_steps.forEach((step, idx) => {
                const stepHtml = `
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="badge bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width: 28px; height: 28px;">
                            ${idx + 1}
                        </div>
                        <div class="pt-1 small text-dark font-weight-medium">
                            ${step}
                        </div>
                    </div>
                `;
                stepsContainer.innerHTML += stepHtml;
            });
        }

        // 4. Next Inspection Steps
        const nextContainer = document.getElementById('nextStepsContainer');
        nextContainer.innerHTML = '';
        if (res.next_inspection_steps && res.next_inspection_steps.length > 0) {
            res.next_inspection_steps.forEach(ns => {
                nextContainer.innerHTML += `<li class="mb-1">${ns}</li>`;
            });
        }

        // 5. Escalation Advice
        document.getElementById('escalationContainer').innerText = res.escalation_advice || 'Jika perbaikan dasar tidak berhasil, segera eskalasikan ke Senior Teknisi.';
    }

    // Modal Edit Notes Prep
    document.getElementById('btnUseAiNotes')?.addEventListener('click', function() {
        if (!currentAiResponseData) return;

        let diagText = "Kemungkinan Penyebab Utama (Indikatif):\n";
        (currentAiResponseData.possible_causes || []).forEach(pc => {
            diagText += `- ${pc.cause} (${pc.probability})\n`;
        });

        let inspText = "Checklist Pemeriksaan Lapangan:\n";
        (currentAiResponseData.inspection_checklist || []).forEach(ic => {
            inspText += `[ ] ${ic}\n`;
        });

        let actText = "Langkah Troubleshooting Dasar:\n";
        (currentAiResponseData.basic_steps || []).forEach((bs, i) => {
            actText += `${i+1}. ${bs}\n`;
        });

        let notesText = "Saran Eskalasi & Perhatian Lanjutan:\n" + (currentAiResponseData.escalation_advice || '');

        document.getElementById('edit_diagnosis').value = diagText;
        document.getElementById('edit_inspection').value = inspText;
        document.getElementById('edit_action_taken').value = actText;
        document.getElementById('edit_notes').value = notesText;

        // Store into localStorage for seamless auto-fill on Report page if navigating
        localStorage.setItem('tkj_ai_edited_diag', diagText);
        localStorage.setItem('tkj_ai_edited_insp', inspText);
        localStorage.setItem('tkj_ai_edited_act', actText);
        localStorage.setItem('tkj_ai_edited_notes', notesText);

        const modal = new bootstrap.Modal(document.getElementById('modalEditAiNotes'));
        modal.show();
    });

    // Copy Raw Summary
    document.getElementById('btnCopyRaw')?.addEventListener('click', function() {
        if (!currentAiResponseData) return;

        let fullText = "--- ASISTEN AI TROUBLESHOOTING TEKNISI TKJ ---\n\n";
        fullText += "1. KEMUNGKINAN PENYEBAB:\n";
        (currentAiResponseData.possible_causes || []).forEach(pc => {
            fullText += `- ${pc.cause} (${pc.probability})\n`;
        });
        fullText += "\n2. CHECKLIST PEMERIKSAAN:\n";
        (currentAiResponseData.inspection_checklist || []).forEach(ic => {
            fullText += `• ${ic}\n`;
        });
        fullText += "\n3. LANGKAH TROUBLESHOOTING:\n";
        (currentAiResponseData.basic_steps || []).forEach((bs, i) => {
            fullText += `${i+1}. ${bs}\n`;
        });
        fullText += "\n4. SARAN ESKALASI:\n" + (currentAiResponseData.escalation_advice || '');

        navigator.clipboard.writeText(fullText).then(() => {
            alert('Teks ringkasan AI berhasil disalin ke clipboard!');
        });
    });

    // Copy Edited Notes in Modal
    document.getElementById('btnCopyEditedModal')?.addEventListener('click', function() {
        const diag = document.getElementById('edit_diagnosis').value;
        const insp = document.getElementById('edit_inspection').value;
        const act  = document.getElementById('edit_action_taken').value;
        const nts  = document.getElementById('edit_notes').value;

        const combined = `=== CATATAN PEMERIKSAAN TEKNISI (EDITED AI) ===\n\n[DIAGNOSA]\n${diag}\n\n[PEMERIKSAAN]\n${insp}\n\n[TINDAKAN]\n${act}\n\n[CATATAN & ESKALASI]\n${nts}`;

        // Save updated inputs to localStorage
        localStorage.setItem('tkj_ai_edited_diag', diag);
        localStorage.setItem('tkj_ai_edited_insp', insp);
        localStorage.setItem('tkj_ai_edited_act', act);
        localStorage.setItem('tkj_ai_edited_notes', nts);

        navigator.clipboard.writeText(combined).then(() => {
            alert('Catatan hasil edit berhasil disalin ke clipboard!');
        });
    });

    // Apply to Order Report (if clicked)
    document.getElementById('btnApplyToOrderReport')?.addEventListener('click', function() {
        const diag = document.getElementById('edit_diagnosis').value;
        const insp = document.getElementById('edit_inspection').value;
        const act  = document.getElementById('edit_action_taken').value;
        const nts  = document.getElementById('edit_notes').value;

        localStorage.setItem('tkj_ai_edited_diag', diag);
        localStorage.setItem('tkj_ai_edited_insp', insp);
        localStorage.setItem('tkj_ai_edited_act', act);
        localStorage.setItem('tkj_ai_edited_notes', nts);
        localStorage.setItem('tkj_ai_autofill_pending', '1');
    });

});
</script>
