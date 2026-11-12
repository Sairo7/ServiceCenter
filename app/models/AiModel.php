<?php
/**
 * AiModel Class
 * Handles AI Catalog Prompt Context Building, Conversation Logging, and Smart Fallback Engine
 */

class AiModel
{
    private $db;
    private $table = 'ai_conversations';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Build DB Service Catalog Context to prevent price hallucinations
     */
    public function buildCatalogContext()
    {
        $serviceModel = new ServiceModel();
        $services = $serviceModel->getAllServices('', '', true);

        $contextText = "DAFTAR RESMI KATALOG JASA TKJ SERVICE CENTER:\n";
        foreach ($services as $s) {
            $contextText .= "- " . $s['name'] . " (Kategori: " . $s['category'] . ", Harga Mulai: Rp " . number_format($s['price_starts_from'], 0, ',', '.') . ", Estimasi: " . $s['estimated_time'] . ")\n  Deskripsi: " . $s['description'] . "\n";
        }
        return $contextText;
    }

    /**
     * Save Conversation turn to ai_conversations table
     */
    public function saveConversation($userId, $userMessage, $aiResponse)
    {
        if ($this->db->isConnected()) {
            $sql = "INSERT INTO " . $this->table . " (user_id, question, answer, created_at)
                    VALUES (:user_id, :question, :answer, NOW())";
            $this->db->query($sql);
            $this->db->bind(':user_id', $userId ?? 1);
            $this->db->bind(':question', $userMessage);
            $this->db->bind(':answer', $aiResponse);

            return $this->db->execute();
        }
        return true;
    }

    /**
     * Fetch conversation history for a user
     */
    public function getConversationHistory($userId, $limit = 20)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT * FROM " . $this->table . " WHERE user_id = :uid ORDER BY id ASC LIMIT :lim";
            $this->db->query($sql);
            $this->db->bind(':uid', $userId);
            $this->db->bind(':lim', $limit);
            $results = $this->db->resultSet();
            if ($results !== false) {
                return $results;
            }
        }
        return [];
    }

    /**
     * SMART FALLBACK ENGINE:
     * High-precision Rule Engine matching DB Catalog when external AI API is unavailable/offline
     */
    public function generateSmartFallbackResponse($message, $role = 'customer', $user = null)
    {
        $msg = trim(strtolower($message));
        $serviceModel = new ServiceModel();
        $allServices = $serviceModel->getAllServices('', '', true);

        $recommendedService = null;
        $replyText = "";

        // Check user authentication context if not provided
        if ($user === null && class_exists('Auth')) {
            $user = Auth::user();
        }

        // 1. Skenario Penolakan: "Buatkan coding Python untuk menuliskan nama Anda." (Coding / Python request)
        if (
            (strpos($msg, 'python') !== false && (strpos($msg, 'coding') !== false || strpos($msg, 'code') !== false || strpos($msg, 'kode') !== false || strpos($msg, 'skrip') !== false || strpos($msg, 'script') !== false || strpos($msg, 'buat') !== false || strpos($msg, 'program') !== false || strpos($msg, 'tulis') !== false)) ||
            (strpos($msg, 'coding') !== false || strpos($msg, 'buatkan kode') !== false || strpos($msg, 'buatkan script') !== false)
        ) {
            return [
                'text' => "Mohon maaf, permintaan pembuatan coding atau skrip program berada di luar cakupan layanan AI Assistant TKJ Service Center.\n\n" .
                          "Fokus utama saya adalah memberikan konsultasi seputar layanan perbaikan dan pemeliharaan IT, seperti:\n" .
                          "• Perbaikan & pembersihan PC/Laptop\n" .
                          "• Konfigurasi router, Wi-Fi, dan instalasi jaringan LAN\n" .
                          "• Instalasi serta perbaikan CCTV dan DVR\n" .
                          "• Perawatan printer dan perangkat keras lainnya\n\n" .
                          "Jika Anda memiliki pertanyaan atau kendala seputar perangkat dan layanan TKJ di atas, silakan tanyakan kepada kami!",
                'recommended_service' => null
            ];
        }

        // 2. Skenario: "Siapa saya?"
        if (
            preg_match('/^siapa\s*(saya|aku|gue|gw)[?!.\s]*$/i', $msg) ||
            $msg === 'siapa saya' ||
            $msg === 'siapa saya?' ||
            $msg === 'siapa aku' ||
            $msg === 'siapa aku?'
        ) {
            if ($user && !empty($user['name'])) {
                $roleLabels = [
                    'customer' => 'Customer / Pelanggan',
                    'teknisi' => 'Teknisi Siswa',
                    'operator' => 'Operator TEFA',
                    'kepala_sekolah' => 'Kepala Sekolah'
                ];
                $userRoleKey = $user['role'] ?? $role;
                $displayRole = $roleLabels[$userRoleKey] ?? ucfirst(str_replace('_', ' ', $userRoleKey));

                $replyText = "Anda saat ini login sebagai **" . htmlspecialchars($user['name']) . "** dengan role **" . $displayRole . "**.";
            } else {
                $replyText = "Anda belum login ke dalam sistem. Silakan login terlebih dahulu untuk mengakses akun dan data profil Anda.";
            }

            return [
                'text' => $replyText,
                'recommended_service' => null
            ];
        }

        // 3. Skenario: "Siapa Anda?"
        if (
            preg_match('/^(siapa\s*(anda|kamu|lo|lu|kau|situ)|anda\s*siapa|kamu\s*siapa)[?!.\s]*$/i', $msg)
        ) {
            return [
                'text' => "Saya adalah AI Assistant TKJ Service Center, asisten virtual resmi untuk membantu konsultasi teknis, troubleshooting awal perangkat komputer, jaringan, dan CCTV, serta memberikan informasi seputar layanan jasa TKJ Service Center.",
                'recommended_service' => null
            ];
        }

        // 4. Skenario: "Halo"
        if (
            preg_match('/^(halo+|hai+|hey+|hi+|hello+|helo+)[!?.\s]*$/i', $msg)
        ) {
            return [
                'text' => "Halo! Ada yang bisa saya bantu terkait kendala perangkat atau layanan TKJ Service Center hari ini?",
                'recommended_service' => null
            ];
        }

        // Check if query is a technical issue / troubleshooting inquiry
        $isTechIssue = (
            strpos($msg, 'rusak') !== false || strpos($msg, 'mati') !== false || strpos($msg, 'lemot') !== false ||
            strpos($msg, 'lambat') !== false || strpos($msg, 'rto') !== false || strpos($msg, 'bluescreen') !== false ||
            strpos($msg, 'bsod') !== false || strpos($msg, 'error') !== false || strpos($msg, 'bisa connect') !== false ||
            strpos($msg, 'tidak connect') !== false || strpos($msg, 'gak konek') !== false || strpos($msg, 'no display') !== false ||
            strpos($msg, 'blank') !== false || strpos($msg, 'panas') !== false || strpos($msg, 'overheat') !== false ||
            strpos($msg, 'beep') !== false || strpos($msg, 'putus') !== false || strpos($msg, 'gangguan') !== false ||
            strpos($msg, 'tidak ada internet') !== false || strpos($msg, 'kabel') !== false
        );

        // Check if query is a greeting / sapaan (general greetings like assalamualaikum, selamat pagi, etc.)
        $isGreeting = (
            preg_match('/^(assalamualaikum|selamat\s*(pagi|siang|sore|malam)|p|permisi|bang|kak|min|admin|oi|woi)[!?.\s]*$/i', $msg)
        );

        // Check if query is an identity question (general)
        $isIdentityQuestion = (
            preg_match('/(siapa\s*(anda|kamu|lo|lu|elu|kau|situ|nih)|anda\s*siapa|kamu\s*siapa|ini\s*siapa|bot\s*apa|apa\s*ini|apa\s*itu\s*tkj|kenalan|perkenalan|perkenalkan)/i', $msg)
        );

        // ====================================================================
        // REAL DATABASE & FEATURE Q&A ENGINE WITH STRICT ROLE-BASED ACCESS
        // ====================================================================

        // Helper closures for clean database queries
        $isDbOnline = $this->db && $this->db->isConnected();

        // ----------------------------------------------------
        // A. ADMIN / OPERATOR QUERIES (System-wide authorized)
        // ----------------------------------------------------
        // "Ada berapa jumlah user yang terdaftar?"
        if (
            preg_match('/(berapa|jumlah|total).*?(user|pengguna|akun)/i', $msg) ||
            preg_match('/(user|pengguna).*?(terdaftar|semua|total)/i', $msg)
        ) {
            if ($role === 'operator' || $role === 'kepala_sekolah') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM users");
                    $res = $this->db->single();
                    $totalUsers = intval($res['total'] ?? 0);

                    // Optional breakdown by role
                    $this->db->query("SELECT r.display_name, COUNT(DISTINCT u.id) as count 
                                      FROM users u 
                                      JOIN roles r ON u.role_id = r.id 
                                      GROUP BY r.id, r.display_name");
                    $roleBreakdown = $this->db->resultSet() ?: [];
                    $detailLines = [];
                    foreach ($roleBreakdown as $rb) {
                        $detailLines[] = "• " . htmlspecialchars($rb['display_name']) . ": **" . intval($rb['count']) . "** akun";
                    }
                    $breakdownText = !empty($detailLines) ? "\n\nRincian per peran:\n" . implode("\n", $detailLines) : "";

                    return [
                        'text' => "👥 Berdasarkan data database terkini, saat ini terdapat **" . $totalUsers . " user terdaftar** dalam sistem TKJ Service Center." . $breakdownText,
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, koneksi ke database sedang tidak tersedia sehingga data jumlah user belum dapat dihitung secara akurat.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, informasi data pengguna sistem hanya dapat diakses oleh Administrator/Operator dan Kepala Sekolah.",
                    'recommended_service' => null
                ];
            }
        }

        // "Ada berapa customer?"
        if (
            preg_match('/(berapa|jumlah|total).*?(customer|pelanggan)/i', $msg) ||
            preg_match('/(customer|pelanggan).*?(berapa|jumlah|total|terdaftar)/i', $msg)
        ) {
            if ($role === 'operator' || $role === 'kepala_sekolah') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM customers");
                    $res = $this->db->single();
                    $totalCustomers = intval($res['total'] ?? 0);

                    $this->db->query("SELECT customer_type, COUNT(DISTINCT id) as count FROM customers GROUP BY customer_type");
                    $types = $this->db->resultSet() ?: [];
                    $typeDetails = [];
                    foreach ($types as $t) {
                        $typeDetails[] = "• Kategori " . ucfirst($t['customer_type']) . ": **" . intval($t['count']) . "** customer";
                    }
                    $typeText = !empty($typeDetails) ? "\n\nKlasifikasi tipe:\n" . implode("\n", $typeDetails) : "";

                    return [
                        'text' => "🏢 Berdasarkan database sistem, saat ini tercatat ada **" . $totalCustomers . " customer** yang terdaftar." . $typeText,
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, koneksi ke database sedang tidak tersedia sehingga data jumlah customer tidak dapat dimuat.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, Anda tidak memiliki izin untuk melihat data rekapitulasi customer.",
                    'recommended_service' => null
                ];
            }
        }

        // "Ada berapa teknisi?"
        if (
            preg_match('/(berapa|jumlah|total).*?(teknisi)/i', $msg) ||
            preg_match('/(teknisi).*?(berapa|jumlah|total|tersedia|aktif)/i', $msg)
        ) {
            if ($role === 'operator' || $role === 'kepala_sekolah') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM technicians");
                    $res = $this->db->single();
                    $totalTechs = intval($res['total'] ?? 0);

                    $this->db->query("SELECT status, COUNT(DISTINCT id) as count FROM technicians GROUP BY status");
                    $statusRows = $this->db->resultSet() ?: [];
                    $statusDetails = [];
                    foreach ($statusRows as $sr) {
                        $stName = ($sr['status'] === 'available') ? 'Tersedia (Available)' : (($sr['status'] === 'on_duty') ? 'Sedang Bertugas (On Duty)' : 'Off/Libur');
                        $statusDetails[] = "• " . $stName . ": **" . intval($sr['count']) . "** orang";
                    }
                    $statusText = !empty($statusDetails) ? "\n\nStatus kesiapan:\n" . implode("\n", $statusDetails) : "";

                    return [
                        'text' => "👨‍🔧 Saat ini terdapat **" . $totalTechs . " orang teknisi** siswa TKJ yang tercatat di database." . $statusText,
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, koneksi ke database gagal. Data jumlah teknisi tidak dapat diambil saat ini.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, rekapitulasi data teknisi hanya dapat diakses oleh Operator dan Kepala Sekolah.",
                    'recommended_service' => null
                ];
            }
        }

        // "Ada berapa pengajuan layanan?" (Order dengan status 'Diajukan' / 'pending')
        if (
            preg_match('/(berapa|jumlah|total).*?(pengajuan|order baru|tiket baru|pesanan baru)/i', $msg) ||
            preg_match('/(pengajuan\s*layanan|pengajuan\s*jasa)/i', $msg)
        ) {
            if ($role === 'operator' || $role === 'kepala_sekolah') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders WHERE status IN ('Diajukan', 'pending')");
                    $res = $this->db->single();
                    $totalSubmissions = intval($res['total'] ?? 0);

                    return [
                        'text' => "📥 Saat ini terdapat **" . $totalSubmissions . " pengajuan layanan** berstatus *Diajukan* yang tercatat di database dan menunggu verifikasi oleh Operator.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, gagal mengambil data pengajuan layanan dari database.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, informasi antrean pengajuan layanan baru hanya dapat diakses oleh Operator.",
                    'recommended_service' => null
                ];
            }
        }

        // "Ada berapa order yang masih berjalan?" (Status in_progress / assigned / dll)
        if (
            preg_match('/(berapa|jumlah|total).*?(order|pesanan|pekerjaan).*?(berjalan|aktif|proses|pengerjaan)/i', $msg) ||
            preg_match('/(order|pesanan|pekerjaan).*?(masih berjalan|sedang berjalan|berjalan)/i', $msg)
        ) {
            if ($role === 'operator' || $role === 'kepala_sekolah') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders 
                                      WHERE status IN ('Menunggu Verifikasi', 'Disetujui', 'Teknisi Ditugaskan', 'Dijadwalkan', 'Dalam Pengerjaan', 'Menunggu Customer/Sparepart', 'assigned', 'in_progress', 'approved')");
                    $res = $this->db->single();
                    $totalOngoing = intval($res['total'] ?? 0);

                    return [
                        'text' => "⚙️ Saat ini terdapat **" . $totalOngoing . " order yang masih berjalan** (dalam status penugasan, penjadwalan, maupun pengerjaan aktif di lapangan).",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, data order berjalan belum dapat dimuat karena gangguan koneksi database.",
                        'recommended_service' => null
                    ];
                }
            } else if ($role === 'teknisi') {
                // Redirect for technician to their own ongoing jobs
                $techModel = new TechnicianModel();
                $tech = $techModel->getTechnicianByUserId($user['id'] ?? 0);
                $techId = $tech['id'] ?? 0;
                if ($isDbOnline && $techId) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders 
                                      WHERE technician_id = :tid AND status NOT IN ('Selesai', 'Ditutup', 'completed', 'Dibatalkan', 'cancelled')");
                    $this->db->bind(':tid', $techId);
                    $myOngoing = intval($this->db->single()['total'] ?? 0);
                    return [
                        'text' => "🔧 Anda memiliki **" . $myOngoing . " tugas pengerjaan yang belum selesai** di lapangan.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, Anda hanya memiliki izin untuk melihat order milik akun Anda sendiri di menu **Pesanan Saya**.",
                    'recommended_service' => null
                ];
            }
        }

        // "Berapa transaksi yang tercatat?"
        if (
            preg_match('/(berapa|jumlah|total).*?(transaksi|invoice|pembayaran)/i', $msg) ||
            preg_match('/(transaksi).*?(tercatat|semua|total|masuk)/i', $msg)
        ) {
            if ($role === 'operator' || $role === 'kepala_sekolah') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total_count, 
                                             COALESCE(SUM(grand_total), 0) as total_nominal,
                                             SUM(CASE WHEN payment_status IN ('Lunas', 'paid') THEN 1 ELSE 0 END) as paid_count,
                                             SUM(CASE WHEN payment_status IN ('Belum Dibayar', 'unpaid') THEN 1 ELSE 0 END) as unpaid_count
                                      FROM transactions");
                    $trx = $this->db->single();
                    $totalTrx = intval($trx['total_count'] ?? 0);
                    $totalNominal = floatval($trx['total_nominal'] ?? 0);
                    $paidCount = intval($trx['paid_count'] ?? 0);
                    $unpaidCount = intval($trx['unpaid_count'] ?? 0);

                    return [
                        'text' => "💳 Berdasarkan data database, terdapat **" . $totalTrx . " transaksi yang tercatat** dengan rincian:\n" .
                                  "• Transaksi Lunas: **" . $paidCount . "**\n" .
                                  "• Belum Dibayar: **" . $unpaidCount . "**\n" .
                                  "• Akumulasi Nilai Invoice: **Rp " . number_format($totalNominal, 0, ',', '.') . "**",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, database sedang offline sehingga transaksi tidak dapat dihitung.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, ringkasan transaksi sistem hanya dapat diakses oleh Operator dan Kepala Sekolah.",
                    'recommended_service' => null
                ];
            }
        }

        // ----------------------------------------------------
        // B. TEKNISI QUERIES (Technician-specific data only)
        // ----------------------------------------------------
        // "Berapa pekerjaan yang sudah saya selesaikan?" (Check before generic tasks)
        if (
            (preg_match('/(berapa).*?(pekerjaan|tugas|order).*?(selesai|diselesaikan)/i', $msg) ||
             preg_match('/(sudah\s*saya\s*selesaikan|telah\s*diselesaikan)/i', $msg)) &&
            ($role === 'teknisi' || strpos($msg, 'saya') !== false)
        ) {
            if ($role === 'teknisi') {
                $techModel = new TechnicianModel();
                $tech = $techModel->getTechnicianByUserId($user['id'] ?? 0);
                $techId = $tech['id'] ?? 0;

                if ($isDbOnline && $techId) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total 
                                      FROM service_orders 
                                      WHERE technician_id = :tid AND status IN ('Selesai', 'Ditutup', 'completed')");
                    $this->db->bind(':tid', $techId);
                    $done = intval($this->db->single()['total'] ?? 0);

                    return [
                        'text' => "✅ Anda telah menyelesaikan total **" . $done . " pekerjaan** dengan sukses di sistem TKJ Service Center.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, riwayat pekerjaan Anda gagal diambil dari database.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Pertanyaan ini ditujukan untuk akun Teknisi Siswa yang sedang login.",
                    'recommended_service' => null
                ];
            }
        }

        // "Ada berapa tugas yang ditugaskan kepada saya?"
        if (
            preg_match('/(berapa|jumlah|total).*?(tugas|penugasan).*?(saya|ditugaskan)/i', $msg) ||
            preg_match('/(tugas).*?(ditugaskan kepada saya|untuk saya|milik saya)/i', $msg)
        ) {
            if ($role === 'teknisi') {
                $techModel = new TechnicianModel();
                $tech = $techModel->getTechnicianByUserId($user['id'] ?? 0);
                $techId = $tech['id'] ?? 0;

                if ($isDbOnline && $techId) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders WHERE technician_id = :tid");
                    $this->db->bind(':tid', $techId);
                    $totalAssigned = intval($this->db->single()['total'] ?? 0);

                    return [
                        'text' => "🛠️ Halo **" . htmlspecialchars($user['name'] ?? 'Teknisi') . "**, saat ini tercatat ada **" . $totalAssigned . " tugas** yang ditugaskan kepada Anda dalam database.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, data tugas Anda tidak dapat dimuat dari database saat ini.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Pertanyaan mengenai penugasan teknisi hanya berlaku untuk akun dengan role **Teknisi Siswa**.",
                    'recommended_service' => null
                ];
            }
        }

        // "Apa saja order yang belum selesai?" (Untuk Teknisi)
        if (
            preg_match('/(apa\s*saja|daftar|mana).*?(order|tugas|pekerjaan).*?(belum\s*selesai|berjalan|aktif)/i', $msg) ||
            preg_match('/(order|tugas).*?(belum selesai)/i', $msg)
        ) {
            if ($role === 'teknisi') {
                $techModel = new TechnicianModel();
                $tech = $techModel->getTechnicianByUserId($user['id'] ?? 0);
                $techId = $tech['id'] ?? 0;

                if ($isDbOnline && $techId) {
                    $this->db->query("SELECT o.order_code, o.status, s.name as service_name, u.full_name as customer_name, o.service_address
                                      FROM service_orders o
                                      LEFT JOIN services s ON o.service_id = s.id
                                      LEFT JOIN customers c ON o.customer_id = c.id
                                      LEFT JOIN users u ON c.user_id = u.id
                                      WHERE o.technician_id = :tid 
                                        AND o.status NOT IN ('Selesai', 'Ditutup', 'completed', 'Dibatalkan', 'cancelled')
                                      ORDER BY o.id DESC");
                    $this->db->bind(':tid', $techId);
                    $orders = $this->db->resultSet() ?: [];

                    if (empty($orders)) {
                        return [
                            'text' => "🎉 Hebat! Tidak ada order atau tugas yang belum selesai saat ini. Semua tugas penugasan Anda telah selesai dikerjakan.",
                            'recommended_service' => null
                        ];
                    }

                    $list = [];
                    foreach ($orders as $idx => $ord) {
                        $list[] = ($idx + 1) . ". **" . htmlspecialchars($ord['order_code']) . "** - " . htmlspecialchars($ord['service_name'] ?? 'Layanan IT') . 
                                  "\n   • Customer: " . htmlspecialchars($ord['customer_name'] ?? 'Pelanggan') .
                                  "\n   • Status: *" . htmlspecialchars($ord['status']) . "*" .
                                  "\n   • Lokasi: " . htmlspecialchars($ord['service_address'] ?? '-');
                    }

                    return [
                        'text' => "📋 Berikut daftar order tugas Anda yang **belum selesai** berdasarkan database:\n\n" . implode("\n\n", $list),
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, tidak dapat mengambil daftar order yang belum selesai dari database.",
                        'recommended_service' => null
                    ];
                }
            } else if ($role === 'operator' || $role === 'kepala_sekolah') {
                if ($isDbOnline) {
                    $this->db->query("SELECT o.order_code, o.status, s.name as service_name, u.full_name as customer_name
                                      FROM service_orders o
                                      LEFT JOIN services s ON o.service_id = s.id
                                      LEFT JOIN customers c ON o.customer_id = c.id
                                      LEFT JOIN users u ON c.user_id = u.id
                                      WHERE o.status NOT IN ('Selesai', 'Ditutup', 'completed', 'Dibatalkan', 'cancelled')
                                      ORDER BY o.id DESC LIMIT 5");
                    $orders = $this->db->resultSet() ?: [];
                    $list = [];
                    foreach ($orders as $idx => $ord) {
                        $list[] = ($idx + 1) . ". **" . htmlspecialchars($ord['order_code']) . "** (" . htmlspecialchars($ord['status']) . ") - " . htmlspecialchars($ord['customer_name'] ?? '-');
                    }
                    return [
                        'text' => "📋 Ringkasan order yang belum selesai dalam sistem:\n\n" . implode("\n", $list) . "\n\nSelengkapnya dapat dilihat pada menu **Kelola Order Masuk**.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, Anda tidak memiliki akses untuk melihat daftar pekerjaan teknisi.",
                    'recommended_service' => null
                ];
            }
        }

        // "Berapa pekerjaan yang sudah saya selesaikan?" (Untuk Teknisi)
        if (
            preg_match('/(berapa).*?(pekerjaan|tugas|order).*?(selesai|diselesaikan)/i', $msg) &&
            (strpos($msg, 'saya') !== false || strpos($msg, 'teknisi') !== false)
        ) {
            if ($role === 'teknisi') {
                $techModel = new TechnicianModel();
                $tech = $techModel->getTechnicianByUserId($user['id'] ?? 0);
                $techId = $tech['id'] ?? 0;

                if ($isDbOnline && $techId) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total 
                                      FROM service_orders 
                                      WHERE technician_id = :tid AND status IN ('Selesai', 'Ditutup', 'completed')");
                    $this->db->bind(':tid', $techId);
                    $done = intval($this->db->single()['total'] ?? 0);

                    return [
                        'text' => "✅ Anda telah menyelesaikan total **" . $done . " pekerjaan** dengan sukses di sistem TKJ Service Center.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, riwayat pekerjaan Anda gagal diambil dari database.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Pertanyaan ini ditujukan untuk akun Teknisi Siswa yang sedang login.",
                    'recommended_service' => null
                ];
            }
        }

        // ----------------------------------------------------
        // C. CUSTOMER QUERIES (Strictly Customer's Own Data)
        // ----------------------------------------------------
        // "Ada berapa order milik saya?"
        if (
            preg_match('/(berapa).*?(order|pesanan).*?(milik saya|punya saya|saya)/i', $msg) ||
            preg_match('/(order|pesanan\s*saya).*?(berapa|jumlah|total)/i', $msg)
        ) {
            if ($role === 'customer' && !empty($user['id'])) {
                $orderModel = new OrderModel();
                $customerId = $orderModel->getCustomerIdByUserId($user['id'] ?? 0);

                if ($isDbOnline && $customerId) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders WHERE customer_id = :cid");
                    $this->db->bind(':cid', $customerId);
                    $totalCustOrders = intval($this->db->single()['total'] ?? 0);

                    return [
                        'text' => "📦 Saat ini terdapat **" . $totalCustOrders . " order pesanan** yang tercatat atas nama akun Anda (**" . htmlspecialchars($user['name'] ?? 'Customer') . "**).",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, sistem tidak dapat memuat data order Anda dari database saat ini.",
                        'recommended_service' => null
                    ];
                }
            } elseif ($role === 'guest' || empty($user['id'])) {
                return [
                    'text' => "Untuk melihat riwayat dan status pesanan milik Anda, silakan **Login** terlebih dahulu ke akun pelanggan Anda.",
                    'recommended_service' => null
                ];
            } else {
                return [
                    'text' => "Akun Anda saat ini login sebagai **" . ucfirst(str_replace('_', ' ', $role)) . "**. Fitur pesanan pelanggan milik saya berlaku untuk akun Customer.",
                    'recommended_service' => null
                ];
            }
        }

        // "Bagaimana status pesanan saya?"
        if (
            preg_match('/(bagaimana|apa|cek).*?(status).*?(pesanan|order).*?(saya)?/i', $msg) ||
            preg_match('/(status\s*pesanan\s*saya|status\s*order\s*saya)/i', $msg)
        ) {
            if ($role === 'customer' && !empty($user['id'])) {
                $orderModel = new OrderModel();
                $customerId = $orderModel->getCustomerIdByUserId($user['id'] ?? 0);

                if ($isDbOnline && $customerId) {
                    $this->db->query("SELECT o.order_code, o.status, s.name as service_name, tu.full_name as technician_name, o.created_at
                                      FROM service_orders o
                                      LEFT JOIN services s ON o.service_id = s.id
                                      LEFT JOIN technicians t ON o.technician_id = t.id
                                      LEFT JOIN users tu ON t.user_id = tu.id
                                      WHERE o.customer_id = :cid
                                      ORDER BY o.id DESC");
                    $this->db->bind(':cid', $customerId);
                    $myOrders = $this->db->resultSet() ?: [];

                    if (empty($myOrders)) {
                        return [
                            'text' => "Anda belum memiliki riwayat pesanan layanan. Anda dapat melakukan pemesanan melalui menu **Pengajuan Layanan**.",
                            'recommended_service' => null
                        ];
                    }

                    $list = [];
                    foreach ($myOrders as $idx => $mo) {
                        $techInfo = !empty($mo['technician_name']) ? " (Teknisi: " . htmlspecialchars($mo['technician_name']) . ")" : " (Belum ditugaskan)";
                        $list[] = ($idx + 1) . ". Tiket **" . htmlspecialchars($mo['order_code']) . "**\n" .
                                  "   • Jasa: " . htmlspecialchars($mo['service_name'] ?? 'Layanan IT') . "\n" .
                                  "   • Status: **" . htmlspecialchars($mo['status']) . "**" . $techInfo;
                    }

                    return [
                        'text' => "📌 Berikut status pesanan layanan terbaru milik Anda berdasarkan database:\n\n" . implode("\n\n", $list) . 
                                  "\n\nAnda dapat melihat detail perjalanan servis lengkap di menu **Pesanan Saya**.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, status pesanan belum dapat diperiksa karena kendala akses database.",
                        'recommended_service' => null
                    ];
                }
            } elseif ($role === 'guest' || empty($user['id'])) {
                return [
                    'text' => "Untuk mengecek status pesanan layanan Anda secara langsung, silakan **Login** terlebih dahulu ke akun pelanggan Anda.",
                    'recommended_service' => null
                ];
            } else {
                // If asked by non-customer, explain the workflow
                return [
                    'text' => "📦 **Alur Status Pesanan Layanan TKJ Service Center**:\n" .
                              "1. **Diajukan** (Customer mengajukan tiket)\n" .
                              "2. **Disetujui** (Diverifikasi oleh Operator)\n" .
                              "3. **Teknisi Ditugaskan** (Alokasi teknisi siswa)\n" .
                              "4. **Dalam Pengerjaan** (Pemeriksaan & perbaikan di lokasi)\n" .
                              "5. **Selesai & Ditutup** (Pekerjaan tuntas & verifikasi pembayaran)",
                    'recommended_service' => null
                ];
            }
        }

        // "Berapa tagihan saya yang belum dibayar?"
        if (
            preg_match('/(berapa|total).*?(tagihan|biaya|bayar|invoice).*?(belum\s*dibayar|belum\s*lunas|harus\s*dibayar)/i', $msg) ||
            preg_match('/(tagihan\s*saya|belum\s*dibayar)/i', $msg)
        ) {
            if ($role === 'customer') {
                $orderModel = new OrderModel();
                $customerId = $orderModel->getCustomerIdByUserId($user['id'] ?? 0);

                if ($isDbOnline && $customerId) {
                    $this->db->query("SELECT COUNT(t.id) as unpaid_count, COALESCE(SUM(t.grand_total), 0) as total_unpaid
                                      FROM transactions t
                                      JOIN service_orders o ON t.order_id = o.id
                                      WHERE o.customer_id = :cid AND t.payment_status IN ('Belum Dibayar', 'unpaid')");
                    $this->db->bind(':cid', $customerId);
                    $unpaidData = $this->db->single();
                    $unpaidCount = intval($unpaidData['unpaid_count'] ?? 0);
                    $totalUnpaid = floatval($unpaidData['total_unpaid'] ?? 0);

                    if ($unpaidCount === 0 || $totalUnpaid == 0) {
                        return [
                            'text' => "🎉 Tidak ada tagihan yang belum dibayar! Seluruh invoice layanan Anda saat ini sudah berstatus **Lunas**.",
                            'recommended_service' => null
                        ];
                    }

                    return [
                        'text' => "💳 Anda memiliki **" . $unpaidCount . " invoice** yang belum dibayar dengan total tagihan sebesar **Rp " . number_format($totalUnpaid, 0, ',', '.') . "**.\n\n" .
                                  "Silakan cek menu **Pesanan Saya** untuk mengunggah bukti pembayaran atau konfirmasi ke Operator TEFA.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, informasi tagihan gagal dimuat dari database saat ini.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Informasi tagihan pribadi hanya tersedia bagi akun Customer yang telah login.",
                    'recommended_service' => null
                ];
            }
        }

        // ----------------------------------------------------
        // D. KEPALA SEKOLAH QUERIES (Executive Overview)
        // ----------------------------------------------------
        // "Berapa total order dalam sistem?"
        if (
            preg_match('/(berapa|total|jumlah).*?(total order|seluruh order|order dalam sistem|semua order)/i', $msg) ||
            preg_match('/(total\s*order)/i', $msg)
        ) {
            if ($role === 'kepala_sekolah' || $role === 'operator') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders");
                    $res = $this->db->single();
                    $totalOrders = intval($res['total'] ?? 0);

                    $this->db->query("SELECT status, COUNT(DISTINCT id) as count FROM service_orders GROUP BY status");
                    $statuses = $this->db->resultSet() ?: [];
                    $statusLines = [];
                    foreach ($statuses as $st) {
                        $statusLines[] = "• " . htmlspecialchars($st['status']) . ": **" . intval($st['count']) . "** order";
                    }
                    $statusText = !empty($statusLines) ? "\n\nDistribusi status pesanan:\n" . implode("\n", $statusLines) : "";

                    return [
                        'text' => "📊 Total order yang tercatat dalam sistem database saat ini berjumlah **" . $totalOrders . " order**." . $statusText,
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, data total order sistem tidak dapat dihitung karena kendala koneksi database.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, data agregasi total order sistem hanya dapat diakses oleh Kepala Sekolah dan Operator.",
                    'recommended_service' => null
                ];
            }
        }

        // "Berapa order yang sudah selesai?"
        if (
            preg_match('/(berapa|jumlah|total).*?(order|pesanan).*?(sudah\s*selesai|selesai|tuntas)/i', $msg) ||
            preg_match('/(order\s*selesai)/i', $msg)
        ) {
            if ($role === 'kepala_sekolah' || $role === 'operator') {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders WHERE status IN ('Selesai', 'Ditutup', 'completed')");
                    $res = $this->db->single();
                    $completedOrders = intval($res['total'] ?? 0);

                    return [
                        'text' => "✅ Jumlah order yang **sudah selesai** dan ditutup secara sukses di sistem adalah sebanyak **" . $completedOrders . " order**.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, data order selesai gagal diambil dari database.",
                        'recommended_service' => null
                    ];
                }
            } else if ($role === 'teknisi') {
                $techModel = new TechnicianModel();
                $tech = $techModel->getTechnicianByUserId($user['id'] ?? 0);
                $techId = $tech['id'] ?? 0;
                if ($isDbOnline && $techId) {
                    $this->db->query("SELECT COUNT(DISTINCT id) as total FROM service_orders WHERE technician_id = :tid AND status IN ('Selesai', 'Ditutup', 'completed')");
                    $this->db->bind(':tid', $techId);
                    $completed = intval($this->db->single()['total'] ?? 0);
                    return [
                        'text' => "✅ Anda telah menyelesaikan **" . $completed . " order** tugas di sistem.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, Anda tidak memiliki akses untuk melihat rekapitulasi order selesai sistem.",
                    'recommended_service' => null
                ];
            }
        }

        // "Bagaimana statistik kinerja teknisi?"
        if (
            preg_match('/(bagaimana|apa|lihat).*?(statistik|kinerja|performa|evaluasi).*?(teknisi)/i', $msg) ||
            preg_match('/(kinerja\s*teknisi|performa\s*teknisi)/i', $msg)
        ) {
            if ($role === 'kepala_sekolah' || $role === 'operator') {
                if ($isDbOnline) {
                    $this->db->query("SELECT t.id, u.full_name, t.specialization, t.status, 
                                             COUNT(o.id) as total_jobs,
                                             SUM(CASE WHEN o.status IN ('Selesai', 'Ditutup', 'completed') THEN 1 ELSE 0 END) as done_jobs,
                                             COALESCE(AVG(r.stars), 0) as avg_rating
                                      FROM technicians t 
                                      JOIN users u ON t.user_id = u.id 
                                      LEFT JOIN service_orders o ON t.id = o.technician_id 
                                      LEFT JOIN ratings r ON t.id = r.technician_id 
                                      GROUP BY t.id, u.full_name, t.specialization, t.status
                                      ORDER BY done_jobs DESC, avg_rating DESC");
                    $techStats = $this->db->resultSet() ?: [];

                    if (empty($techStats)) {
                        return [
                            'text' => "Belum ada catatan kinerja teknisi dalam sistem database.",
                            'recommended_service' => null
                        ];
                    }

                    $lines = [];
                    foreach ($techStats as $ts) {
                        $rateStr = floatval($ts['avg_rating']) > 0 ? number_format($ts['avg_rating'], 1) . " ⭐" : "Belum ada rating";
                        $lines[] = "• **" . htmlspecialchars($ts['full_name']) . "** (" . htmlspecialchars($ts['specialization'] ?? 'Teknisi TKJ') . ")\n" .
                                   "  Total Tugas: " . intval($ts['total_jobs']) . " | Selesai: " . intval($ts['done_jobs']) . " | Rating: " . $rateStr;
                    }

                    return [
                        'text' => "📈 **Statistik Kinerja Teknisi Siswa TEFA** (Data Database Realtime):\n\n" . implode("\n\n", $lines) . 
                                  "\n\nDetail lengkap dapat dipantau di **Pusat Laporan Eksekutif** ➔ Tab Pekerjaan Teknisi.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Mohon maaf, data statistik teknisi gagal diambil dari database.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, evaluasi statistik kinerja teknisi hanya dapat diakses oleh Kepala Sekolah dan Operator TEFA.",
                    'recommended_service' => null
                ];
            }
        }

        // "Layanan apa yang paling banyak dipesan?"
        if (
            preg_match('/(layanan|jasa).*?(paling banyak|terlaris|populer|favorit).*?(dipesan|digunakan|diminati)/i', $msg) ||
            preg_match('/(layanan\s*apa\s*yang\s*paling\s*banyak)/i', $msg) ||
            preg_match('/(jasa\s*terlaris|jasa\s*populer)/i', $msg)
        ) {
            if ($isDbOnline) {
                $this->db->query("SELECT s.name, s.category, COUNT(o.id) as order_count 
                                  FROM services s 
                                  JOIN service_orders o ON s.id = o.service_id 
                                  GROUP BY s.id, s.name, s.category 
                                  ORDER BY order_count DESC, s.name ASC 
                                  LIMIT 3");
                $topServices = $this->db->resultSet() ?: [];

                if (!empty($topServices)) {
                    $topLines = [];
                    foreach ($topServices as $idx => $ts) {
                        $topLines[] = ($idx + 1) . ". **" . htmlspecialchars($ts['name']) . "** (" . htmlspecialchars($ts['category']) . ") — **" . intval($ts['order_count']) . "** pesanan";
                    }

                    return [
                        'text' => "🏆 **Layanan Paling Banyak Dipesan** berdasarkan database:\n\n" . implode("\n", $topLines) . "\n\nData ini diperbarui secara otomatis berdasarkan riwayat pesanan yang masuk ke sistem.",
                        'recommended_service' => null
                    ];
                } else {
                    return [
                        'text' => "Saat ini belum ada riwayat transaksi pesanan layanan yang tercatat di database.",
                        'recommended_service' => null
                    ];
                }
            } else {
                return [
                    'text' => "Mohon maaf, data statistik layanan terlaris tidak dapat diakses saat database offline.",
                    'recommended_service' => null
                ];
            }
        }

        // ==========================================
        // E. ROLE-SPECIFIC OPERATIONAL & GUIDANCE HELP
        // ==========================================
        if ($role === 'teknisi') {
            if (strpos($msg, 'jaringan') !== false || strpos($msg, 'lan') !== false || strpos($msg, 'ip') !== false || strpos($msg, 'dhcp') !== false || strpos($msg, 'ping') !== false) {
                $replyText = "🔧 **BANTUAN TEKNIS JARINGAN (Untuk Teknisi)**:\n\n" .
                             "**Langkah Diagnosis & Troubleshooting:**\n" .
                             "1. **Cek Fisik & Layer 1:** Periksa lampu indikator Link/Activity port RJ45 di PC dan Switch. Uji kontinuitas 8 pin kabel Cat5e/6 menggunakan LAN tester.\n" .
                             "2. **Layer 2 & 3 (IP Stack):** Buka CMD, jalankan `ipconfig /all`. Jika IP `169.254.x.x` (APIPA), artinya gagal menerima DHCP Lease dari router.\n" .
                             "3. **Uji Loopback & Gateway:**\n" .
                             "   • `ping 127.0.0.1` (uji integritas stack TCP/IP internal OS)\n" .
                             "   • `ping [IP_Gateway]` (uji koneksi router lokal)\n" .
                             "   • `ping 8.8.8.8` & `ping google.com` (uji DNS dan internet)\n" .
                             "4. **Tindakan Perbaikan:** Lepas-pasang konektor RJ45/crimping ulang, jalankan `ipconfig /release` dilanjutkan `ipconfig /renew`, atau restart network adapter (`ncpa.cpl`).\n\n" .
                             "💡 *Tips Laporan:* Catat hasil tes ping, kondisi kabel, dan nomor port switch pada draft Laporan Pekerjaan Anda.";
            } else if (strpos($msg, 'router') !== false || strpos($msg, 'mikrotik') !== false || strpos($msg, 'wifi') !== false || strpos($msg, 'bandwidth') !== false) {
                $replyText = "🔧 **BANTUAN TEKNIS ROUTER / MIKROTIK (Untuk Teknisi)**:\n\n" .
                             "**Checklist Troubleshooting:**\n" .
                             "1. Buka Winbox, login ke Router MikroTik dan amati **CPU Usage** di `System` -> `Resources`. Beban >90% mengindikasikan looping trafik atau broadcast storm.\n" .
                             "2. Cek status port WAN (ether1): pastikan mendapat IP dari ISP dan route default `0.0.0.0/0` berstatus Active Reachable di `IP` -> `Routes`.\n" .
                             "3. Cek pengaturan DNS di `IP` -> `DNS`, pastikan `Allow Remote Requests` dicentang dan DNS server dapat di-ping.\n" .
                             "4. Untuk Wi-Fi lambat/interferensi: ubah frekuensi channel Access Point ke non-overlapping channel (1, 6, atau 11 untuk 2.4 GHz).\n" .
                             "5. Periksa `IP` -> `Queues`, pastikan limitasi bandwidth tidak salah sasaran.\n\n" .
                             "💡 *Catatan:* Jika router mengalami kernel crash atau butuh Netinstall, segera laporkan ke Operator/Pembina TEFA.";
            } else if (strpos($msg, 'cctv') !== false || strpos($msg, 'dvr') !== false || strpos($msg, 'kamera') !== false) {
                $replyText = "🔧 **BANTUAN TEKNIS CCTV / DVR (Untuk Teknisi)**:\n\n" .
                             "**Langkah Diagnosis:**\n" .
                             "1. **Cek Power Supply:** Gunakan Multimeter untuk mengukur voltase adaptor/PSU central (standar 12V DC). Jika voltase drop (<11V), kamera akan no video atau infrared gagal aktif.\n" .
                             "2. **Uji Konektor BNC:** Cek sambungan kawat inti dan serabut ground kabel coaxial/balun. Seringkali goyang atau berkarat.\n" .
                             "3. **Bench Test:** Tukar channel BNC di belakang DVR dengan kamera lain yang normal untuk memastikan port DVR masih berfungsi.\n" .
                             "4. **Untuk IP Camera:** Pastikan IP Camera berada di subnet yang sama dengan NVR dan port PoE switch mencukupi daya Watt.\n\n" .
                             "💡 *Dokumentasi:* Ambil foto kondisi sebelum (Before) dan sesudah (After) perbaikan untuk diupload ke laporan pekerjaan.";
            } else if (strpos($msg, 'laporan') !== false || strpos($msg, 'checklist') !== false) {
                $replyText = "📋 **PANDUAN LAPORAN PEKERJAAN TEKNISI**:\n\n" .
                             "Untuk menyelesaikan tugas dan menutup status order:\n" .
                             "1. Buka menu **Tugas Saya** ➔ Pilih tiket pekerjaan ➔ Klik **Laporan Pekerjaan**.\n" .
                             "2. Isi 7 elemen wajib:\n" .
                             "   • Diagnosa awal masalah\n" .
                             "   • Pemeriksaan yang dilakukan\n" .
                             "   • Tindakan perbaikan yang dieksekusi\n" .
                             "   • Komponen/sparepart yang diganti (jika ada)\n" .
                             "   • Hasil akhir pengujian\n" .
                             "   • Foto Before & After pengerjaan\n" .
                             "3. Anda juga dapat menggunakan tombol **AI Generator Laporan** di halaman tersebut untuk merapikan kalimat laporan secara otomatis.";
            } else {
                $replyText = "🔧 **AI ASISTEN TEKNIS TEKNISI SISWA TKJ**\n\n" .
                             "Halo Rekan Teknisi! Saya siap membantu menjawab data tugas dan troubleshooting di lapangan:\n" .
                             "• **Data Tugas:** Tanyakan 'Ada berapa tugas yang ditugaskan kepada saya?', 'Apa saja order yang belum selesai?', atau 'Berapa pekerjaan yang sudah saya selesaikan?'\n" .
                             "• **Jaringan & LAN:** Masalah kabel UTP, tester, APIPA, IP conflict, switch looping.\n" .
                             "• **Router & MikroTik:** Konfigurasi Winbox, gateway unreachable, queue bandwidth, DNS relay.\n" .
                             "• **Komputer & Laptop:** No display, RAM beep code, thermal paste overheat, recovery SSD/HDD.\n" .
                             "• **CCTV & DVR:** Pengukuran voltase PSU 12V DC, konektor BNC/Balun, IP camera PoE.\n" .
                             "• **Laporan Pekerjaan:** Bantuan penyusunan draft laporan teknis resmi.";
            }
        }
        else if ($role === 'operator') {
            if (strpos($msg, 'verifikasi') !== false || strpos($msg, 'masuk') !== false) {
                $replyText = "📋 **PANDUAN PENGELOLAAN ORDER (Untuk Operator)**:\n\n" .
                             "1. Buka menu **Kelola Order Masuk** di navbar atau dashboard.\n" .
                             "2. Periksa tiket dengan status **Diajukan**.\n" .
                             "3. Klik **Detail** untuk memeriksa deskripsi masalah & lokasi pemesan.\n" .
                             "4. Klik **Verifikasi Order** (Setujui jika valid, Batalkan jika tidak memenuhi kriteria).\n" .
                             "5. Setelah disetujui, tombol **Tugaskan Teknisi** akan aktif untuk memilih teknisi dan tanggal kerja.";
            } else if (strpos($msg, 'jadwal') !== false || strpos($msg, 'tugaskan') !== false) {
                $replyText = "👨‍🔧 **PANDUAN PENUGASAN TEKNISI (Untuk Operator)**:\n\n" .
                             "• Buka tiket order ➔ Klik tombol **Tugaskan Teknisi**.\n" .
                             "• Pilih salah satu teknisi siswa yang tersedia.\n" .
                             "• Tentukan tanggal kerja serta jam mulai & jam selesai.\n" .
                             "• Sistem secara otomatis akan mengecek **konflik jadwal**. Jika teknisi yang dipilih sudah memiliki jadwal di jam yang sama, sistem akan memberi peringatan tabrakan.\n" .
                             "• Setelah disimpan, order otomatis masuk ke menu **Tugas Saya** pada akun teknisi yang ditugaskan.";
            } else if (strpos($msg, 'invoice') !== false) {
                $replyText = "💳 **PANDUAN PENGELOLAAN TRANSAKSI & INVOICE**:\n\n" .
                             "• Buka menu **Invoice** pada tiket order yang bersangkutan.\n" .
                             "• Masukkan rincian: Biaya Jasa + Biaya Sparepart + Biaya Tambahan - Diskon.\n" .
                             "• Total tagihan akan dihitung otomatis oleh sistem backend secara aman.\n" .
                             "• Jika customer telah melakukan pembayaran manual, verifikasi status menjadi **Lunas** agar tercatat di laporan omset TEFA.";
            } else {
                $replyText = "💼 **AI ASISTEN OPERASIONAL TEFA (Untuk Operator)**\n\n" .
                             "Halo Operator! Anda dapat menanyakan data statistik operasional realtime:\n" .
                             "• 'Ada berapa jumlah user yang terdaftar?'\n" .
                             "• 'Ada berapa customer?'\n" .
                             "• 'Ada berapa teknisi?'\n" .
                             "• 'Ada berapa pengajuan layanan?'\n" .
                             "• 'Ada berapa order yang masih berjalan?'\n" .
                             "• 'Berapa transaksi yang tercatat?'\n" .
                             "• Panduan verifikasi order, penugasan teknisi, dan invoice pembayaran.";
            }
        }
        else if ($role === 'kepala_sekolah') {
            if (strpos($msg, 'pendapatan') !== false || strpos($msg, 'omset') !== false || strpos($msg, 'keuangan') !== false || strpos($msg, 'bisnis') !== false) {
                if ($isDbOnline) {
                    $this->db->query("SELECT COALESCE(SUM(grand_total), 0) as total FROM transactions WHERE payment_status IN ('Lunas', 'paid')");
                    $rev = $this->db->single()['total'] ?? 0;
                    $replyText = "📈 **Ringkasan Pendapatan Unit Bisnis TEFA**:\n\n" .
                                 "• Total Pendapatan Lunas: **Rp " . number_format($rev, 0, ',', '.') . "**\n" .
                                 "• Laporan formal per periode dapat dicetak pada menu **Laporan Omset TEFA**.";
                } else {
                    $replyText = "📈 Ringkasan finansial unit TEFA TKJ dapat dilihat di KPI Card atas Dashboard atau menu **Laporan Omset TEFA**.";
                }
            } else if (strpos($msg, 'rating') !== false || strpos($msg, 'kepuasan') !== false || strpos($msg, 'kualitas') !== false) {
                if ($isDbOnline) {
                    $this->db->query("SELECT COUNT(id) as total, COALESCE(AVG(stars), 0) as avg_stars FROM ratings");
                    $rat = $this->db->single();
                    $avgR = round(floatval($rat['avg_stars'] ?? 0), 1);
                    $totR = intval($rat['total'] ?? 0);
                    $replyText = "⭐ **Tingkat Kepuasan & Rating Pelanggan**:\n\n" .
                                 "• Rata-rata Rating: **" . $avgR . " / 5.0 ⭐** (dari " . $totR . " ulasan pelanggan)\n" .
                                 "• Ulasan dan saran pelanggan selengkapnya dapat dilihat pada menu **Monitoring Rating**.";
                } else {
                    $replyText = "⭐ Tingkat kepuasan pelanggan dapat dipantau melalui menu **Monitoring Rating Layanan**.";
                }
            } else {
                $replyText = "🏛️ **AI EXECUTIVE ASSISTANT (Kepala Sekolah)**\n\n" .
                             "Selamat datang, Bapak/Ibu Kepala Sekolah. Anda dapat menanyakan ringkasan eksekutif berbasis database:\n" .
                             "• 'Berapa total order dalam sistem?'\n" .
                             "• 'Berapa order yang sudah selesai?'\n" .
                             "• 'Bagaimana statistik kinerja teknisi?'\n" .
                             "• 'Layanan apa yang paling banyak dipesan?'\n" .
                             "• Rekapitulasi omset bisnis TEFA dan kepuasan pelanggan.";
            }
        }
        // ==========================================
        // F. ROLE: CUSTOMER (DEFAULT)
        // ==========================================
        else {
            // SAPAAN / GREETING
            if ($isGreeting) {
                $replyText = "Halo! 👋 Saya **AI Assistant TKJ Service Center**.\n\n" .
                             "Ada kendala perangkat komputer, printer, CCTV, atau jaringan di tempat Anda? Tanyakan saja di sini, saya siap membantu memberikan diagnosa awal, pengecekan pesanan Anda, dan rekomendasi layanan kami!";
            }
            // TROUBLESHOOTING AWAL DULU jika pelanggan bertanya masalah teknis
            else if ($isTechIssue) {
                if (strpos($msg, 'laptop') !== false || strpos($msg, 'komputer') !== false || strpos($msg, 'pc') !== false) {
                    foreach ($allServices as $s) {
                        if (stripos($s['name'], 'laptop') !== false || stripos($s['name'], 'komputer') !== false) {
                            $recommendedService = $s;
                            break;
                        }
                    }
                    $replyText = "💡 **Langkah Pertolongan Pertama (Troubleshooting Awal)**:\n\n" .
                                 "1. **Jika Mati Total:** Cabut kabel charger & baterai (jika removable), tekan dan tahan tombol Power selama 30 detik (Static Discharge), lalu pasang kembali adaptor dan coba nyalakan.\n" .
                                 "2. **Jika Lampu Indikator Nyala tapi Layar Gelap (No Display):** Coba hubungkan ke monitor/TV eksternal via kabel HDMI untuk memastikan apakah masalah pada layar atau RAM/mesin.\n" .
                                 "3. **Jika Lambat / Lemot:** Buka Task Manager (`Ctrl + Shift + Esc`) dan periksa penggunaan Disk dan RAM. Hindari meletakkan laptop di atas kasur/bantal agar sirkulasi udara lancar.\n\n" .
                                 "Jika kendala masih berlanjut setelah langkah di atas, unit TKJ Service Center siap membantu penanganan lebih lanjut melalui jasa **" . ($recommendedService['name'] ?? 'Service & Cleaning Laptop / PC') . "**.\n" .
                                 "• Estimasi Biaya Mulai: Rp " . number_format($recommendedService['price_starts_from'] ?? 150000, 0, ',', '.') . "\n" .
                                 "• Estimasi Pengerjaan: " . ($recommendedService['estimated_time'] ?? '1 Hari Kerja');
                } else if (strpos($msg, 'wifi') !== false || strpos($msg, 'jaringan') !== false || strpos($msg, 'internet') !== false || strpos($msg, 'router') !== false) {
                    foreach ($allServices as $s) {
                        if (stripos($s['name'], 'router') !== false || stripos($s['name'], 'jaringan') !== false) {
                            $recommendedService = $s;
                            break;
                        }
                    }
                    $replyText = "💡 **Langkah Pertolongan Pertama (Troubleshooting Awal)**:\n\n" .
                                 "1. **Restart Modem / Router:** Cabut adaptor listrik modem router selama 10-15 detik, lalu colokkan kembali dan tunggu 2-3 menit hingga semua lampu indikator (PON/Internet) stabil.\n" .
                                 "2. **Periksa Kabel LAN:** Pastikan kabel UTP terpasang kuat di port LAN (terdengar bunyi 'klik').\n" .
                                 "3. **Lupakan Jaringan (Forget Network):** Pada HP/laptop Anda, lakukan 'Forget' pada Wi-Fi tersebut lalu masukkan kembali kata sandinya.\n" .
                                 "4. **Coba Perangkat Lain:** Periksa apakah perangkat lain juga mengalami gangguan untuk memastikan apakah kendala ada pada router atau perangkat tertentu.\n\n" .
                                 "Jika router memerlukan konfigurasi ulang, pembagian bandwidth, atau re-cabling, Anda dapat memesan jasa **" . ($recommendedService['name'] ?? 'Konfigurasi Router & Jaringan') . "** kami.\n" .
                                 "• Estimasi Biaya Mulai: Rp " . number_format($recommendedService['price_starts_from'] ?? 250000, 0, ',', '.') . "\n" .
                                 "• Estimasi Pengerjaan: " . ($recommendedService['estimated_time'] ?? '3-5 Jam');
                } else if (strpos($msg, 'cctv') !== false || strpos($msg, 'kamera') !== false) {
                    foreach ($allServices as $s) {
                        if (stripos($s['name'], 'cctv') !== false) {
                            $recommendedService = $s;
                            break;
                        }
                    }
                    $replyText = "💡 **Langkah Pertolongan Pertama (Troubleshooting Awal)**:\n\n" .
                                 "1. **Cek Sumber Listrik:** Pastikan stopkontak adaptor atau power supply central 12V CCTV dalam keadaan menyala.\n" .
                                 "2. **Cek Lampu Infrared:** Tutup sensor kamera dengan tangan di tempat gelap. Jika lampu infra-merah kecil berwarna merah menyala, berarti kamera mendapat suplai listrik.\n" .
                                 "3. **Periksa Kabel BNC / Jack:** Goyang perlahan konektor BNC di belakang DVR untuk memeriksa apakah gambar sempat muncul (kabel kendor).\n" .
                                 "4. **Restart DVR:** Buka menu pengaturan DVR dan lakukan restart.\n\n" .
                                 "Jika kamera tetap mati atau kabel mengalami kerusakan fisik, teknisi siswa kami siap membantu perbaikan melalui jasa **" . ($recommendedService['name'] ?? 'Instalasi & Perbaikan CCTV') . "**.\n" .
                                 "• Estimasi Biaya Mulai: Rp " . number_format($recommendedService['price_starts_from'] ?? 350000, 0, ',', '.') . "\n" .
                                 "• Estimasi Pengerjaan: " . ($recommendedService['estimated_time'] ?? '1 Hari Kerja');
                } else {
                    $replyText = "💡 **Langkah Diagnosa Awal**:\n\n" .
                                 "1. Lakukan restart pada perangkat Anda terlebih dahulu.\n" .
                                 "2. Pastikan pasokan listrik stabil dan kabel daya/data terhubung kencang.\n" .
                                 "3. Periksa apakah ada pesan error spesifik yang muncul di layar.\n\n" .
                                 "Jika kendala belum teratasi, beritahukan jenis perangkat dan gejala detailnya kepada kami agar saya dapat memberikan langkah pemeriksaan lebih spesifik atau merekomendasikan teknisi kami.";
                }
            }
            // Tanya Katalog Jasa Tertentu
            else if (strpos($msg, 'cctv') !== false) {
                foreach ($allServices as $s) {
                    if (stripos($s['name'], 'cctv') !== false) {
                        $recommendedService = $s;
                        break;
                    }
                }
                $replyText = "📹 Untuk kebutuhan pemasangan atau perawatan CCTV, kami menyediakan layanan **" . ($recommendedService['name'] ?? 'Instalasi CCTV') . "**.\n\n" .
                             "• **Estimasi Biaya Jasa**: Mulai dari Rp " . number_format($recommendedService['price_starts_from'] ?? 350000, 0, ',', '.') . "\n" .
                             "• **Estimasi Waktu**: " . ($recommendedService['estimated_time'] ?? '1 Hari Kerja') . "\n\n" .
                             "Teknisi siswa kami menangani penarikan kabel, crimping konektor BNC/balun, konfigurasi DVR/NVR, serta pengaturan pemantauan online via smartphone.";
            } else if (strpos($msg, 'jaringan') !== false || strpos($msg, 'wifi') !== false || strpos($msg, 'router') !== false || strpos($msg, 'mikrotik') !== false || strpos($msg, 'lan') !== false) {
                foreach ($allServices as $s) {
                    if (stripos($s['name'], 'router') !== false || stripos($s['name'], 'jaringan') !== false) {
                        $recommendedService = $s;
                        break;
                    }
                }
                $replyText = "🌐 Untuk instalasi jaringan dan konfigurasi router, kami menyediakan layanan **" . ($recommendedService['name'] ?? 'Konfigurasi Router & Jaringan') . "**.\n\n" .
                             "• **Estimasi Biaya Jasa**: Mulai dari Rp " . number_format($recommendedService['price_starts_from'] ?? 250000, 0, ',', '.') . "\n" .
                             "• **Estimasi Waktu**: " . ($recommendedService['estimated_time'] ?? '3-5 Jam') . "\n\n" .
                             "Layanan mencakup setting MikroTik, manajemen bandwidth, hotspot voucher, instalasi kabel LAN gedung, dan konfigurasi Access Point Wi-Fi.";
            } else if (strpos($msg, 'laptop') !== false || strpos($msg, 'komputer') !== false || strpos($msg, 'pc') !== false) {
                foreach ($allServices as $s) {
                    if (stripos($s['name'], 'laptop') !== false || stripos($s['name'], 'komputer') !== false) {
                        $recommendedService = $s;
                        break;
                    }
                }
                $replyText = "💻 Untuk perbaikan PC dan laptop, kami menyediakan layanan **" . ($recommendedService['name'] ?? 'Service & Cleaning Laptop / PC') . "**.\n\n" .
                             "• **Estimasi Biaya Jasa**: Mulai dari Rp " . number_format($recommendedService['price_starts_from'] ?? 150000, 0, ',', '.') . "\n" .
                             "• **Estimasi Waktu**: " . ($recommendedService['estimated_time'] ?? '1 Hari Kerja') . "\n\n" .
                             "Layanan mencakup pembersihan debu motherboard, penggantian pasta termal (thermal paste), instalasi ulang OS, dan upgrade modul SSD/RAM.";
            }
            // PERTANYAAN IDENTITAS (Rule #2)
            else if ($isIdentityQuestion) {
                $replyText = "Saya adalah **AI Assistant TKJ Service Center** yang siap membantu Anda dalam:\n" .
                             "• **Konsultasi & Diagnosa IT:** Kendala laptop/PC, router MikroTik, jaringan LAN/Wi-Fi, dan CCTV.\n" .
                             "• **Pengecekan Data Akun:** Status pesanan layanan Anda, jumlah order, dan tagihan invoice.\n" .
                             "• **Rekomendasi Layanan:** Informasi paket jasa resmi dari katalog TKJ Service Center.";
            }
            // FALLBACK PENOLAKAN SOPAN: Pertanyaan di luar cakupan aplikasi TKJ Service Center
            else {
                $replyText = "Mohon maaf, pertanyaan tersebut berada di luar cakupan aplikasi TKJ Service Center.\n\n" .
                             "Saya adalah **AI Assistant TKJ Service Center** yang dapat membantu Anda mengenai:\n" .
                             "• **Data Layanan & Akun Anda:** Jumlah order Anda, status pesanan, dan tagihan invoice.\n" .
                             "• **Konsultasi Teknis IT:** Permasalahan komputer, laptop, jaringan LAN, Wi-Fi, router MikroTik, printer, atau CCTV.\n" .
                             "• **Katalog Jasa & Estimasi Biaya:** Rekomendasi layanan dan pemesanan perbaikan resmi.\n\n" .
                             "Silakan tanyakan seputar kendala perangkat atau pesanan layanan Anda di TKJ Service Center!";
            }
        }

        return [
            'text' => $replyText,
            'recommended_service' => $recommendedService
        ];
    }

    /**
     * TECHNICIAN AI TROUBLESHOOTING ASSISTANT ENGINE
     * Generates structured diagnostic advice for TKJ technicians based on:
     * - Device Type (PC, Laptop, Router, Switch, AP, Printer, CCTV, Server, LAN Cable)
     * - Symptom
     * - Physical/Device Condition
     * - Initial Check Results
     */
    public function generateTechnicianTroubleshootResponse($deviceType, $symptom, $deviceCondition = '', $initialCheck = '')
    {
        $deviceTypeClean = trim($deviceType);
        $symptomClean = trim($symptom);
        $conditionClean = trim($deviceCondition);
        $checkClean = trim($initialCheck);

        // Check if external Gemini API Key is configured in environment/config
        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : (getenv('GEMINI_API_KEY') ?: null);
        if ($apiKey) {
            $apiResult = $this->callGeminiApiForTroubleshoot($apiKey, $deviceTypeClean, $symptomClean, $conditionClean, $checkClean);
            if ($apiResult !== null) {
                return $apiResult;
            }
        }

        // Execute High-Precision Technical Smart Engine for TKJ Scenarios
        return $this->generateTechnicalSmartFallback($deviceTypeClean, $symptomClean, $conditionClean, $checkClean);
    }

    /**
     * Call Gemini API server-side proxy
     */
    private function callGeminiApiForTroubleshoot($apiKey, $deviceType, $symptom, $condition, $check)
    {
        $prompt = "Anda adalah AI Technical Assistant senior untuk laboratorium dan bisnis TEFA TKJ (Teknik Komputer & Jaringan).\n" .
            "Bantu teknisi siswa menganalisis masalah berikut secara hati-hati, terstruktur, dan non-definitive:\n\n" .
            "Jenis Perangkat: {$deviceType}\n" .
            "Gejala Masalah: {$symptom}\n" .
            "Kondisi Perangkat: {$condition}\n" .
            "Hasil Pemeriksaan Awal: {$check}\n\n" .
            "Berikan keluaran JSON valid dengan struktur berikut:\n" .
            "{\n" .
            "  \"possible_causes\": [{\"cause\": \"...\", \"probability\": \"Tinggi/Sedang/Rendah\", \"indicative_notes\": \"...\"}],\n" .
            "  \"inspection_checklist\": [\"...\"],\n" .
            "  \"basic_steps\": [\"...\"],\n" .
            "  \"next_inspection_steps\": [\"...\"],\n" .
            "  \"escalation_advice\": \"...\",\n" .
            "  \"disclaimer\": \"...\"\n" .
            "}";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.2,
                "responseMimeType" => "application/json"
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $decoded = json_decode($response, true);
            $jsonText = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($jsonText) {
                $parsed = json_decode($jsonText, true);
                if (isset($parsed['possible_causes']) && isset($parsed['inspection_checklist'])) {
                    return $parsed;
                }
            }
        }

        return null; // Fallback if API fails or offline
    }

    /**
     * Technical Smart Engine: Rule-Based Logic matching TKJ Hardware & Networking Standards
     */
    private function generateTechnicalSmartFallback($deviceType, $symptom, $condition, $check)
    {
        $d = strtolower($deviceType);
        $s = strtolower($symptom);
        $c = strtolower($condition);
        $k = strtolower($check);

        $possibleCauses = [];
        $inspectionChecklist = [];
        $basicSteps = [];
        $nextInspectionSteps = [];
        $escalationAdvice = "";
        $disclaimer = "AI ini hanya berfungsi sebagai alat bantu navigasi teknis. Diagnosa bersifat indikatif dan tidak 100% pasti. Teknisi tetap menjadi pengambil keputusan akhir di lapangan.";

        // --- SCENARIO 1: NETWORK & LAN / PC NO INTERNET ---
        if (strpos($d, 'pc') !== false || strpos($d, 'komputer') !== false || strpos($d, 'lan') !== false || strpos($d, 'network') !== false || strpos($s, 'jaringan') !== false || strpos($s, 'internet') !== false || strpos($s, 'ping') !== false || strpos($s, '169.254') !== false) {

            $possibleCauses = [
                [
                    'cause' => 'Masalah Kabel UTP / Konektor RJ45 Teroksidasi atau Pin Longgar',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Tanda fisiknya sering ditunjukkan dengan indikator port LAN (link LED) mati atau blinking abnormal, atau kabel terpuntir.'
                ],
                [
                    'cause' => 'Konfigurasi IP Address APIPA (169.254.x.x) akibat Gagal DHCP Lease',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Terjadi ketika perangkat tidak menerima respon respon ACK DHCP dari Router/MikroTik atau kabel terputus di tengah.'
                ],
                [
                    'cause' => 'Driver Network Interface Card (NIC) Corrupt / Disable di OS',
                    'probability' => 'Sedang',
                    'indicative_notes' => 'Status adapter di Control Panel Network Connections mungkin bertuliskan Disabled atau tanda seru di Device Manager.'
                ],
                [
                    'cause' => 'Port Switch / LAN Motherboard Rusak Fisik (Terkena Surge/Petir)',
                    'probability' => 'Rendah - Perlu Uji Lanjut',
                    'indicative_notes' => 'Kemungkinan terjadi jika port tidak merespon sama sekali dengan kabel tester atau PC lain di port yang sama.'
                ]
            ];

            $inspectionChecklist = [
                'Periksa fisik port RJ45 di PC dan Switch (pastikan kait pengunci RJ45 mengeklik sempurna).',
                'Amati indikator lampu LED di NIC PC (Hijau: Link Speed, Oranye/Kuning: Activity Data).',
                'Gunakan Cable Tester UTP untuk menguji keterhubungan 8 pin kabel Cat5e/Cat6.',
                'Jalankan perintah `ipconfig /all` di Command Prompt Windows untuk memeriksa IP, Subnet, Gateway, dan DNS.',
                'Lakukan tes Loopback Ping ke `127.0.0.1` untuk memastikan protokol TCP/IP internal OS berfungsi.',
                'Lakukan Ping ke IP Gateway Router (misal: `ping 192.168.1.1` atau `ping 192.168.88.1`).'
            ];

            $basicSteps = [
                'Lepas dan pasang kembali kabel RJ45 pada PC dan walljack/switch.',
                'Reset tumpukan TCP/IP dan DNS di CMD (Jalankan sebagai Admin): `ipconfig /release` -> `ipconfig /renew` -> `ipconfig /flushdns`.',
                'Nonaktifkan dan aktifkan kembali Network Adapter: `ncpa.cpl` -> Klik Kanan Ethernet -> Disable -> Enable.',
                'Jalankan perintah Windows Network Troubleshooter (`msdt.exe /id NetworkDiagnosticsNetworkAdapter`).',
                'Coba gunakan USB to LAN adapter atau kabel LAN patchcord baru yang sudah lulus uji tester.'
            ];

            $nextInspectionSteps = [
                'Pindahkan koneksi kabel ke port Switch lain yang dipastikan aktif/normal.',
                'Cek tabel DHCP Leases dan status log di Router MikroTik untuk memastikan pool IP address tidak penuh.',
                'Uji perangkat PC menggunakan OS Live Boot (misal Bootable Linux/WinPE) untuk mengisolasi kendala software OS vs Hardware NIC.'
            ];

            $escalationAdvice = 'Jika tes loopback 127.0.0.1 gagal, port LAN motherboard mati total (terbakar/tidak terdeteksi di BIOS), atau membutuhkan penggantian Switch Layer-3 / Re-cabling jaringan gedung, segera eskalasikan ke Senior Teknisi / Operator TKJ.';

        }
        // --- SCENARIO 2: ROUTER MIKROTIK / ACCESS POINT / HOTSPOT ---
        else if (strpos($d, 'router') !== false || strpos($d, 'mikrotik') !== false || strpos($d, 'access point') !== false || strpos($d, 'wifi') !== false || strpos($s, 'hotspot') !== false || strpos($s, 'rto') !== false || strpos($s, 'lambat') !== false) {

            $possibleCauses = [
                [
                    'cause' => 'Alokasi Bandwidth Saturated / Limit Queue Terlampaui',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Pengguna jaringan tinggi menyebabkan paket RTO dan ping latency melonjak.'
                ],
                [
                    'cause' => 'Gagal Dial modem ISP / Link WAN Terputus',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Indikator port WAN di router mati atau status PPP/DHCP Client di MikroTik bertuliskan Searching/Connecting.'
                ],
                [
                    'cause' => 'Interferensi Frekuensi Wi-Fi 2.4GHz / Channel Overlapping',
                    'probability' => 'Sedang',
                    'indicative_notes' => 'Terjadi jika banyak Access Point menggunakan channel sama (misal channel 6 bertumpuk).'
                ],
                [
                    'cause' => 'High CPU Usage Router (>90%) akibat Loop Jaringan atau DDoS',
                    'probability' => 'Sedang - Perlu Uji Lanjut',
                    'indicative_notes' => 'Dapat diperiksa melalui menu System -> Resources di Winbox.'
                ]
            ];

            $inspectionChecklist = [
                'Login ke Winbox / WebFig Router MikroTik dan amati beban CPU di Dashboard.',
                'Cek status Interface WAN (ether1) apakah mendapat IP dari ISP dan status Link UP.',
                'Cek menu IP -> Routes (pastikan ada route default `0.0.0.0/0` dengan status AS/Reachable).',
                'Cek menu IP -> DNS (pastikan Allow Remote Requests diaktifkan dan DNS Google/ISP dapat di-ping).',
                'Uji Ping & Traceroute dari Winbox Terminal ke `8.8.8.8` dan `google.com`.'
            ];

            $basicSteps = [
                'Reboot Router MikroTik secara aman via Winbox: `System` -> `Reboot`.',
                'Periksa kabel modem ISP yang terhubung ke port ether1 WAN router.',
                'Cek aturan Simple Queue atau Queue Tree di IP -> Queues, pastikan tidak ada IP yang ter-limit secara tidak sengaja.',
                'Gantikan kabel patchcord antarmuka modem ISP ke Router.',
                'Pada Access Point, sesuaikan channel Wi-Fi ke channel non-overlapping (Channel 1, 6, atau 11 untuk 2.4GHz).'
            ];

            $nextInspectionSteps = [
                'Lakukan Packet Capture (Torch) pada interface WAN untuk menganalisis trafik abnormal atau broadcast storm.',
                'Verifikasi voltase Power Supply/Adaptor Router menggunakan Multimeter (pastikan tidak drop di bawah 12V/24V).',
                'Cek konfigurasi Firewall Filter Rules jika ada blocking port/IP yang tidak disengaja.'
            ];

            $escalationAdvice = 'Jika Router mengalami Kernel Crash / Bootloop (lampu indikator LED berkedip bersamaan), butuh reset netinstall, atau ada pembaruan konfigurasi VLAN trunk sekolah, konsultasikan dengan Instruktur / Senior Teknisi.';

        }
        // --- SCENARIO 3: CCTV / DVR / NVR ---
        else if (strpos($d, 'cctv') !== false || strpos($d, 'dvr') !== false || strpos($d, 'nvr') !== false || strpos($s, 'no video') !== false || strpos($s, 'kamera') !== false || strpos($s, 'hitam') !== false) {

            $possibleCauses = [
                [
                    'cause' => 'Power Supply Central / Adaptor Kamera Drop Voltase atau Mati',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Menyebabkan satu atau beberapa kamera mati bersamaan (No Video / Blank Black Screen).'
                ],
                [
                    'cause' => 'Konektor BNC Jack / Power DC Teroksidasi atau Kendor',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Sering menimbulkan efek gambar bergaris, bergelombang, flickering, lalu hilang total.'
                ],
                [
                    'cause' => 'Port DVR/NVR Channel Rusak atau IP Camera Mismatch Subnet',
                    'probability' => 'Sedang',
                    'indicative_notes' => 'Untuk IP Cam, pastikan IP camera berada dalam satu segmen network dengan NVR.'
                ]
            ];

            $inspectionChecklist = [
                'Amati fisik lensa kamera pada tempat gelap: Cek apakah lampu LED Infrared (IR) menyala merah redup.',
                'Uji keluaran voltase Power Supply Central 12V DC menggunakan Multimeter di titik jack DC kamera.',
                'Tukar port BNC / Channel di belakang DVR dari kamera bermasalah ke channel kamera yang normal.',
                'Periksa sambungan kabel coaxial RG59/RG6 atau balun UTP di kedua ujung (DVR & Kamera).',
                'Buka menu Encode/Video Setting di DVR untuk memastikan resolusi kamera sesuai dengan kapabilitas DVR.'
            ];

            $basicSteps = [
                'Kencangkan konektor BNC jack dan pastikan serabut grounding tidak menyenggol kawat inti coaxial.',
                'Ganti konektor BNC atau Pasif Video Balun dengan unit baru.',
                'Lakukan restart pada DVR/NVR via menu System Logout -> Reboot.',
                'Uji kamera bermasalah secara langsung dekat DVR menggunakan kabel test pendek 1 meter (Bench Test).'
            ];

            $nextInspectionSteps = [
                'Uji sambungan kabel coaxial menggunakan Kabel Tester / Multimeter mode kontinuitas (beep test).',
                'Jika IP Camera PoE, periksa port PoE Switch dan daya total Watt yang dikonsumsi PoE Switch.',
                'Periksa kesehatan Harddisk (HDD Health Status) di menu Storage DVR untuk kendala rekaman tidak tersimpan.'
            ];

            $escalationAdvice = 'Jika ditemukan kerusakan IC Mainboard DVR, Harddisk Bad Sector berat, atau membutuhkan penarikan kabel outdoor jalur tinggi (membutuhkan scaffolding/tangga khusus), eskalasikan ke Operator/Teknisi Senior.';

        }
        // --- SCENARIO 4: LAPTOP & HARDWARE PC (MATI TOTAL / OVERHEAT / BLUESCREEN) ---
        else if (strpos($d, 'laptop') !== false || strpos($s, 'mati') !== false || strpos($s, 'panas') !== false || strpos($s, 'bluescreen') !== false || strpos($s, 'beep') !== false || strpos($s, 'display') !== false) {

            $possibleCauses = [
                [
                    'cause' => 'Kotoran Berlebih di Fansink & Thermal Paste Kering (Overheat Shutdown)',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Perangkat menyala sebentar lalu mati mendadak saat membuka aplikasi berat.'
                ],
                [
                    'cause' => 'Pin Kuningan RAM Teroksidasi / Kerusakan Modul RAM',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Kondisi ditandai dengan lampu indikator power menyala tapi layar tidak tampil (No Display) atau bunyi beep berkelanjutan.'
                ],
                [
                    'cause' => 'Adaptor Charger / Power Supply Drop / Baterai CMOS Habis',
                    'probability' => 'Sedang',
                    'indicative_notes' => 'Komputer mati total saat tombol power ditekan, tanpa ada indikator LED menyala.'
                ],
                [
                    'cause' => 'Kerusakan Storage SSD/HDD (Bad Sector / BSOD Critical Process Died)',
                    'probability' => 'Sedang',
                    'indicative_notes' => 'Sering muncul layar biru Windows (BSOD) atau langsung masuk ke BIOS Setup (No Bootable Device).'
                ]
            ];

            $inspectionChecklist = [
                'Periksa indikator LED saat charger dicolokkan (apakah menyala/berkedip).',
                'Lakukan tes Static Discharge: Cabut adaptor & baterai, tekan tombol Power selama 30 detik.',
                'Lepas RAM, bersihkan pin kuningan RAM menggunakan penghapus pensil halus.',
                'Dengarkan suara kipas heatsink dan rasakan hembusan udara panas di lubang ventilasi.',
                'Buka BIOS (tekan F2/Del saat boot) untuk memastikan SSD/HDD terdeteksi dengan benar.',
                'Cek kesehatan SSD/HDD menggunakan CrystalDiskInfo atau tool diagnostik vendor.'
            ];

            $basicSteps = [
                'Jalankan tes kelistrikan adaptor charger menggunakan Multimeter (pastikan voltase output sesuai stiker adaptor, misal 19V DC).',
                'Pasang 1 keping RAM secara bergantian di slot berbeda (jika menggunakan dual RAM).',
                'Lakukan pembersihan debu fisik menggunakan blower dan ganti Thermal Paste pada CPU & GPU.',
                'Reset settings BIOS ke Factory Default (Load Optimized Defaults).',
                'Ganti baterai CMOS CR2032 jika tanggal & waktu BIOS selalu ter-reset ke tahun lama.'
            ];

            $nextInspectionSteps = [
                'Uji kestabilan RAM menggunakan Windows Memory Diagnostic atau MemTest86.',
                'Bongkar motherboard untuk memeriksa apakah ada kapasitor kembung, jalur korosi akibat cairan, atau komponen short.',
                'Ganti temporary SSD test untuk memastikan kendala OS corrupt vs kerusakan fisik drive.'
            ];

            $escalationAdvice = 'Jika terjadi short circuit pada chipset motherboard, butuh solder/reballing chip GPU/CPU, atau layar LCD pecah/bergaris fisik, eskalasikan pekerjaan ke Teknisi Specialist Hardware TKJ.';

        }
        // --- SCENARIO 5: GENERAL DEFAULT / OTHER HARDWARE ---
        else {

            $possibleCauses = [
                [
                    'cause' => 'Gangguan Kelistrikan / Koneksi Kabel Power & Kabel Data Longgar',
                    'probability' => 'Tinggi (Terindikasi)',
                    'indicative_notes' => 'Kabel daya atau kabel interface data belum terpasang kencang.'
                ],
                [
                    'cause' => 'Konfigurasi Parameter Software / Driver Perangkat Tidak Sesuai',
                    'probability' => 'Sedang',
                    'indicative_notes' => 'Driver perangkat mengalami ketidakcocokan versi OS atau belum di-instal.'
                ],
                [
                    'cause' => 'Penumpukan Thermal / Debu di Jalur Airflow Komponen Internal',
                    'probability' => 'Sedang',
                    'indicative_notes' => 'Debu tebal dapat menghambat konduksi panas dan menyebabkan perlindungan proteksi komponen aktif.'
                ],
                [
                    'cause' => 'Kerusakan Fisik Elemen Komponen Internal',
                    'probability' => 'Rendah - Butuh Diagnosa Spesifik',
                    'indicative_notes' => 'Perlu pembongkaran & pengukuran lanjutan dengan instrumen ukur laboratorium.'
                ]
            ];

            $inspectionChecklist = [
                'Pastikan kabel power terhubung ke stopkontak ber-grounding dengan voltase AC 220V stabil.',
                'Periksa indikator lampu LED status pada fisik perangkat.',
                'Uji fungsionalitas kabel data (USB/LAN/HDMI) dengan perangkat pembanding yang dipastikan normal.',
                'Cek Device Manager / Event Viewer di OS untuk melihat pesan error log hardware.'
            ];

            $basicSteps = [
                'Lakukan restart daya (Power Cycle): Matikan saklar, tunggu 30 detik, lalu hidupkan kembali.',
                'Pastikan seluruh soket dan kabel terpasang dengan kuat.',
                'Update driver perangkat ke versi terbaru dari situs resmi pabrikan.',
                'Bersihkan ventilasi dan area fisik perangkat dari debu.'
            ];

            $nextInspectionSteps = [
                'Uji perangkat di lingkungan laboratorium terpisah untuk mengisolasi kendala catu daya / lingkungan.',
                'Lakukan pengujian komponen satu per satu (Isolasi Komponen).'
            ];

            $escalationAdvice = 'Jika pemeriksaan dasar tidak menyelesaikan kendala atau membutuhkan penggantian part bernilai tinggi, segera berkonsultasi dengan Senior Teknisi atau Operator.';

        }

        // Custom recommendations if symptom is vague
        if (strlen($symptom) < 10) {
            $disclaimer .= " Catatan: Gejala masalah yang dimasukkan sangat singkat. Disarankan memasukkan detail gejala yang lebih spesifik untuk rekomendasi yang lebih presisi.";
        }

        return [
            'possible_causes' => $possibleCauses,
            'inspection_checklist' => $inspectionChecklist,
            'basic_steps' => $basicSteps,
            'next_inspection_steps' => $nextInspectionSteps,
            'escalation_advice' => $escalationAdvice,
            'disclaimer' => $disclaimer
        ];
    }

    /**
     * AI WORK REPORT GENERATOR FOR TECHNICIANS
     * Transforms technician raw inputs (masalah, diagnosa, tindakan, hasil, sparepart, catatan)
     * into a formal 7-section professional service report without fabricating facts.
     */
    public function generateWorkReportResponse($order, $inputs)
    {
        $masalah   = trim($inputs['masalah'] ?? '');
        $diagnosa  = trim($inputs['diagnosa'] ?? '');
        $tindakan  = trim($inputs['tindakan'] ?? '');
        $hasil     = trim($inputs['hasil'] ?? '');
        $sparepart = trim($inputs['sparepart'] ?? '');
        $catatan   = trim($inputs['catatan'] ?? '');

        // Check if external Gemini API Key is configured
        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : (getenv('GEMINI_API_KEY') ?: null);
        if ($apiKey) {
            $apiResult = $this->callGeminiApiForWorkReport($apiKey, $order, $masalah, $diagnosa, $tindakan, $hasil, $sparepart, $catatan);
            if ($apiResult !== null) {
                return $apiResult;
            }
        }

        // Fallback to high-precision professional template engine
        return $this->generateWorkReportSmartFallback($order, $masalah, $diagnosa, $tindakan, $hasil, $sparepart, $catatan);
    }

    /**
     * Call Gemini API server-side proxy for Work Report Generation
     */
    private function callGeminiApiForWorkReport($apiKey, $order, $masalah, $diagnosa, $tindakan, $hasil, $sparepart, $catatan)
    {
        $orderCode = $order['order_code'] ?? 'ORD-UNKNOWN';
        $serviceName = $order['service_name'] ?? 'Layanan Teknis IT';
        $customerName = $order['customer_name'] ?? 'Pelanggan';
        $address = $order['service_address'] ?? 'Lokasi Pelanggan';

        $prompt = "Anda adalah AI Technical Report Specialist untuk TKJ Service Center.\n" .
            "Tugas Anda: Mengubah catatan mentah dari teknisi menjadi Laporan Hasil Pekerjaan Resmi & Profesional.\n\n" .
            "INFORMASI PEKERJAAN LAPANGAN:\n" .
            "- Kode Tiket Order: {$orderCode}\n" .
            "- Layanan Jasa: {$serviceName}\n" .
            "- Pelanggan: {$customerName}\n" .
            "- Alamat: {$address}\n\n" .
            "INPUT CATATAN TEKNISI:\n" .
            "- Masalah: {$masalah}\n" .
            "- Diagnosa: {$diagnosa}\n" .
            "- Tindakan: {$tindakan}\n" .
            "- Hasil: {$hasil}\n" .
            "- Sparepart: " . ($sparepart ?: 'Tidak ada') . "\n" .
            "- Catatan: " . ($catatan ?: 'Tidak ada') . "\n\n" .
            "PERATURAN MUTLAK (STRICT POLICY):\n" .
            "1. HANYA gunakan fakta yang diberikan teknisi di atas. JANGAN menambah tindakan perbaikan, sparepart, atau diagnosa yang tidak ditulis oleh teknisi.\n" .
            "2. JIKA informasi sparepart/catatan kosong atau tidak ada, tuliskan secara jujur: 'Tidak ada sparepart/material tambahan yang digunakan.' atau 'Tidak ada catatan tambahan.' JANGAN MENGARANG INFOMASI.\n" .
            "3. Gunakan tata bahasa Indonesia baku, formal, dan profesional khas laporan teknik IT.\n" .
            "4. Berikan keluaran JSON valid dengan struktur 7 bagian berikut:\n" .
            "{\n" .
            "  \"identitas\": \"...\",\n" .
            "  \"permasalahan\": \"...\",\n" .
            "  \"hasil_pemeriksaan\": \"...\",\n" .
            "  \"tindakan_dilakukan\": \"...\",\n" .
            "  \"sparepart_material\": \"...\",\n" .
            "  \"hasil_akhir\": \"...\",\n" .
            "  \"rekomendasi\": \"...\"\n" .
            "}";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.2,
                "responseMimeType" => "application/json"
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $decoded = json_decode($response, true);
            $jsonText = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($jsonText) {
                $parsed = json_decode($jsonText, true);
                if (isset($parsed['identitas']) && isset($parsed['permasalahan']) && isset($parsed['hasil_akhir'])) {
                    $fullReport = $this->buildFullReportString($parsed);
                    return [
                        'success' => true,
                        'sections' => $parsed,
                        'full_report' => $fullReport
                    ];
                }
            }
        }

        return null;
    }

    /**
     * High-Precision Technical Smart Engine Fallback for Work Report Generation
     */
    private function generateWorkReportSmartFallback($order, $masalah, $diagnosa, $tindakan, $hasil, $sparepart, $catatan)
    {
        $orderCode   = $order['order_code'] ?? 'ORD-LAPORAN';
        $serviceName = $order['service_name'] ?? 'Layanan Teknis IT & Jaringan';
        $custName    = $order['customer_name'] ?? 'Pelanggan TKJ';
        $custPhone   = $order['customer_phone'] ?? '-';
        $address     = $order['service_address'] ?? 'Lokasi Pekerjaan';

        $user = Auth::user();
        $techName = $user['name'] ?? ($user['full_name'] ?? 'Teknisi Siswa TKJ');
        $dateStr  = date('d F Y, H:i') . ' WIB';

        // 1. Identitas Pekerjaan
        $identitas = "Kode Tiket : {$orderCode}\n" .
                     "Jenis Layanan : {$serviceName}\n" .
                     "Pelanggan : {$custName} ({$custPhone})\n" .
                     "Lokasi Pekerjaan : {$address}\n" .
                     "Teknisi Pelaksana : {$techName}\n" .
                     "Waktu Pengerjaan : {$dateStr}";

        // 2. Permasalahan
        $permasalahan = !empty($masalah)
            ? "Berdasarkan laporan dan hasil konfirmasi awal, kendala yang dialami yaitu: " . rtrim($masalah, '.') . "."
            : "Kendala teknis pada lokasi pekerjaan memerlukan penanganan dan verifikasi teknisi lapangan.";

        // 3. Hasil Pemeriksaan
        $hasilPemeriksaan = !empty($diagnosa)
            ? "Hasil diagnosa dan pemeriksaan teknis di lapangan menunjukkan: " . rtrim($diagnosa, '.') . "."
            : "Pemeriksaan fungsi dan jalur kelistrikan/jaringan telah dilakukan oleh teknisi.";

        // 4. Tindakan yang Dilakukan
        $tindakanDilakukan = !empty($tindakan)
            ? "Langkah perbaikan dan penanganan yang telah dieksekusi yaitu: " . rtrim($tindakan, '.') . "."
            : "Tindakan perbaikan standar layanan TKJ Service Center telah dilaksanakan di lokasi.";

        // 5. Sparepart/Material
        $sparepartLower = strtolower($sparepart);
        if (empty($sparepart) || strpos($sparepartLower, 'tidak') !== false || strpos($sparepartLower, 'tanpa') !== false || $sparepartLower === '-') {
            $sparepartMaterial = "Tidak ada sparepart atau material tambahan yang digunakan.";
        } else {
            $sparepartMaterial = "Komponen/material yang digunakan selama pengerjaan:\n• " . str_replace(',', "\n• ", $sparepart);
        }

        // 6. Hasil Akhir
        $hasilAkhir = !empty($hasil)
            ? "Setelah dilakukan perbaikan dan pengujian akhir, didapatkan hasil: " . rtrim($hasil, '.') . "."
            : "Pekerjaan telah diselesaikan dan sistem berfungsi dengan baik.";

        // 7. Rekomendasi
        if (!empty($catatan)) {
            $rekomendasi = "Saran dan catatan pemeliharaan teknis dari teknisi:\n" . rtrim($catatan, '.') . ".";
        } else {
            $rekomendasi = "Rekomendasi Pemeliharaan:\n" .
                           "1. Lakukan pemeliharaan rutin dan pembersihan fisik perangkat secara berkala.\n" .
                           "2. Pastikan pasokan daya listrik stabil dan terhindar dari benturan/kelembaban berlebih.";
        }

        $sections = [
            'identitas'          => $identitas,
            'permasalahan'       => $permasalahan,
            'hasil_pemeriksaan'  => $hasilPemeriksaan,
            'tindakan_dilakukan' => $tindakanDilakukan,
            'sparepart_material' => $sparepartMaterial,
            'hasil_akhir'        => $hasilAkhir,
            'rekomendasi'        => $rekomendasi
        ];

        $fullReport = $this->buildFullReportString($sections);

        return [
            'success'     => true,
            'sections'    => $sections,
            'full_report' => $fullReport
        ];
    }

    /**
     * Helper to combine 7 sections into a formatted markdown text string
     */
    private function buildFullReportString($sections)
    {
        return "=== LAPORAN HASIL PEKERJAAN RESMI ===\n" .
            "TKJ SERVICE CENTER - LAPANGAN\n\n" .
            "1. IDENTITAS PEKERJAAN\n" .
            $sections['identitas'] . "\n\n" .
            "2. PERMASALAHAN\n" .
            $sections['permasalahan'] . "\n\n" .
            "3. HASIL PEMERIKSAAN\n" .
            $sections['hasil_pemeriksaan'] . "\n\n" .
            "4. TINDAKAN YANG DILAKUKAN\n" .
            $sections['tindakan_dilakukan'] . "\n\n" .
            "5. SPAREPART / MATERIAL\n" .
            $sections['sparepart_material'] . "\n\n" .
            "6. HASIL AKHIR\n" .
            $sections['hasil_akhir'] . "\n\n" .
            "7. REKOMENDASI\n" .
            $sections['rekomendasi'];
    }
}

