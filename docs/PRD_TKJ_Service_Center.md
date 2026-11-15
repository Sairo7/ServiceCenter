# DOKUMEN SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK (PRD / SRS)
## SISTEM INFORMASI UNIT BISNIS TEFA "TKJ SERVICE CENTER" BERBASIS WEB DENGAN INTEGRASI KECERDASAN ARTIFISIAL (AI)

**Mata Kuliah:** Rekayasa Perangkat Lunak (TIKB2503021)  
**Program Studi:** S1 Teknik Informatika — STMIK IKMI Cirebon  
**Standar Dokumen:** IEEE Std 830-1998 / ISO/IEC/IEEE 29148:2018  
**Versi Dokumen:** 1.0  
**Tanggal:** 14 November 2026  

---

## DAFTAR ISI
1. [BAB I: PENDAHULUAN](#bab-i-pendahuluan)
   - 1.1 Tujuan Pembuatan Dokumen
   - 1.2 Lingkup Masalah & Nilai Guna Aplikasi
   - 1.3 Definisi, Akronim, dan Singkatan
   - 1.4 Referensi Standar
2. [BAB II: DESKRIPSI KESELURUHAN SISTEM](#bab-ii-deskripsi-keseluruhan-sistem)
   - 2.1 Perspektif Produk & Arsitektur Solusi
   - 2.2 Karakteristik & Peran Pengguna (Multi-Role Stakeholders)
   - 2.3 Batasan Perangkat Keras, Lunak, dan Lingkungan Operasional
   - 2.4 Asumsi dan Ketergantungan
3. [BAB III: SPESIFIKASI KEBUTUHAN FUNGSIONAL & USER STORIES](#bab-iii-spesifikasi-kebutuhan-fungsional--user-stories)
   - 3.1 Matriks Kebutuhan Fungsional (FR)
   - 3.2 User Stories & Kriteria Penerimaan (Acceptance Criteria)
4. [BAB IV: SPESIFIKASI FITUR KECERDASAN ARTIFISIAL (AI) UTAMA](#bab-iv-spesifikasi-fitur-kecerdasan-artifisial-ai-utama)
   - 4.1 Peran Strategis AI dalam Operasional TEFA
   - 4.2 Alur Pemrosesan Query & Query Engine Database Real-Time
   - 4.3 Spesifikasi Prompt Engineering & Format Respon Ringkas-ke-Rinci
5. [BAB V: KEBUTUHAN NON-FUNGSIONAL (NFR)](#bab-v-kebutuhan-non-fungsional-nfr)
   - 5.1 Kinerja (Performance)
   - 5.2 Keamanan & Privasi (Security & OTP Gateways)
   - 5.3 Ketersediaan & Keandalan (Availability & Reliability)
   - 5.4 Kemudahan Penggunaan (Usability)
6. [BAB VI: PENUTUP & KELAYAKAN PROYEK](#bab-vi-penutup--kelayakan-proyek)

---

## BAB I: PENDAHULUAN

### 1.1 Tujuan Pembuatan Dokumen
Dokumen *Product Requirements Document* (PRD) sekaligus *Software Requirements Specification* (SRS) ini disusun untuk merumuskan secara komprehensif seluruh kebutuhan fungsional, non-fungsional, dan arsitektur integrasi AI pada aplikasi **TKJ Service Center**. Dokumen ini menjadi acuan tunggal bagi rekayasa perangkat lunak mandiri, perancangan diagram UML, konstruksi kode, pengujian otomatis, serta demonstrasi proyek akhir mandiri.

### 1.2 Lingkup Masalah & Nilai Guna Aplikasi
Unit Bisnis Jasa *Teaching Factory* (TEFA) pada SMK Jurusan Teknik Komputer dan Jaringan (TKJ) memegang peran vital dalam melatih keterampilan vokasi siswa di bidang perbaikan komputer, instalasi jaringan, dan pemeliharaan CCTV. Namun, pengelolaan operasional konvensional menghadapi berbagai hambatan:
1. **Pencatatan Pesanan Tercecer:** Pengajuan perbaikan dari masyarakat/sekolah sering dicatat manual atau melalui chat lepas tanpa rekam jejak status yang transparan.
2. **Monitoring Tugas Teknisi Siswa:** Pihak operator sekolah kesulitan memonitor penugasan kerja siswa (*work orders*, log pengerjaan, dan bukti foto).
3. **Keterbatasan Layanan Tanya-Jawab 24/7:** Pelanggan dan pihak manajemen membutuhkan informasi cepat seputar layanan, harga, sparepart, dan status pekerjaan tanpa menunggu jam kerja operator.

**Nilai Guna Solusi (Value Proposition):**  
Aplikasi **TKJ Service Center** menghadirkan portal terintegrasi berbasis web yang memadukan transaksi jasa perbaikan, inventaris sparepart, penjadwalan teknisi siswa, penjaminan mutu melalui rating kepuasan, serta **AI Asisten Operasional Cerdas 24/7** yang langsung membaca data transaksi, pengguna, dan orderan secara *real-time*.

### 1.3 Definisi, Akronim, dan Singkatan
- **TEFA (*Teaching Factory*):** Model pembelajaran vokasi yang mengkondisikan siswa belajar dalam suasana industri nyata.
- **PRD / SRS:** *Product Requirements Document* / *Software Requirements Specification*.
- **MVC:** *Model-View-Controller*, pola arsitektur kode perangkat lunak.
- **RAG (*Retrieval-Augmented Generation*):** Metode pengambilan konteks data internal sebelum AI menghasilkan respon.
- **OTP (*One-Time Password*):** Kode keamanan 6 digit sekali pakai via WhatsApp.
- **SOLID:** 5 prinsip utama perancangan perangkat lunak berorientasi objek (*Single Responsibility, Open-Closed, Liskov Substitution, Interface Segregation, Dependency Inversion*).

### 1.4 Referensi Standar
- IEEE Std 830-1998, *IEEE Recommended Practice for Software Requirements Specifications*.
- ISO/IEC/IEEE 29148:2018, *Systems and software engineering — Life cycle processes — Requirements engineering*.

---

## BAB II: DESKRIPSI KESELURUHAN SISTEM

### 2.1 Perspektif Produk & Arsitektur Solusi
Aplikasi **TKJ Service Center** dibangun menggunakan arsitektur web MVC mandiri (*native*) dengan basis data relasional MariaDB/MySQL (15+ tabel ternormalisasi 3NF). Arsitektur sistem memisahkan lapisan penyajian (*Bootstrap 5 & Vanilla JS*), pengendali alur (*Controllers*), integritas data (*Models*), serta gateway layanan eksternal (Fonnte WhatsApp API Gateway & Gemini AI Engine).

### 2.2 Karakteristik & Peran Pengguna (Multi-Role Stakeholders)
Sistem memiliki 4 aktor utama dengan wewenang yang diisolasi oleh Middleware `Auth::requireRole()`:
1. **Kepala Sekolah (Executive / Pimpinan):**
   - Memantau rekapitulasi performa bisnis TEFA, omset total, kepuasan pelanggan, dan rasio pengerjaan teknisi siswa.
2. **Operator TKJ (Administrator & Pengelola Sistem):**
   - Mengelola master katalog jasa & suku cadang.
   - Mengelola akun pengguna (tambah, verifikasi status, filter per role).
   - Menyetujui order masuk, menugaskan teknisi siswa, dan memvalidasi pembayaran.
3. **Teknisi Siswa TKJ (Pelaksana Teknis Lapangan):**
   - Melihat daftar tugas perbaikan yang didelegasikan kepadanya.
   - Mengunggah catatan diagnosa (*work log*) dan bukti pengerjaan.
   - Mengubah status pengerjaan (*in_progress* hingga *completed*).
4. **Customer / Pelanggan (Umum, Sekolah, Instansi, Siswa):**
   - Mengajukan permohonan jasa servis perangkat/jaringan.
   - Melacak perkembangan perbaikan secara transparan.
   - Melakukan pembayaran dan memberikan ulasan/rating bintang.

### 2.3 Batasan Perangkat Keras, Lunak, dan Lingkungan Operasional
- **Server:** Apache 2.4+, PHP 8.2+ (XAMPP Environment).
- **Basis Data:** MariaDB / MySQL 10.4+ InnoDB dengan relasi Foreign Key cascade.
- **Client Browser:** Google Chrome, Mozilla Firefox, Microsoft Edge modern (mendukung ES6 dan CSS Grid).
- **External Integration:** WhatsApp Business Gateway API (Fonnte) & Generative AI API.

### 2.4 Asumsi dan Ketergantungan
- Seluruh data transaksi, pengguna, teknisi, dan order disimpan pada basis data relasional lokal yang selalu terjaga integritas referensialnya.
- Sistem menyediakan *fallback graceful degradation* jika koneksi database/jaringan eksternal mengalami latensi.

---

## BAB III: SPESIFIKASI KEBUTUHAN FUNGSIONAL & USER STORIES

### 3.1 Matriks Kebutuhan Fungsional (Functional Requirements)

| Kode FR | Modul / Fitur | Deskripsi Fungsional | Aktor Terkait |
| :--- | :--- | :--- | :--- |
| **FR-01** | Autentikasi & RBAC | Pengguna dapat masuk ke sistem sesuai kredensial dan peran (Operator, Kepsek, Teknisi, Customer). | Semua Aktor |
| **FR-02** | Lupa Sandi & OTP WA | Pengguna dapat mereset kata sandi melalui pengiriman token verifikasi 6-digit ke nomor WhatsApp. | Semua Aktor |
| **FR-03** | Manajemen Pengguna | Operator dapat menambah, memfilter, melihat direktori, dan mengatur status keaktifan akun pengguna. | Operator |
| **FR-04** | Master Katalog Layanan | Operator dapat mengelola master layanan servis (CCTV, Jaringan, Komputer) beserta estimasi tarif. | Operator |
| **FR-05** | Pengajuan Order Servis | Pelanggan dapat memilih jenis jasa, mendeskripsikan kendala perangkat, menentukan jadwal, dan mengajukan pesanan. | Customer |
| **FR-06** | Penugasan Teknisi | Operator meninjau pesanan masuk, menyetujui, dan mendelegasikan tugas kepada Teknisi Siswa yang tersedia. | Operator |
| **FR-07** | Work Log & Tracking | Teknisi mencatat perkembangan investigasi perbaikan, dan pelanggan dapat memantau status secara live. | Teknisi, Customer |
| **FR-08** | Verifikasi Pembayaran | Operator memverifikasi bukti transaksi pembayaran pelanggan dan menerbitkan status lunas. | Operator, Customer |
| **FR-09** | Rating & Ulasan | Pelanggan memberikan rating bintang 1–5 serta testimoni evaluasi kualitas kerja teknisi siswa. | Customer, Kepsek |
| **FR-10** | Laporan Eksekutif | Kepala Sekolah dapat melihat analitik pendapatan, performa perbaikan, dan kepuasan pelanggan secara holistik. | Kepala Sekolah |
| **FR-11** | AI Asisten Cerdas | AI menjawab pertanyaan operasional, ketersediaan layanan, statistik database secara singkat lalu rinci sesuai permintaan. | Semua Aktor |

### 3.2 User Stories & Acceptance Criteria

#### User Story 1: Manajemen Akun Pengguna (Operator)
- **Sebagai** Operator TKJ,
- **Saya ingin** menambahkan akun pengguna baru dengan formulir modal interaktif di menu Total Pengguna,
- **Sehingga** teknisi siswa atau pelanggan baru dapat segera mengakses sistem tanpa registrasi manual di database.
- **Acceptance Criteria:**
  1. Validasi mencegah username dan email kembar (*unique constraint*).
  2. Password terenkripsi secara otomatis menggunakan algoritma BCRYPT.
  3. Pemilihan role Teknisi otomatis menginisialisasi entri pada tabel `technicians`, dan role Customer pada tabel `customers`.
  4. Flash message berhasil tampil dengan notifikasi jelas.

#### User Story 2: Pemulihan Akun via WhatsApp OTP (Semua Aktor)
- **Sebagai** Pengguna yang lupa kata sandi,
- **Saya ingin** meminta kode OTP yang dikirimkan langsung ke nomor WhatsApp saya,
- **Sehingga** saya dapat memulihkan akses akun secara aman dan mandiri tanpa intervensi admin.
- **Acceptance Criteria:**
  1. Pengguna memasukkan username atau nomor telepon terdaftar.
  2. Sistem men-generate 6 digit OTP acak dengan masa kedaluwarsa 10 menit.
  3. Token dikirim melalui API Gateway WhatsApp Fonnte.
  4. Input OTP yang benar memungkinkan form penggantian password baru terbuka.

#### User Story 3: Konsultasi Data Real-Time Berbasis AI (Semua Aktor)
- **Sebagai** Pengguna sistem (Operator/Customer/Kepsek),
- **Saya ingin** menanyakan data sistem (jumlah teknisi, order aktif, detail pemesanan, harga jasa) ke chatbot AI,
- **Sehingga** saya mendapatkan jawaban instan yang akurat bersumber langsung dari basis data tanpa harus mencari manual di tabel.
- **Acceptance Criteria:**
  1. Pertanyaan awal dijawab secara **singkat dan tepat** (misal: "Ada 3 teknisi yang tercatat dalam database.").
  2. Saat pengguna meminta rincian (misal: "Siapa saja 3 teknisi tersebut?"), AI menyajikan daftar detail berisikan nama, spesialisasi, dan status.
  3. Format output bersih dari simbol Markdown acak atau karakter anomali.

---

## BAB IV: SPESIFIKASI FITUR KECERDASAN ARTIFISIAL (AI) UTAMA

### 4.1 Peran Strategis AI dalam Operasional TEFA
Fitur AI pada **TKJ Service Center** bukan sekadar antarmuka generatif generik (*gimmick*), melainkan bertindak sebagai **Asisten Operasional Berbasis Data (Data-Grounded Intelligent Assistant)**. AI menghubungkan bahasa natural manusia dengan struktur relasional 15 tabel operasional TEFA.

### 4.2 Alur Pemrosesan Query & Query Engine Database Real-Time
1. **Penerimaan Pesan:** Pengguna mengetikkan pesan pada floating widget chat web.
2. **Ekstraksi Entitas & Intent (Pattern Matching & RAG):** Mesin AI menganalisis intent pengguna (misal: inquiry jumlah pengguna, teknisi, pesanan berjalan, status order, daftar harga sparepart, omset).
3. **Query Retrieval Dinamis:** Sistem mengeksekusi *safe prepared statements* ke tabel terkait (`users`, `technicians`, `service_orders`, `services`, `transactions`).
4. **Respon Pola Dua Tahap (Two-Tier Response Pattern):**
   - **Tahap 1 (Ringkas):** Jika pesan merupakan pertanyaan umum/jumlah (*"ada berapa teknisi?"*, *"ada berapa orderan yang selesai?"*), AI merespons dalam 1-2 kalimat padat.
   - **Tahap 2 (Rincian Mendalam):** Jika pesan merupakan instruksi penelusuran lebih lanjut (*"siapa saja teknisi tersebut?"*, *"rincikan orderannya"*), AI memetakan nama entitas, status, kode transaksi, dan nomor telepon.
5. **Sanitasi Output:** Menghilangkan formatting mentah agar tampilan chat bersih, elegan, dan mudah dipahami di layar desktop maupun mobile.

---

## BAB V: KEBUTUHAN NON-FUNGSIONAL (NFR)

### 5.1 Kinerja (Performance)
- Waktu respon pemuatan halaman web rata-rata $\le 1.5$ detik pada jaringan lokal.
- Latensi pemrosesan pertanyaan chatbot AI lokal $\le 500$ ms untuk pembacaan database internal.

### 5.2 Keamanan & Privasi (Security & OTP Gateways)
- **Proteksi Kata Sandi:** Hashing *one-way* menggunakan `password_hash($pwd, PASSWORD_BCRYPT)`.
- **Pencegahan SQL Injection:** 100% interaksi database wajib menggunakan PDO Prepared Statements dengan parameter binding.
- **Pencegahan XSS & CSRF:** Sanitasi output menggunakan `htmlspecialchars()` dan validasi session token pada transaksi kritis.
- **Isolasi Hak Akses:** Setiap controller dilindungi guard autentikasi session yang menolak akses lintas peran (*unauthorized redirect*).

### 5.3 Ketersediaan & Keandalan (Availability & Reliability)
- Ketersediaan sistem ditargetkan 99.5% pada jam operasional sekolah.
- Mekanisme proteksi kesalahan (*try-catch exception handling*) pada koneksi basis data sehingga jika server database terputus, aplikasi menampilkan pesan ramah tanpa membocorkan kredensial sistem.

### 5.4 Kemudahan Penggunaan (Usability)
- Desain antarmuka responsif (*Mobile-First*) dengan panduan warna hierarkis, ikonografi Bootstrap yang konsisten, dan tipografi jelas.

---

## BAB VI: PENUTUP & KELAYAKAN PROYEK
Dokumen PRD/SRS ini membuktikan bahwa aplikasi **TKJ Service Center** dirancang untuk memecahkan persoalan nyata tata kelola unit bisnis vokasi. Integrasi fitur kecerdasan artifisial yang terhubung langsung ke data operasional memberikan diferensiasi teknologi tinggi dan memenuhi kriteria penilaian mutu kurikulum OBE secara optimal.

