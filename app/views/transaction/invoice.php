<div class="container py-4 my-auto">
  
  <div class="mb-3">
    <?php if (Auth::role() === 'operator'): ?>
      <a href="<?= BASE_URL ?>/index.php?url=order/manage" class="text-decoration-none text-secondary small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Panel Order Operator
      </a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/index.php?url=order/my_orders" class="text-decoration-none text-secondary small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan Saya
      </a>
    <?php endif; ?>
  </div>

  <?php Flasher::flash(); ?>

  <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
    
    <!-- Invoice Header -->
    <div class="card-header bg-dark-gradient text-white p-4 p-md-5 border-0">
      <div class="row align-items-center gy-3">
        <div class="col-md-8">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary px-3 py-2 rounded-pill font-weight-bold">
              <i class="bi bi-receipt me-1"></i> INVOICE TAGIHAN
            </span>
            <span class="badge bg-secondary-subtle text-light border px-2 py-1 extra-small">
              No. Trx: <?= htmlspecialchars($trx['transaction_code'] ?? 'TRX-001') ?>
            </span>
          </div>
          <h2 class="display-6 fw-extrabold mb-1">Tiket Order: <?= htmlspecialchars($order['order_code']) ?></h2>
          <p class="text-white opacity-75 mb-0">Customer: <strong><?= htmlspecialchars($order['customer_name']) ?></strong> (<?= htmlspecialchars($order['customer_phone']) ?>)</p>
        </div>
        
        <!-- 4 Payment Status Badges -->
        <div class="col-md-4 text-md-end">
          <div class="bg-white bg-opacity-10 p-3 rounded-3 d-inline-block text-start border border-white border-opacity-25">
            <span class="text-white-50 extra-small d-block mb-1">Status Pembayaran:</span>
            <?php 
              $pSt = $trx['payment_status'] ?? 'Belum Dibayar';
              $pBadgeClass = 'bg-danger text-white';
              if ($pSt === 'Menunggu Verifikasi') $pBadgeClass = 'bg-warning text-dark';
              else if ($pSt === 'Lunas') $pBadgeClass = 'bg-success text-white';
              else if ($pSt === 'Dibatalkan') $pBadgeClass = 'bg-secondary text-white';
            ?>
            <span class="badge <?= $pBadgeClass ?> fs-6 px-3 py-2 rounded-pill fw-bold">
              <i class="bi bi-wallet2 me-1"></i> <?= htmlspecialchars($pSt) ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoice Body & Cost Breakdown -->
    <div class="card-body p-4 p-md-5">
      <div class="row gy-4">
        
        <!-- Itemized Cost Table -->
        <div class="col-lg-8">
          <h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-stars me-2 text-primary"></i> Rincian Komponen Biaya Transaksi</h5>
          
          <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
              <thead class="bg-light extra-small text-uppercase text-muted">
                <tr>
                  <th>Deskripsi Komponen Biaya</th>
                  <th class="text-end" style="width: 220px;">Nominal (Rp)</th>
                </tr>
              </thead>
              <tbody class="small">
                <tr>
                  <td>
                    <div class="fw-bold text-dark">1. Biaya Jasa Teknis (Service Fee)</div>
                    <span class="extra-small text-muted"><?= htmlspecialchars($order['service_name']) ?></span>
                  </td>
                  <td class="text-end fw-bold text-dark">
                    Rp <?= number_format($trx['total_service_fee'] ?? 0, 0, ',', '.') ?>
                  </td>
                </tr>

                <tr>
                  <td>
                    <div class="fw-bold text-dark">2. Biaya Sparepart / Suku Cadang Terpakai</div>
                    <span class="extra-small text-muted">Total penggantian komponen hardware/kabel</span>
                  </td>
                  <td class="text-end fw-bold text-dark">
                    Rp <?= number_format($trx['total_sparepart_fee'] ?? 0, 0, ',', '.') ?>
                  </td>
                </tr>

                <tr>
                  <td>
                    <div class="fw-bold text-dark">3. Biaya Tambahan (Operasional / Transport)</div>
                    <span class="extra-small text-muted">Biaya penanganan lokasi & pendukung</span>
                  </td>
                  <td class="text-end fw-bold text-dark">
                    Rp <?= number_format($trx['tax_amount'] ?? 0, 0, ',', '.') ?>
                  </td>
                </tr>

                <tr class="table-success table-opacity-10">
                  <td class="text-success fw-bold">
                    4. Potongan Diskon
                  </td>
                  <td class="text-end fw-bold text-success">
                    - Rp <?= number_format($trx['discount'] ?? 0, 0, ',', '.') ?>
                  </td>
                </tr>

                <tr class="table-dark fs-6">
                  <td class="fw-extrabold ps-3">TOTAL TAGIHAN TRANSISASI (GRAND TOTAL)</td>
                  <td class="text-end fw-extrabold text-success pe-3">
                    Rp <?= number_format($trx['grand_total'] ?? 0, 0, ',', '.') ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="alert alert-light border small text-muted">
            <i class="bi bi-shield-check text-primary me-1"></i> Perhitungan Grand Total dihitung secara aman di backend server PHP ($Total = Jasa + Sparepart + Tambahan - Diskon$).
          </div>

        </div>

        <!-- Right Side: Payment Form & Operator Controls -->
        <div class="col-lg-4">
          
          <!-- OPERATOR INVOICE CREATOR & PAYMENT VERIFICATION -->
          <?php if (Auth::role() === 'operator'): ?>
            <div class="card border-0 bg-dark-gradient text-white p-4 rounded-4 mb-4">
              <h5 class="fw-bold mb-3"><i class="bi bi-calculator-fill text-warning me-2"></i> Operator Billing Controls</h5>
              
              <!-- Modal Trigger to Edit Billing Breakdown -->
              <button type="button" class="btn btn-warning text-dark font-weight-bold w-100 rounded-pill mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateInvoice">
                <i class="bi bi-pencil-square me-1"></i> Susun / Edit Rincian Tagihan
              </button>

              <?php if (($trx['payment_status'] ?? '') !== 'Lunas'): ?>
                <form action="<?= BASE_URL ?>/index.php?url=transaction/verifyPayment/<?= $order['id'] ?>" method="POST">
                  <input type="hidden" name="status" value="Lunas">
                  <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran LUNAS untuk tiket ini?')">
                    <i class="bi bi-check-circle-fill me-1"></i> Verifikasi Pembayaran LUNAS
                  </button>
                </form>
              <?php else: ?>
                <div class="alert alert-success text-center mb-0 py-2 fw-bold">
                  <i class="bi bi-check-all me-1"></i> Pembayaran LUNAS & Terverifikasi
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <!-- CUSTOMER MANUAL PAYMENT MVP RECORDING -->
          <div class="card border-0 bg-light p-4 rounded-4">
            <h5 class="fw-bold text-dark mb-2"><i class="bi bi-cash-coin text-success me-2"></i> Pembayaran Manual MVP</h5>
            <p class="text-muted extra-small mb-3">Catat konfirmasi pembayaran manual Anda (Transfer Bank / Cash) untuk diverifikasi Operator.</p>

            <?php if (($trx['payment_status'] ?? '') === 'Lunas'): ?>
              <div class="alert alert-success text-center border-0 shadow-sm rounded-3 py-3 mb-0">
                <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-1"></i>
                <div class="fw-bold text-dark">TAGIHAN LUNAS</div>
                <div class="extra-small text-muted">Terima kasih, pembayaran Anda telah terverifikasi resmi oleh Operator TEFA.</div>
              </div>
            <?php else: ?>
              <form action="<?= BASE_URL ?>/index.php?url=transaction/pay/<?= $order['id'] ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label class="form-label extra-small fw-bold text-dark">Metode Pembayaran</label>
                  <select name="payment_method" class="form-select form-select-sm rounded-3" required>
                    <option value="transfer_bank">Transfer Bank (BCA / Mandiri)</option>
                    <option value="cash">Tunai / Cash di Workshop TKJ</option>
                    <option value="qris">QRIS Sekolah / E-Wallet</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label extra-small fw-bold text-dark">Nominal Pembayaran (Rp)</label>
                  <input type="number" name="amount" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($trx['grand_total'] ?? 0) ?>" required>
                </div>

                <div class="mb-3">
                  <label class="form-label extra-small fw-bold text-dark">Upload Bukti Bayar (Opsional)</label>
                  <input type="file" name="proof_image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                </div>

                <button type="submit" class="btn btn-primary-gradient text-white btn-sm w-100 rounded-pill fw-bold shadow-sm">
                  <i class="bi bi-send-fill me-1"></i> Catat Konfirmasi Pembayaran
                </button>
              </form>
            <?php endif; ?>

          </div>

        </div>

      </div>
    </div>
  </div>

</div>

<!-- Modal Operator Create/Edit Invoice -->
<div class="modal fade" id="modalCreateInvoice" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 border-0">
      <form action="<?= BASE_URL ?>/index.php?url=transaction/saveInvoice/<?= $order['id'] ?>" method="POST">
        <div class="modal-header bg-dark-gradient text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-calculator me-2"></i> Susun Rincian Tagihan Order</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">1. Biaya Jasa Teknis (Rp) <span class="text-danger">*</span></label>
              <input type="number" name="total_service_fee" class="form-control" value="<?= htmlspecialchars($trx['total_service_fee'] ?? 200000) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">2. Biaya Sparepart Terpakai (Rp)</label>
              <input type="number" name="total_sparepart_fee" class="form-control" value="<?= htmlspecialchars($trx['total_sparepart_fee'] ?? 0) ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">3. Biaya Tambahan / Operasional (Rp)</label>
              <input type="number" name="tax_amount" class="form-control" value="<?= htmlspecialchars($trx['tax_amount'] ?? 0) ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">4. Potongan Diskon (Rp)</label>
              <input type="number" name="discount" class="form-control" value="<?= htmlspecialchars($trx['discount'] ?? 0) ?>">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-gradient text-white rounded-pill px-4 fw-bold">Simpan & Hitung Tagihan</button>
        </div>
      </form>
    </div>
  </div>
</div>
