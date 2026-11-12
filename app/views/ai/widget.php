<?php
/**
 * Global Floating AI Assistant Widget
 * Available for all logged in users across all roles (and guest preview if desired)
 */
$currentUser = Auth::user();
$currentRole = $currentUser['role'] ?? 'customer';
$roleTitles = [
    'customer' => 'Customer',
    'teknisi' => 'Teknisi Siswa',
    'operator' => 'Operator TEFA',
    'kepala_sekolah' => 'Kepala Sekolah'
];
$displayRoleName = $roleTitles[$currentRole] ?? 'Customer';

// Load recent conversation history if user is logged in
$widgetHistory = [];
if ($currentUser) {
    try {
        $aiModelObj = new AiModel();
        $widgetHistory = $aiModelObj->getConversationHistory($currentUser['id'], 15);
    } catch (\Throwable $th) {
        $widgetHistory = [];
    }
}
?>
<style>
/* Strictly Isolated Styles for Floating Chatbot Widget */
#tkjFloatingChatWrap.tkj-chat-floating-wrap {
  position: fixed !important;
  bottom: 24px !important;
  right: 24px !important;
  z-index: 99999 !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: flex-end !important;
  pointer-events: none !important;
  margin: 0 !important;
  padding: 0 !important;
}

#tkjFloatingChatWrap .tkj-chat-toggle-btn {
  pointer-events: auto !important;
  width: 58px !important;
  height: 58px !important;
  border-radius: 50% !important;
  border: none !important;
  background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%) !important;
  color: #ffffff !important;
  box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  position: relative !important;
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease !important;
  outline: none !important;
}

#tkjFloatingChatWrap .tkj-chat-toggle-btn:hover {
  transform: scale(1.08) !important;
  box-shadow: 0 12px 30px rgba(37, 99, 235, 0.55) !important;
}

#tkjFloatingChatWrap .tkj-chat-toggle-btn:active {
  transform: scale(0.95) !important;
}

#tkjFloatingChatWrap .tkj-chat-toggle-btn .chat-btn-icon-open,
#tkjFloatingChatWrap .tkj-chat-toggle-btn .chat-btn-icon-close {
  position: absolute !important;
  font-size: 1.5rem !important;
  transition: opacity 0.2s ease, transform 0.2s ease !important;
}

#tkjFloatingChatWrap .tkj-chat-toggle-btn .chat-btn-icon-close {
  opacity: 0 !important;
  transform: rotate(-90deg) scale(0.7) !important;
}

#tkjFloatingChatWrap.active .tkj-chat-toggle-btn .chat-btn-icon-open {
  opacity: 0 !important;
  transform: rotate(90deg) scale(0.7) !important;
}

#tkjFloatingChatWrap.active .tkj-chat-toggle-btn .chat-btn-icon-close {
  opacity: 1 !important;
  transform: rotate(0deg) scale(1) !important;
}

#tkjFloatingChatWrap .tkj-chat-badge-pulse {
  position: absolute !important;
  top: 2px !important;
  right: 2px !important;
  width: 13px !important;
  height: 13px !important;
  background-color: #10b981 !important;
  border: 2px solid #ffffff !important;
  border-radius: 50% !important;
}

#tkjFloatingChatWrap .tkj-chat-panel {
  pointer-events: auto !important;
  width: 380px !important;
  max-width: calc(100vw - 32px) !important;
  height: 540px !important;
  max-height: calc(100vh - 110px) !important;
  background: #ffffff !important;
  border-radius: 20px !important;
  box-shadow: 0 16px 48px -8px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(15, 23, 42, 0.06) !important;
  display: flex !important;
  flex-direction: column !important;
  overflow: hidden !important;
  margin-bottom: 14px !important;
  transform-origin: bottom right !important;
  opacity: 0 !important;
  transform: scale(0.85) translateY(20px) !important;
  visibility: hidden !important;
  transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.25s !important;
}

#tkjFloatingChatWrap.active .tkj-chat-panel {
  opacity: 1 !important;
  transform: scale(1) translateY(0) !important;
  visibility: visible !important;
}

#tkjFloatingChatWrap .tkj-chat-panel-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
  color: #ffffff !important;
  padding: 14px 18px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
  flex-shrink: 0 !important;
}

#tkjFloatingChatWrap .tkj-chat-panel-title {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
}

#tkjFloatingChatWrap .tkj-chat-panel-title .bot-avatar {
  width: 36px !important;
  height: 36px !important;
  border-radius: 10px !important;
  background: linear-gradient(135deg, #2563eb, #06b6d4) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #ffffff !important;
  font-size: 1.15rem !important;
  box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35) !important;
}

#tkjFloatingChatWrap .tkj-chat-panel-actions button {
  background: transparent !important;
  border: none !important;
  color: #94a3b8 !important;
  width: 30px !important;
  height: 30px !important;
  border-radius: 8px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
}

#tkjFloatingChatWrap .tkj-chat-panel-actions button:hover {
  background: rgba(255, 255, 255, 0.12) !important;
  color: #ffffff !important;
}

#tkjFloatingChatWrap .tkj-chat-quick-chips {
  background: #f8fafc !important;
  padding: 8px 14px !important;
  border-bottom: 1px solid #e2e8f0 !important;
  display: flex !important;
  gap: 6px !important;
  overflow-x: auto !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
  scrollbar-width: thin !important;
}

#tkjFloatingChatWrap .tkj-chat-quick-chips .chip-btn {
  font-size: 0.72rem !important;
  padding: 4px 10px !important;
  border-radius: 50px !important;
  border: 1px solid #cbd5e1 !important;
  background: #ffffff !important;
  color: #334155 !important;
  cursor: pointer !important;
  transition: all 0.15s ease !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 4px !important;
}

#tkjFloatingChatWrap .tkj-chat-quick-chips .chip-btn:hover {
  background: #eff6ff !important;
  border-color: #3b82f6 !important;
  color: #1d4ed8 !important;
}

#tkjFloatingChatWrap .tkj-chat-messages {
  flex: 1 !important;
  overflow-y: auto !important;
  padding: 16px !important;
  background: #f8fafc !important;
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}

#tkjFloatingChatWrap .tkj-chat-msg-row {
  display: flex !important;
  align-items: flex-start !important;
  gap: 8px !important;
}

#tkjFloatingChatWrap .tkj-chat-msg-row.bot {
  justify-content: flex-start !important;
}

#tkjFloatingChatWrap .tkj-chat-msg-row.user {
  justify-content: flex-end !important;
}

#tkjFloatingChatWrap .tkj-chat-msg-bubble {
  max-width: 82% !important;
  padding: 10px 14px !important;
  border-radius: 16px !important;
  font-size: 0.82rem !important;
  line-height: 1.48 !important;
  word-break: break-word !important;
}

#tkjFloatingChatWrap .tkj-chat-msg-row.bot .tkj-chat-msg-bubble {
  background: #ffffff !important;
  color: #0f172a !important;
  border: 1px solid #e2e8f0 !important;
  border-bottom-left-radius: 4px !important;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04) !important;
}

#tkjFloatingChatWrap .tkj-chat-msg-row.user .tkj-chat-msg-bubble {
  background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
  color: #ffffff !important;
  border-bottom-right-radius: 4px !important;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
}

#tkjFloatingChatWrap .tkj-chat-footer {
  padding: 12px 14px !important;
  background: #ffffff !important;
  border-top: 1px solid #e2e8f0 !important;
  flex-shrink: 0 !important;
}

#tkjFloatingChatWrap .tkj-chat-input-box {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  background: #f1f5f9 !important;
  border: 1px solid #cbd5e1 !important;
  border-radius: 24px !important;
  padding: 4px 6px 4px 14px !important;
  transition: border-color 0.2s, box-shadow 0.2s !important;
}

#tkjFloatingChatWrap .tkj-chat-input-box:focus-within {
  border-color: #2563eb !important;
  background: #ffffff !important;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
}

#tkjFloatingChatWrap .tkj-chat-input-box input {
  flex: 1 !important;
  border: none !important;
  background: transparent !important;
  outline: none !important;
  font-size: 0.85rem !important;
  color: #0f172a !important;
  padding: 6px 0 !important;
}

#tkjFloatingChatWrap .tkj-chat-send-btn {
  width: 34px !important;
  height: 34px !important;
  border-radius: 50% !important;
  border: none !important;
  background: linear-gradient(135deg, #2563eb, #06b6d4) !important;
  color: #ffffff !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: transform 0.15s ease, opacity 0.15s ease !important;
  flex-shrink: 0 !important;
}

#tkjFloatingChatWrap .tkj-chat-send-btn:hover {
  transform: scale(1.06) !important;
}

#tkjFloatingChatWrap .tkj-chat-send-btn:active {
  transform: scale(0.92) !important;
}

@media (max-width: 576px) {
  #tkjFloatingChatWrap.tkj-chat-floating-wrap {
    bottom: 16px !important;
    right: 16px !important;
  }
  #tkjFloatingChatWrap .tkj-chat-panel {
    width: calc(100vw - 32px) !important;
    height: calc(100vh - 90px) !important;
    max-height: 560px !important;
    margin-bottom: 10px !important;
  }
}
</style>
<div class="tkj-chat-floating-wrap" id="tkjFloatingChatWrap">
  <!-- Chat Panel Window -->
  <div class="tkj-chat-panel" id="tkjChatPanel" role="dialog" aria-labelledby="tkjChatHeaderTitle" aria-hidden="true">
    
    <!-- Panel Header -->
    <div class="tkj-chat-panel-header">
      <div class="tkj-chat-panel-title">
        <div class="bot-avatar">
          <i class="bi bi-robot"></i>
        </div>
        <div>
          <div class="fw-bold fs-6 text-white mb-0" id="tkjChatHeaderTitle" style="line-height: 1.2;">
            AI <?= htmlspecialchars($displayRoleName) ?> Assistant
          </div>
          <div class="d-flex align-items-center gap-1 extra-small text-light opacity-75">
            <span class="rounded-circle bg-success d-inline-block" style="width: 7px; height: 7px;"></span>
            <span>Online 24/7 &bull; TKJ Service</span>
          </div>
        </div>
      </div>
      <div class="tkj-chat-panel-actions">
        <button type="button" id="tkjWidgetClearBtn" title="Bersihkan Percakapan" aria-label="Bersihkan Chat">
          <i class="bi bi-trash3"></i>
        </button>
        <button type="button" id="tkjWidgetCloseBtn" title="Tutup Chatbot" aria-label="Tutup Chatbot">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    <!-- Quick Question Chips -->
    <div class="tkj-chat-quick-chips">
      <?php if ($currentRole === 'teknisi'): ?>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-briefcase text-primary"></i> Ada berapa tugas yang ditugaskan kepada saya?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-clock-history text-info"></i> Apa saja order yang belum selesai?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-check-circle text-success"></i> Berapa pekerjaan yang sudah saya selesaikan?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-file-earmark-text text-warning"></i> Apa saja checklist laporan pekerjaan teknisi?
        </button>
      <?php elseif ($currentRole === 'operator'): ?>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-people text-primary"></i> Ada berapa jumlah user yang terdaftar?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-tools text-success"></i> Ada berapa teknisi?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-inbox text-warning"></i> Ada berapa pengajuan layanan?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-activity text-info"></i> Ada berapa order yang masih berjalan?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-receipt text-dark"></i> Berapa transaksi yang tercatat?
        </button>
      <?php elseif ($currentRole === 'kepala_sekolah'): ?>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-folder-check text-primary"></i> Berapa total order dalam sistem?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-check2-all text-success"></i> Berapa order yang sudah selesai?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-graph-up-arrow text-warning"></i> Bagaimana statistik kinerja teknisi?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-trophy text-info"></i> Layanan apa yang paling banyak dipesan?
        </button>
      <?php else: ?>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-box-seam text-primary"></i> Ada berapa order milik saya?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-clock-history text-info"></i> Bagaimana status pesanan saya?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-credit-card text-success"></i> Berapa tagihan saya yang belum dibayar?
        </button>
        <button type="button" class="chip-btn tkj-widget-preset-btn">
          <i class="bi bi-camera-video text-warning"></i> Berapa estimasi biaya pasang CCTV sekolah?
        </button>
      <?php endif; ?>
    </div>

    <!-- Messages Container -->
    <div class="tkj-chat-messages" id="tkjWidgetMessageArea">
      <!-- Default Bot Greeting -->
      <div class="tkj-chat-msg-row bot">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
          <i class="bi bi-robot" style="font-size: 0.8rem;"></i>
        </div>
        <div class="tkj-chat-msg-bubble">
          <?php if ($currentRole === 'teknisi'): ?>
            Halo Rekan Teknisi! 👨‍🔧 Saya <strong>AI Technical Assistant</strong>.<br>
            Siap membantu troubleshooting jaringan LAN, MikroTik, CCTV, PC/laptop, checklist pengerjaan, dan pembuatan laporan teknisi.
          <?php elseif ($currentRole === 'operator'): ?>
            Halo Operator! 💼 Saya <strong>AI Operasional Assistant</strong>.<br>
            Siap membantu verifikasi order masuk, penugasan teknisi, jadwal pengerjaan, dan pengelolaan transaksi invoice.
          <?php elseif ($currentRole === 'kepala_sekolah'): ?>
            Selamat datang Bapak/Ibu Kepala Sekolah! 🏛️ Saya <strong>AI Executive Assistant</strong>.<br>
            Siap menyajikan data monitoring pesanan, rekap omset bisnis TEFA, serta evaluasi performa teknisi siswa.
          <?php else: ?>
            Halo! 👋 Saya <strong>AI Assistant TKJ Service Center</strong>.<br>
            Ada kendala perangkat komputer, printer, CCTV, atau jaringan di tempat Anda? Tanyakan langsung di sini!
          <?php endif; ?>
        </div>
      </div>

      <!-- History Render (If Available) -->
      <?php if (!empty($widgetHistory)): ?>
        <?php foreach ($widgetHistory as $wh): 
          $qText = $wh['question'] ?? ($wh['message'] ?? '');
          $aText = $wh['answer'] ?? '';
          if (empty($qText) && empty($aText)) continue;
        ?>
          <?php if (!empty($qText)): ?>
            <div class="tkj-chat-msg-row user">
              <div class="tkj-chat-msg-bubble">
                <?= htmlspecialchars($qText) ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($aText)): ?>
            <div class="tkj-chat-msg-row bot">
              <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                <i class="bi bi-robot" style="font-size: 0.8rem;"></i>
              </div>
              <div class="tkj-chat-msg-bubble">
                <?= nl2br(htmlspecialchars($aText)) ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Chat Footer Input -->
    <div class="tkj-chat-footer">
      <form id="tkjWidgetForm" onsubmit="return false;">
        <div class="tkj-chat-input-box">
          <input type="text" id="tkjWidgetInput" placeholder="Ketik pertanyaan atau kendala Anda..." autocomplete="off" required>
          <button type="submit" class="tkj-chat-send-btn" id="tkjWidgetSendBtn" title="Kirim Pesan" aria-label="Kirim Pesan">
            <i class="bi bi-send-fill" style="font-size: 0.85rem;"></i>
          </button>
        </div>
      </form>
    </div>

  </div>

  <!-- Floating Toggle Button -->
  <button type="button" class="tkj-chat-toggle-btn" id="tkjFloatingChatBtn" title="Buka AI Assistant" aria-label="Buka AI Assistant" aria-haspopup="dialog" aria-expanded="false">
    <i class="bi bi-chat-dots-fill chat-btn-icon-open"></i>
    <i class="bi bi-x-lg chat-btn-icon-close"></i>
    <span class="tkj-chat-badge-pulse" title="Online"></span>
  </button>
</div>

<!-- Floating Chatbot Logic -->
<script>
(function() {
    // Prevent duplicate widget initialization
    if (window.__TKJ_CHATBOT_INITIALIZED__) return;
    window.__TKJ_CHATBOT_INITIALIZED__ = true;

    document.addEventListener('DOMContentLoaded', function() {
        const wrap = document.getElementById('tkjFloatingChatWrap');
        const toggleBtn = document.getElementById('tkjFloatingChatBtn');
        const closeBtn = document.getElementById('tkjWidgetCloseBtn');
        const clearBtn = document.getElementById('tkjWidgetClearBtn');
        const chatForm = document.getElementById('tkjWidgetForm');
        const chatInput = document.getElementById('tkjWidgetInput');
        const chatArea = document.getElementById('tkjWidgetMessageArea');
        const panel = document.getElementById('tkjChatPanel');
        const baseUrl = '<?= BASE_URL ?>';
        const roleGreeting = <?= json_encode($currentRole) ?>;

        if (!wrap || !toggleBtn) return;

        // Auto Scroll Messages
        function scrollToBottom() {
            if (chatArea) {
                chatArea.scrollTop = chatArea.scrollHeight;
            }
        }
        scrollToBottom();

        // Open/Close Widget
        function openChat() {
            wrap.classList.add('active');
            toggleBtn.setAttribute('aria-expanded', 'true');
            if (panel) panel.setAttribute('aria-hidden', 'false');
            scrollToBottom();
            setTimeout(() => {
                chatInput?.focus();
            }, 250);
        }

        function closeChat() {
            wrap.classList.remove('active');
            toggleBtn.setAttribute('aria-expanded', 'false');
            if (panel) panel.setAttribute('aria-hidden', 'true');
        }

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (wrap.classList.contains('active')) {
                closeChat();
            } else {
                openChat();
            }
        });

        closeBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            closeChat();
        });

        // Close when clicking outside of the panel (optional convenience)
        document.addEventListener('click', function(e) {
            if (wrap.classList.contains('active') && !wrap.contains(e.target)) {
                closeChat();
            }
        });

        // Preset Chips Click
        document.querySelectorAll('.tkj-widget-preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (chatInput) {
                    chatInput.value = this.innerText.trim();
                    handleSendMessage();
                }
            });
        });

        // Clear Chat Messages
        clearBtn?.addEventListener('click', function() {
            let defaultGreeting = 'Halo! 👋 Saya <strong>AI Assistant TKJ Service Center</strong>.<br>Ada kendala perangkat komputer, printer, CCTV, atau jaringan di tempat Anda? Tanyakan saja di sini!';
            if (roleGreeting === 'teknisi') {
                defaultGreeting = 'Halo Rekan Teknisi! 👨‍🔧 Saya <strong>AI Technical Assistant</strong>.<br>Siap membantu troubleshooting jaringan LAN, MikroTik, CCTV, PC/laptop, checklist pengerjaan, dan pembuatan laporan teknisi.';
            } else if (roleGreeting === 'operator') {
                defaultGreeting = 'Halo Operator! 💼 Saya <strong>AI Operasional Assistant</strong>.<br>Siap membantu verifikasi order masuk, penugasan teknisi, jadwal pengerjaan, dan pengelolaan transaksi invoice.';
            } else if (roleGreeting === 'kepala_sekolah') {
                defaultGreeting = 'Selamat datang Bapak/Ibu Kepala Sekolah! 🏛️ Saya <strong>AI Executive Assistant</strong>.<br>Siap menyajikan data monitoring pesanan, rekap omset bisnis TEFA, serta evaluasi performa teknisi siswa.';
            }

            chatArea.innerHTML = `
              <div class="tkj-chat-msg-row bot">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                  <i class="bi bi-robot" style="font-size: 0.8rem;"></i>
                </div>
                <div class="tkj-chat-msg-bubble">
                  ${defaultGreeting}
                </div>
              </div>
            `;
            scrollToBottom();
        });

        // Form Submit
        chatForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            handleSendMessage();
        });

        function handleSendMessage() {
            if (!chatInput) return;
            const message = chatInput.value.trim();
            if (!message) return;

            // 1. Append User Message
            const userHtml = `
                <div class="tkj-chat-msg-row user">
                  <div class="tkj-chat-msg-bubble">
                    ${escapeHtml(message)}
                  </div>
                </div>
            `;
            chatArea.innerHTML += userHtml;
            chatInput.value = '';
            scrollToBottom();

            // 2. Append Loading Placeholder
            const loadingId = 'tkj_loading_' + Date.now();
            const loadingHtml = `
                <div class="tkj-chat-msg-row bot" id="${loadingId}">
                  <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                    <i class="bi bi-robot" style="font-size: 0.8rem;"></i>
                  </div>
                  <div class="tkj-chat-msg-bubble d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="text-muted extra-small">AI sedang mengetik jawaban...</span>
                  </div>
                </div>
            `;
            chatArea.innerHTML += loadingHtml;
            scrollToBottom();

            // 3. Send AJAX to AiController::ask
            fetch(baseUrl + '/index.php?url=ai/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById(loadingId)?.remove();

                if (data.success && data.text) {
                    let textFormatted = escapeHtml(data.text)
                        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\n/g, '<br>');

                    let orderBtnHtml = '';
                    if (data.recommended_service) {
                        orderBtnHtml = `
                            <div class="mt-2 pt-2 border-top">
                                <a href="${baseUrl}/index.php?url=<?= Auth::check() ? 'order/create' : 'auth/login' ?>" class="btn btn-sm btn-success text-white rounded-pill px-3 fw-bold extra-small">
                                    <i class="bi bi-plus-circle me-1"></i> Pesan Jasa Ini Sekarang
                                </a>
                            </div>
                        `;
                    }

                    const aiHtml = `
                        <div class="tkj-chat-msg-row bot">
                          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                            <i class="bi bi-robot" style="font-size: 0.8rem;"></i>
                          </div>
                          <div class="tkj-chat-msg-bubble">
                            ${textFormatted}
                            ${orderBtnHtml}
                          </div>
                        </div>
                    `;
                    chatArea.innerHTML += aiHtml;
                } else {
                    const errHtml = `
                        <div class="tkj-chat-msg-row bot">
                          <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                            <i class="bi bi-exclamation-triangle" style="font-size: 0.8rem;"></i>
                          </div>
                          <div class="tkj-chat-msg-bubble text-danger border border-danger-subtle">
                            Maaf, gagal memproses pertanyaan. ${data.error || 'Silakan coba beberapa saat lagi.'}
                          </div>
                        </div>
                    `;
                    chatArea.innerHTML += errHtml;
                }
                scrollToBottom();
            })
            .catch(err => {
                console.error(err);
                document.getElementById(loadingId)?.remove();
                const netErrHtml = `
                    <div class="tkj-chat-msg-row bot">
                      <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px;">
                        <i class="bi bi-wifi-off" style="font-size: 0.8rem;"></i>
                      </div>
                      <div class="tkj-chat-msg-bubble text-danger border border-danger-subtle">
                        Terjadi kesalahan koneksi jaringan.
                      </div>
                    </div>
                `;
                chatArea.innerHTML += netErrHtml;
                scrollToBottom();
            });
        }

        function escapeHtml(str) {
            return str.replace(/&/g, "&amp;")
                      .replace(/</g, "&lt;")
                      .replace(/>/g, "&gt;")
                      .replace(/"/g, "&quot;")
                      .replace(/'/g, "&#039;");
        }

        // Global trigger from anywhere: window.openTkjChatbot()
        window.openTkjChatbot = function() {
            openChat();
        };
    });
})();
</script>
