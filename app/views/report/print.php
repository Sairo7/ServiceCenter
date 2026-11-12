<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> - Business Unit TEFA TKJ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Times New Roman', Times, serif, sans-serif;
      background-color: #ffffff;
      color: #000000;
      font-size: 12pt;
    }
    .kop-surat {
      border-bottom: 3px double #000000;
      padding-bottom: 12px;
      margin-bottom: 20px;
    }
    .signature-box {
      margin-top: 50px;
    }
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        padding: 0;
        margin: 0;
      }
    }
  </style>
</head>
<body class="p-4">

  <!-- No-Print Action Toolbar -->
  <div class="no-print mb-4 p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
    <div>
      <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-printer-fill me-2 text-danger"></i> Pratinjau Dokumen Cetak / Export PDF</h6>
      <small class="text-muted">Gunakan opsi "Save as PDF" pada dialog cetak browser untuk menyimpan file PDF.</small>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button onclick="window.print()" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF
      </button>
      <button onclick="window.close()" class="btn btn-secondary btn-sm rounded-pill px-3">
        Tutup
      </button>
    </div>
  </div>

  <!-- KOP SURAT FORMAL -->
  <div class="kop-surat d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 60px; height: 60px; font-size: 1.5rem;">
        TKJ
      </div>
      <div>
        <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">TEACHING FACTORY (TEFA) TKJ SERVICE CENTER</h4>
        <h6 class="mb-0 text-muted">SMK NEGERI BUSINESS UNIT SERVICES & IT INFRASTRUCTURE</h6>
        <small class="text-secondary">Jl. Pendidikan No. 1, Telp: (022) 7654321, Website: tefa.tkj.sch.id</small>
      </div>
    </div>
    <div class="text-end">
      <span class="badge bg-dark text-white p-2 text-uppercase">DOKUMEN RESMI</span>
    </div>
  </div>

  <!-- REPORT METADATA BANNER -->
  <div class="text-center mb-4">
    <h4 class="fw-bold text-uppercase mb-1"><?= htmlspecialchars($tab_title) ?></h4>
    <p class="mb-0 text-muted">Periode Laporan: <strong><?= htmlspecialchars($report_data['period_label'] ?? 'Semua Periode') ?></strong></p>
    <small class="text-secondary">Dicetak Oleh: <?= htmlspecialchars($user['name'] ?? 'Sistem') ?> (Role: <?= htmlspecialchars($user['role'] ?? 'User') ?>) pada <?= date('d F Y, H:i') ?> WIB</small>
  </div>

  <!-- DATA TABLE RENDER -->
  <?php if ($active_tab === 'orders'): ?>
    <table class="table table-bordered table-striped align-middle small">
      <thead class="table-dark text-center text-uppercase">
        <tr>
          <th>No</th>
          <th>No Tiket</th>
          <th>Nama Customer</th>
          <th>Layanan Utama</th>
          <th>Teknisi</th>
          <th>Status</th>
          <th>Total (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($report_data['orders'])): ?>
          <?php foreach ($report_data['orders'] as $i => $ord): ?>
            <tr>
              <td class="text-center"><?= $i + 1 ?></td>
              <td class="fw-bold"><?= htmlspecialchars($ord['order_code']) ?></td>
              <td><?= htmlspecialchars($ord['customer_name'] ?? '-') ?></td>
              <td><?= htmlspecialchars($ord['main_service_name'] ?? '-') ?></td>
              <td><?= htmlspecialchars($ord['technician_name'] ?? 'Belum Ditugaskan') ?></td>
              <td class="text-center"><?= htmlspecialchars($ord['status']) ?></td>
              <td class="text-end fw-bold">Rp <?= number_format($ord['grand_total'] ?? 0, 0, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">Tidak ada data order.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php elseif ($active_tab === 'revenue'): ?>
    <div class="row mb-3 small">
      <div class="col-4"><strong>Total Omset Gross:</strong> Rp <?= number_format($report_data['gross_revenue'] ?? 0, 0, ',', '.') ?></div>
      <div class="col-4"><strong>Revenue Lunas:</strong> Rp <?= number_format($report_data['paid_revenue'] ?? 0, 0, ',', '.') ?></div>
      <div class="col-4"><strong>Piutang Unpaid:</strong> Rp <?= number_format($report_data['unpaid_revenue'] ?? 0, 0, ',', '.') ?></div>
    </div>
    <table class="table table-bordered table-striped align-middle small">
      <thead class="table-dark text-center text-uppercase">
        <tr>
          <th>No</th>
          <th>Kode Transaksi</th>
          <th>No Tiket</th>
          <th>Customer</th>
          <th>Biaya Jasa (Rp)</th>
          <th>Sparepart (Rp)</th>
          <th>Diskon (Rp)</th>
          <th>Grand Total (Rp)</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($report_data['transactions'])): ?>
          <?php foreach ($report_data['transactions'] as $i => $trx): ?>
            <tr>
              <td class="text-center"><?= $i + 1 ?></td>
              <td class="fw-bold"><?= htmlspecialchars($trx['transaction_code']) ?></td>
              <td><?= htmlspecialchars($trx['order_code']) ?></td>
              <td><?= htmlspecialchars($trx['customer_name']) ?></td>
              <td class="text-end">Rp <?= number_format($trx['total_service_fee'] ?? 0, 0, ',', '.') ?></td>
              <td class="text-end">Rp <?= number_format($trx['total_sparepart_fee'] ?? 0, 0, ',', '.') ?></td>
              <td class="text-end">Rp <?= number_format($trx['discount'] ?? 0, 0, ',', '.') ?></td>
              <td class="text-end fw-bold">Rp <?= number_format($trx['grand_total'] ?? 0, 0, ',', '.') ?></td>
              <td class="text-center"><?= htmlspecialchars($trx['payment_status']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center">Tidak ada data transaksi.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php elseif ($active_tab === 'services'): ?>
    <table class="table table-bordered table-striped align-middle small">
      <thead class="table-dark text-center text-uppercase">
        <tr>
          <th>Peringkat</th>
          <th>Kode Jasa</th>
          <th>Nama Layanan Jasa</th>
          <th>Kategori</th>
          <th>Frekuensi Dipesan</th>
          <th>Total Revenue Dihasilkan (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($report_data['services'])): ?>
          <?php foreach ($report_data['services'] as $i => $svc): ?>
            <tr>
              <td class="text-center fw-bold">#<?= $i + 1 ?></td>
              <td><?= htmlspecialchars($svc['code']) ?></td>
              <td class="fw-bold"><?= htmlspecialchars($svc['name']) ?></td>
              <td><?= htmlspecialchars($svc['category']) ?></td>
              <td class="text-center fw-bold"><?= number_format($svc['total_orders']) ?> Order</td>
              <td class="text-end fw-bold">Rp <?= number_format($svc['total_revenue'], 0, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center">Belum ada data jasa.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php elseif ($active_tab === 'customers'): ?>
    <table class="table table-bordered table-striped align-middle small">
      <thead class="table-dark text-center text-uppercase">
        <tr>
          <th>No</th>
          <th>Nama Pelanggan</th>
          <th>Kontak Email / Telepon</th>
          <th>Tipe Customer</th>
          <th>Kota</th>
          <th>Total Order</th>
          <th>Total Spending (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($report_data['customers'])): ?>
          <?php foreach ($report_data['customers'] as $i => $c): ?>
            <tr>
              <td class="text-center"><?= $i + 1 ?></td>
              <td class="fw-bold"><?= htmlspecialchars($c['full_name']) ?></td>
              <td><?= htmlspecialchars($c['email']) ?> / <?= htmlspecialchars($c['phone'] ?? '-') ?></td>
              <td class="text-uppercase"><?= htmlspecialchars($c['customer_type'] ?? 'umum') ?></td>
              <td><?= htmlspecialchars($c['city'] ?? '-') ?></td>
              <td class="text-center fw-bold"><?= number_format($c['total_orders']) ?> Order</td>
              <td class="text-end fw-bold">Rp <?= number_format($c['total_spent'] ?? 0, 0, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">Tidak ada data customer.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php elseif ($active_tab === 'technicians'): ?>
    <table class="table table-bordered table-striped align-middle small">
      <thead class="table-dark text-center text-uppercase">
        <tr>
          <th>No</th>
          <th>Nama Teknisi Siswa</th>
          <th>NIS & Kelas</th>
          <th>Spesialisasi</th>
          <th>Tugas Ditugaskan</th>
          <th>Tugas Selesai</th>
          <th>Rating Rata-rata</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($report_data['technicians'])): ?>
          <?php foreach ($report_data['technicians'] as $i => $tec): ?>
            <tr>
              <td class="text-center"><?= $i + 1 ?></td>
              <td class="fw-bold"><?= htmlspecialchars($tec['full_name']) ?></td>
              <td><?= htmlspecialchars($tec['nis_nip']) ?> (<?= htmlspecialchars($tec['class_grade']) ?>)</td>
              <td><?= htmlspecialchars($tec['specialization']) ?></td>
              <td class="text-center"><?= number_format($tec['total_assigned']) ?> Task</td>
              <td class="text-center fw-bold text-success"><?= number_format($tec['total_completed']) ?> Task</td>
              <td class="text-center fw-bold"><?= number_format($tec['avg_rating'], 1) ?> / 5.0</td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">Tidak ada data teknisi.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php elseif ($active_tab === 'ratings'): ?>
    <div class="row mb-3 small">
      <div class="col-4"><strong>Total Ulasan:</strong> <?= number_format($report_data['total_reviews'] ?? 0) ?> Review</div>
      <div class="col-4"><strong>Rating Rata-rata:</strong> <?= number_format($report_data['avg_rating'] ?? 5.0, 1) ?> / 5.0</div>
      <div class="col-4"><strong>Tingkat Kepuasan:</strong> <?= $report_data['satisfaction_rate'] ?? 100 ?>%</div>
    </div>
    <table class="table table-bordered table-striped align-middle small">
      <thead class="table-dark text-center text-uppercase">
        <tr>
          <th>No</th>
          <th>No Tiket</th>
          <th>Customer</th>
          <th>Layanan Jasa</th>
          <th>Teknisi</th>
          <th>Rating</th>
          <th>Komentar Feedback</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($report_data['ratings'])): ?>
          <?php foreach ($report_data['ratings'] as $i => $rt): ?>
            <tr>
              <td class="text-center"><?= $i + 1 ?></td>
              <td class="fw-bold"><?= htmlspecialchars($rt['order_code']) ?></td>
              <td><?= htmlspecialchars($rt['customer_name']) ?></td>
              <td><?= htmlspecialchars($rt['service_name'] ?? '-') ?></td>
              <td><?= htmlspecialchars($rt['technician_name'] ?? '-') ?></td>
              <td class="text-center fw-bold"><?= $rt['stars'] ?> Bintang</td>
              <td><?= nl2br(htmlspecialchars($rt['comment'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">Tidak ada ulasan rating.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  <?php elseif ($active_tab === 'period'): ?>
    <table class="table table-bordered table-striped align-middle small">
      <thead class="table-dark text-center text-uppercase">
        <tr>
          <th>No</th>
          <th>Periode Waktu</th>
          <th>Total Order Masuk</th>
          <th>Order Selesai</th>
          <th>Gross Omset (Rp)</th>
          <th>Revenue Lunas (Rp)</th>
          <th>Rating Rata-rata</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($report_data['timeline'])): ?>
          <?php foreach ($report_data['timeline'] as $i => $row): ?>
            <tr>
              <td class="text-center"><?= $i + 1 ?></td>
              <td class="fw-bold"><?= htmlspecialchars($row['period_date']) ?></td>
              <td class="text-center"><?= number_format($row['total_orders']) ?> Order</td>
              <td class="text-center fw-bold text-success"><?= number_format($row['completed_orders']) ?> Order</td>
              <td class="text-end">Rp <?= number_format($row['gross_revenue'], 0, ',', '.') ?></td>
              <td class="text-end fw-bold">Rp <?= number_format($row['paid_revenue'], 0, ',', '.') ?></td>
              <td class="text-center fw-bold"><?= number_format($row['avg_rating'], 1) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">Tidak ada data garis waktu.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <!-- OFFICIAL SIGNATURE BLOCKS -->
  <div class="signature-box row text-center">
    <div class="col-6">
      <p class="mb-5">Mengetahui,<br><strong>Kepala Sekolah SMKN</strong></p>
      <br><br>
      <p class="mb-0 text-decoration-underline fw-bold">Drs. H. Hendra M.Pd</p>
      <small class="text-muted">NIP. 19680315 199403 1 002</small>
    </div>
    <div class="col-6">
      <p class="mb-5">Kota Bandung, <?= date('d F Y') ?><br><strong>Operator Unit Bisnis TEFA TKJ</strong></p>
      <br><br>
      <p class="mb-0 text-decoration-underline fw-bold">Siti Rahmawati, S.Kom</p>
      <small class="text-muted">NIP / NPTK. 202208 001</small>
    </div>
  </div>

</body>
</html>
