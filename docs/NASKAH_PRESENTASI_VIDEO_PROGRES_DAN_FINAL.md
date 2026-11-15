# BUKU PANDUAN NASKAH (SCRIPT) PRESENTASI VIDEO PROYEK AKHIR MANDIRI
## MATA KULIAH: REKAYASA PERANGKAT LUNAK (TIKB2503021) — KURIKULUM OBE
**Aplikasi:** Sistem Informasi Unit Bisnis TEFA "TKJ Service Center" Berbasis Web & AI Cerdas  
**Institusi:** S1 Teknik Informatika — STMIK IKMI Cirebon  
**Dosen Pengampu:** Yudhistira Arie Wijaya, M.Kom., CDS.  

---

> ### ⚠️ PANDUAN PEREKAMAN VIDEO (STANDAR RUBRIK SKOR 4 / SANGAT BAIK):
> 1. **Webcam Wajib Aktif Sepanjang Video:** Wajah Anda wajib tampil jelas di pojok layar (*screen recording + facecam*) untuk memverifikasi keaslian pengerjaan mandiri (*100% individual build*).
> 2. **Kualitas Audio & Visual:** Resolusi video 1080p, suara terdengar tegas, lancar, percaya diri, dan **tidak membaca teks secara kaku**.
> 3. **Live Demo Wajib Real-Time (Khusus Video Final):** Tunjukkan aplikasi berjalan langsung di browser dan terminal, bukan sekadar tangkapan layar (screenshot).
> 4. **Durasi Waktu:** Jaga durasi agar presisi sesuai batas waktu yang ditentukan pada panduan tugas.

---

## BAGIAN I: NASKAH VIDEO PROGRES 1 (DURASI 3–5 MENIT)
**Topik:** Dokumen PRD/SRS Mandiri & Konsep Aplikasi, Kebutuhan Fungsional, serta Peran Strategis Fitur AI  
**Alokasi Waktu Ideal:** 4 Menit (Toleransi: 3:30 – 4:30)  
**Media yang Ditampilkan:** Slide Presentasi / Dokumen PRD PDF (`PRD_TKJ_Service_Center.md`) + Tampilan Beranda Web

---

### [00:00 - 00:45] MENIT 1: PEMBUKAAN & IDENTIFIKASI MASALAH RIIL
* **Visual di Layar:** Slide Judul Proyek, Nama Mahasiswa, NIM, Foto Logo STMIK IKMI Cirebon, dan Webcam di pojok kanan atas.
* **Narasi Anda:**
  > *"Assalamu’alaikum Warahmatullahi Wabarakatuh. Selamat pagi/siang kepada Bapak Yudhistira Arie Wijaya, M.Kom., CDS. selaku dosen pengampu mata kuliah Rekayasa Perangkat Lunak, serta rekan-rekan sekalian.*  
  >  
  > *Perkenalkan, saya **[Nama Anda]**, mahasiswa S1 Teknik Informatika STMIK IKMI Cirebon dengan NIM **[NIM Anda]**. Pada kesempatan Progres Pertama ini, saya akan mempresentasikan dokumen **Product Requirements Document (PRD) / SRS** dari proyek rekayasa perangkat lunak mandiri yang saya bangun secara penuh (full end-to-end), yaitu **TKJ Service Center** — Sistem Informasi Manajemen Unit Bisnis Jasa Teaching Factory (TEFA) SMK Jurusan Teknik Komputer dan Jaringan yang terintegrasi Kecerdasan Artifisial (AI).*  
  >  
  > *Latar belakang proyek ini berakar dari masalah nyata di lapangan: unit bisnis TEFA di SMK mengelola puluhan layanan teknis mulai dari servis komputer, jaringan, hingga instalasi CCTV. Namun, proses pencatatan servis masih manual dan tersebar di chat pribadi, ketiadaan sistem pemantauan kerja teknisi siswa, serta ketiadaan layanan informasi operasional 24 jam bagi pelanggan."*

---

### [00:45 - 01:45] MENIT 2: NILAI GUNA APLIKASI & MULTI-ROLE RBAC
* **Visual di Layar:** Tampilkan diagram stakeholder / tabel 4 Role dan Value Proposition pada dokumen PRD.
* **Narasi Anda:**
  > *"Untuk mengatasi persoalan tersebut, aplikasi TKJ Service Center dirancang menghadirkan nilai guna nyata (value proposition) berupa sentralisasi operasional berbasis web terpadu dengan arsitektur multi-role Role-Based Access Control (RBAC) yang membedakan 4 peran pengguna:*  
  > 1. *Pertama, **Customer atau Pelanggan:** yang dapat memesan layanan secara online, melacak progres servis secara transparan, melakukan pembayaran, hingga memberikan rating kepuasan.*  
  > 2. *Kedua, **Operator TKJ:** bertindak sebagai pengelola sistem, memvalidasi pesanan, mendelegasikan teknisi, mengelola katalog dan inventaris sparepart, serta memiliki fitur eksklusif Tambah Pengguna baru.*  
  > 3. *Ketiga, **Teknisi Siswa TKJ:** sebagai pelaksana lapangan yang mencatat log diagnosa kerja (work logs) dan bukti foto pengerjaan.*  
  > 4. *Dan keempat, **Kepala Sekolah:** pimpinan institusi yang memantau performa bisnis, omset berkala, dan evaluasi kepuasan pelanggan secara eksekutif."*

---

### [01:45 - 03:00] MENIT 3: MATRIKS KEBUTUHAN FUNGSIONAL & NON-FUNGSIONAL
* **Visual di Layar:** Tampilkan tabel Matriks Kebutuhan Fungsional (FR-01 sampai FR-11) pada dokumen PRD.
* **Narasi Anda:**
  > *"Dalam dokumen PRD yang saya susun berbasis standar IEEE 830 dan ISO 29148, terdapat 11 Kebutuhan Fungsional utama yang telah dirumuskan secara terukur:*  
  > *Mulai dari **FR-01** Autentikasi multi-role yang aman, **FR-02** Fitur pemulihan lupa kata sandi terintegrasi WhatsApp OTP Gateway menggunakan Fonnte API, **FR-03** Manajemen direktori akun dan tambah pengguna oleh Operator, hingga modul manajemen order, penugasan teknisi, verifikasi pembayaran, rating bintang, dan laporan KPI omset.*  
  >  
  > *Dari segi Non-Fungsional (NFR), sistem menerapkan standar keamanan tinggi: enkripsi kata sandi satu arah menggunakan algoritma BCRYPT, proteksi 100% dari SQL Injection melalui PDO Prepared Statements dengan parameter binding, sanitasi XSS di setiap view antarmuka, serta performa kueri basis data dengan waktu respon di bawah 500 milidetik."*

---

### [03:00 - 04:15] MENIT 4: PERAN STRATEGIS FITUR KECERDASAN ARTIFISIAL (AI)
* **Visual di Layar:** Tampilkan bab spesifikasi fitur AI pada dokumen PRD atau widget chatbot AI di beranda web.
* **Narasi Anda:**
  > *"Poin diferensiasi paling fundamental pada aplikasi ini adalah kehadiran fitur **Kecerdasan Artifisial (AI) Cerdas Data-Grounded**.*  
  >  
  > *AI di sistem ini bukan sekadar chatbot tempelan atau gimmick yang menjawab hal umum. AI ini terhubung langsung secara real-time ke basis data relasional 15 tabel sistem kita dengan menerapkan **Pola Respon Dua Tahap (Two-Tier Response Pattern)**:*  
  > *Tahap pertama, saat pengguna menanyakan data agregat atau jumlah (misalnya 'ada berapa teknisi?'), AI akan membaca data database dan menjawab secara singkat serta padat dalam satu kalimat.*  
  > *Tahap kedua, jika pengguna meminta rincian lebih lanjut (misalnya 'siapa saja 3 teknisi tersebut?'), barulah AI memetakan query relasional untuk menampilkan daftar detail nama, spesialisasi keahlian, dan status ketersediaannya secara terstruktur dan bersih dari simbol anomali.*  
  >  
  > *Hal ini memberikan efisiensi luar biasa bagi operator maupun pimpinan dalam mengambil keputusan operasional secara cepat selama 24 jam penuh."*

---

### [04:15 - 04:45] PENUTUP PROGRES 1
* **Visual di Layar:** Slide Kesimpulan & Rencana Tahap 2 (Perancangan UML & Arsitektur SDD).
* **Narasi Anda:**
  > *"Sebagai penutup, dokumen PRD ini telah menjadi fondasi spesifikasi yang kokoh dan siap dilanjutkan ke Tahap 2, yaitu perancangan arsitektur berorientasi objek dan diagram UML utama. Seluruh berkas PRD lengkap telah saya dokumentasikan di repositori GitHub mandiri saya.*  
  >  
  > *Terima kasih atas perhatian Bapak Dosen dan rekan-rekan sekalian. Wassalamu’alaikum Warahmatullahi Wabarakatuh."*

---
---

## BAGIAN II: NASKAH VIDEO PROGRES 2 (DURASI 5–7 MENIT)
**Topik:** Dokumen SDD, Penjelasan 4 Diagram UML (Use Case, Activity, Class, Sequence), ERD Basis Data 15 Tabel 3NF, & Prototipe Interaktif UI/UX Figma  
**Alokasi Waktu Ideal:** 6 Menit (Toleransi: 5:30 – 6:30)  
**Media yang Ditampilkan:** Gambar Diagram UML PNG HD (`1_use_case_diagram.png`, `2_activity_diagram.png`, `3_class_diagram.png`, `4_sequence_diagram.png`, `5_erd_basis_data.png`) + Prototipe UI/UX Figma / Antarmuka Web

---

### [00:00 - 00:45] MENIT 1: SALAM PEMBUKA & INTRO ARSITEKTUR SDD
* **Visual di Layar:** Slide Judul Video Progres 2, Identitas Mahasiswa, dan Cuplikan Diagram UML & ERD.
* **Narasi Anda:**
  > *"Assalamu’alaikum Warahmatullahi Wabarakatuh. Selamat berjumpa kembali dengan saya, **[Nama Anda]**, NIM **[NIM Anda]**, mahasiswa S1 Teknik Informatika STMIK IKMI Cirebon.*  
  >  
  > *Pada presentasi Progres Kedua mata kuliah Rekayasa Perangkat Lunak ini, saya akan membedah secara komprehensif dokumen **Software Design Document (SDD)** dari aplikasi **TKJ Service Center**.*  
  >  
  > *Sesuai panduan kurikulum OBE, hari ini saya akan memaparkan **4 Diagram UML Utama (Use Case, Activity, Class, Sequence)**, dilanjutkan dengan **ERD Basis Data Relasional 15 Tabel 3NF**, serta **Prototipe Interaktif UI/UX Figma** yang menjadi acuan desain antarmuka aplikasi."*

---

### [00:45 - 01:45] MENIT 2: USE CASE DIAGRAM (DISTRIBUSI AKTOR & FITUR AI)
* **Visual di Layar:** Buka gambar `docs/diagrams/1_use_case_diagram.png`. Arahkan kursor mouse ke aktor kiri, kanan, dan kotak AI luar.
* **Narasi Anda:**
  > *"Diagram pertama adalah **Use Case Diagram**. Sesuai standar tata letak dan batasan sistem, diagram ini membagi aktor ke dalam posisi yang simetris:*  
  > - *Di sisi sebelah kiri, terdapat aktor **Customer** yang berinteraksi dengan use case pengajuan order, pelacakan servis, pembayaran, dan rating; serta aktor **Operator TKJ** yang menangani verifikasi order, penugasan teknisi, katalog jasa, dan use case kunci **Tambah Akun Pengguna Baru**.*  
  > - *Di sisi sebelah kanan, terdapat aktor **Teknisi Siswa** yang mengelola tugas servis serta upload catatan log pengerjaan; dan aktor **Kepala Sekolah** yang mengakses monitoring KPI bisnis dan laporan omset.*  
  >  
  > *Perhatikan di bagian bawah: terdapat **External Sub-System Boundary** khusus untuk **Fitur AI Cerdas** yang berada di luar sistem utama. Sub-sistem ini memuat UC-10 Konsultasi AI Database 24/7 dan UC-11 External Generative AI Gateway, di mana keempat aktor dapat berkonsultasi langsung ke asisten cerdas ini."*

---

### [01:45 - 02:45] MENIT 3: ACTIVITY DIAGRAM (SWIMLANE BISNIS & KEPUTUSAN AI)
* **Visual di Layar:** Buka gambar `docs/diagrams/2_activity_diagram.png`. Tunjukkan alur Swimlane di sisi kiri, lalu geser ke alur AI di sisi kanan.
* **Narasi Anda:**
  > *"Diagram kedua adalah **Activity Diagram**, yang saya bagi menjadi dua area komprehensif:*  
  > *Pada area sebelah kiri, saya memodelkan alur bisnis siklus pemesanan jasa perbaikan menggunakan **3 Swimlane**: Customer, Operator, dan Teknisi Siswa. Alur dimulai dari Customer mengajukan order, Operator memeriksa kelayakan dan mengambil keputusan: jika dibatalkan pesanan selesai, namun jika disetujui status menjadi In Progress dan didelegasikan ke Teknisi Siswa. Teknisi mengunggah bukti log kerja dan menyelesaikan servis, Customer melakukan transfer pembayaran, Operator memverifikasi status menjadi Lunas, dan diakhiri dengan Customer memberikan rating bintang kepuasan.*  
  >  
  > *Sementara di area sebelah kanan, dimodelkan **Alur Logika Pengambilan Keputusan AI**: Pesan masuk dianalisis intent-nya. Jika pertanyaan mengacu ke data internal dan berupa hitungan agregat, AI mengambil jalur **Tahap 1: Respon Singkat** (eksekusi kueri COUNT). Jika meminta detail, AI mengambil jalur **Tahap 2: Respon Rincian**. Seluruh output disanitasi dari simbol markdown mentah sebelum ditampilkan rapi ke antarmuka chat."*

---

### [02:45 - 03:45] MENIT 4: CLASS DIAGRAM & PENEGAKAN 5 PRINSIP SOLID
* **Visual di Layar:** Buka gambar `docs/diagrams/3_class_diagram.png`. Sorot baris Core, Controller, Model, dan panel SOLID di bawah.
* **Narasi Anda:**
  > *"Diagram ketiga adalah **Class Diagram**, yang memodelkan struktur berorientasi objek aplikasi dan disusun mematuhi **5 Prinsip SOLID**:*  
  > - *Di baris atas, terdapat Core Engine: class `Database` sebagai wrapper PDO prepared statements, class abstrak `Controller`, class middleware `Auth`, dan helper `Flasher`.*  
  > - *Di baris tengah, terdapat layer Controller: `UserController`, `OrderController`, `AuthController`, dan `AiController` yang mewarisi class Controller.*  
  > - *Di baris ketiga, terdapat layer Model data: `UserModel`, `OrderModel`, dan `AiModel` yang berinteraksi dengan 15 tabel relasional.*  
  >  
  > *Bagaimana prinsip SOLID ditegakkan?*  
  > 1. *Single Responsibility: Setiap kelas punya satu tanggung jawab tunggal. AuthController khusus otentikasi, UserModel khusus manipulasi akun, AiModel khusus query intent.*  
  > 2. *Open-Closed: AiModel terbuka untuk penambahan kueri topik baru tanpa perlu merombak fungsi controller dasar.*  
  > 3. *Liskov Substitution: Semua controller dapat saling menggantikan dalam menjalankan perutean model dan view.*  
  > 4. *Interface Segregation: Antarmuka view dipecah menjadi komponen parsial yang independen.*  
  > 5. *Dependency Inversion: Controller bergantung pada abstraksi class Database berbasis PDO, bukan ketergantungan SQL langsung."*

---

### [03:45 - 04:45] MENIT 5: SEQUENCE DIAGRAM (AI QUERY & WHATSAPP OTP)
* **Visual di Layar:** Buka gambar `docs/diagrams/4_sequence_diagram.png`, perlihatkan pertukaran pesan Skenario A dan Skenario B.
* **Narasi Anda:**
  > *"Diagram keempat adalah **Sequence Diagram**, yang memetakan pertukaran pesan real-time antar-komponen untuk dua skenario kunci:*  
  > *Pada **Skenario A**, ditunjukkan siklus kueri Chatbot AI: dari pengguna mengetik pertanyaan, widget frontend mengirim AJAX POST ke AiController, diteruskan ke AiModel untuk kueri ke MariaDB, dan mengembalikan respon JSON singkat lalu berlanjut ke respon rinci.*  
  > *Pada **Skenario B**, digambarkan alur Lupa Kata Sandi via WhatsApp OTP Gateway: pengguna memasukkan nomor telepon, AuthController memvalidasi dan menyimpan token 6 digit ke tabel `otp_codes`, lalu memanggil API Fonnte via cURL HTTPS hingga pesan OTP masuk ke smartphone pengguna."*

---

### [04:45 - 06:00] MENIT 6: ERD BASIS DATA (15 TABEL 3NF) & PROTOTIPE UI/UX FIGMA
* **Visual di Layar:** Buka gambar `docs/diagrams/5_erd_basis_data.png`, kemudian beralih membuka Figma / browser antarmuka UI web.
* **Narasi Anda:**
  > *"Selanjutnya, mari kita lihat **Entity Relationship Diagram (ERD) Basis Data Relasional 15 Tabel (Normalisasi 3NF)**:*  
  > *Basis data `db_tkj_service` dirancang secara presisi:*  
  > *Tabel `roles` berelasi ke `users`, yang diwariskan ke entitas khusus `customers` dan `technicians`. Tabel `service_orders` menjadi sentral transaksi yang menghubungkan pelanggan, teknisi, rincian `order_details`, katalog `services`, jadwal `work_schedules`, rekam jejak `work_logs`, suku cadang `spareparts`, faktur `transactions`, bukti bayar `payments`, ulasan `ratings`, serta tabel keamanan `otp_codes` dan riwayat `ai_conversations`.*  
  >  
  > *Terakhir, untuk **Prototipe Interaktif UI/UX Figma**:*  
  > *(Tampilkan rancangan Figma / antarmuka UI Web)*  
  > *Desain antarmuka mengadopsi Design System modern dengan pendekatan mobile-first responsif: tata letak card-based, palet warna berhierarki (Navy Dark, Royal Blue, Emerald Green, Amber Warning), tipografi Segoe UI & Inter yang tajam, serta komponen modal pop-up interaktif untuk Tambah Pengguna dan Lupa Sandi OTP."*

---

### [06:00 - 06:30] PENUTUP PROGRES 2
* **Visual di Layar:** Slide Penutup & Tautan Repositori GitHub.
* **Narasi Anda:**
  > *"Dengan tuntasnya pemodelan 4 Diagram UML, ERD 15 tabel, dan prototipe UI/UX ini, seluruh cetak biru sistem telah selaras 100% dengan implementasi kode aplikasi nyata. Pada video final berikutnya, saya akan menyajikan Live Demo utuh aplikasi dan pengujian otomatisnya.*  
  >  
  > *Terima kasih atas perhatian Bapak Yudhistira dan rekan-rekan sekalian. Wassalamu’alaikum Warahmatullahi Wabarakatuh."*

---
---

## BAGIAN III: NASKAH VIDEO FINAL & LIVE DEMO LENGKAP (DURASI 8–12 MENIT)
**Topik:** Elevator Pitch, Arsitektur Sistem, Live Demo Seluruh Fitur, Demo AI Real-Time, Pengujian Otomatis & CI/CD Pipeline  
**Alokasi Waktu Ideal:** 10 Menit (Toleransi: 9:00 – 11:30)  
**Media yang Ditampilkan:** Aplikasi Web Live di Browser (`http://localhost/ADCom/`), Database di phpMyAdmin, Terminal Eksekusi Test, dan Halaman GitHub Actions.

---

### [00:00 - 01:15] MENIT 1: ELEVATOR PITCH & PEMBUKAAN FORMAL
* **Visual di Layar:** Wajah di webcam aktif pojok kanan atas, latar belakang layar menampilkan landing page web TKJ Service Center.
* **Narasi Anda:**
  > *"Assalamu’alaikum Warahmatullahi Wabarakatuh. Selamat pagi/siang kepada Bapak Yudhistira Arie Wijaya, M.Kom., CDS. selaku dosen pengampu mata kuliah Rekayasa Perangkat Lunak, serta seluruh civitas akademika STMIK IKMI Cirebon.*  
  >  
  > *Saya **[Nama Anda]**, NIM **[NIM Anda]**, dengan bangga mempersembahkan presentasi Final dan Live Demo dari proyek akhir mandiri saya: **Sistem Informasi Unit Bisnis Jasa Teaching Factory 'TKJ Service Center' Berbasis Web dengan Integrasi Kecerdasan Artifisial (AI)**.*  
  >  
  > *Aplikasi ini merupakan solusi Full-Stack Software Engineering yang dibangun 100% secara mandiri (individual ownership) untuk mentransformasikan pengelolaan unit bisnis vokasi sekolah. Menggabungkan arsitektur MVC native, 15 tabel relasional ternormalisasi 3NF, keamanan multi-role RBAC 4 aktor, verifikasi pemulihan akun via WhatsApp OTP Gateway Fonnte, serta asisten AI operasional cerdas yang membaca database secara real-time."*

---

### [01:15 - 02:30] MENIT 2: HIGHLIGHT ARSITEKTUR & STRUKTUR KODE
* **Visual di Layar:** Buka VS Code / text editor, perlihatkan struktur direktori folder (`app/controllers/`, `app/models/`, `core/`, `tests/`, `.github/workflows/`).
* **Narasi Anda:**
  > *"Sebelum masuk ke live demo, mari kita lihat arsitektur kode di balik sistem ini. Proyek ini dibangun dengan struktur yang sangat bersih mematuhi prinsip Clean Code dan SOLID:*  
  > *Di folder `core/`, terdapat class `Database.php` sebagai PDO abstraction wrapper yang menjamin seluruh kueri menggunakan prepared statements kebal dari SQL Injection, class middleware `Auth.php`, dan helper `Flasher.php`.*  
  > *Di folder `app/controllers/`, tanggung jawab dipisahkan secara modular: `AuthController` untuk autentikasi dan OTP, `UserController` untuk direktori dan penambahan akun, `OrderController` untuk siklus pemesanan, dan `AiController` untuk pemrosesan konsultasi cerdas.*  
  > *Di folder `app/models/`, seluruh manipulasi data dilakukan secara terpusat oleh `UserModel`, `OrderModel`, dan `AiModel`.*  
  > *Selain itu, sudah tersedia skrip automated unit testing di folder `tests/` dan alur CI/CD pipeline GitHub Actions pada folder `.github/workflows/`."*

---

### [02:30 - 04:30] MENIT 3 & 4: LIVE DEMO OPERATOR & FITUR TAMBAH PENGGUNA
* **Visual di Layar:** Buka browser di `http://localhost/ADCom/`. Login sebagai Operator (`operator` / `operator456`). Masuk ke Dashboard Operator, lalu klik menu **Total Pengguna**.
* **Narasi Anda:**
  > *"Sekarang kita mulai Live Demo aplikasinya secara langsung.*  
  >  
  > *Pertama, saya masuk sebagai **Operator TKJ** menggunakan akun `operator`. Di dashboard operator, kita dapat memantau kartu metrik KPI bisnis: Total Pengguna, Total Customer, Total Teknisi, Order Baru, Order Berjalan, dan Order Selesai.*  
  >  
  > *Sekarang saya buka halaman **Daftar Akun & Total Pengguna**. Di sini terdapat tombol baru yaitu **'+ Tambah Pengguna'**. Mari kita demokan penambahan pengguna baru secara live.*  
  > *(Klik tombol Tambah Pengguna $\rightarrow$ Modal popup muncul)*  
  >  
  > *Saya masukkan nama: 'Rian Hidayat, S.Kom', username: 'rian_teknisi', email: 'rian@tkj.sch.id', nomor WhatsApp: '081234567899', saya pilih role 'Teknisi Siswa TKJ'. Perhatikan saat saya memilih role Teknisi, form otomatis memunculkan input spesialisasi keahlian: saya isi 'Instalasi CCTV & Jaringan Fiber'. Saya masukkan password, lalu klik Simpan Pengguna.*  
  > *(Klik Simpan $\rightarrow$ Muncul notifikasi hijau Berhasil)*  
  >  
  > *Sistem memvalidasi keunikan username dan email, mengenkripsi password dengan hash BCRYPT, dan langsung memasukkan data relasional ke tabel `technicians`. Akun baru langsung tampil aktif di tabel direktori pengguna!"*

---

### [04:30 - 06:15] MENIT 5 & 6: LIVE DEMO ALUR ORDER CUSTOMER & PENUGASAN TEKNISI
* **Visual di Layar:** Logout dari Operator. Login sebagai Customer (`customer` / `customer123`). Buka katalog jasa dan buat pesanan servis. Lalu login kembali sebagai Operator untuk penugasan, dan login sebagai Teknisi (`teknisi` / `teknisi789`) untuk melihat tugas.
* **Narasi Anda:**
  > *"Selanjutnya, mari kita uji siklus pemesanan servis dari sisi Customer.*  
  > *Saya login sebagai akun `customer`. Di dashboard pelanggan, saya klik 'Pesan Layanan Jasa'. Saya pilih layanan 'Instalasi CCTV Analog & DVR 4 Channel', mengisi deskripsi kendala: 'Perlu pasang 4 titik kamera di ruang lab TKJ', menentukan tanggal pengerjaan, dan mengirimkan pesanan.*  
  >  
  > *Status pesanan awal tercatat sebagai **Pending**.*  
  >  
  > *Sekarang saya berpindah kembali ke akun **Operator**. Pada menu 'Kelola Order Masuk', pesanan tadi muncul. Operator memverifikasi kelayakan masalah, menyetujui order, dan mendisposisikan tugas kepada Teknisi Siswa yang tersedia, misalnya kepada 'Budi Santoso'. Status otomatis berubah menjadi **In Progress**.*  
  >  
  > *Ketika saya masuk sebagai akun Teknisi `teknisi`, tugas baru ini langsung tampil di dashboard-nya. Teknisi dapat memperbarui catatan investigasi (work logs), mengunggah bukti foto pengerjaan, dan menandai servis menjadi **Completed**. Setelah itu, pelanggan mengunggah bukti pembayaran yang kemudian divalidasi oleh operator menjadi Lunas (Paid) dan diakhiri dengan pelanggan memberikan rating bintang kepuasan."*

---

### [06:15 - 07:15] MENIT 7: LIVE DEMO LUPA KATA SANDI VIA WHATSAPP OTP GATEWAY
* **Visual di Layar:** Buka halaman Login (`http://localhost/ADCom/index.php?url=auth/login`). Klik tombol 'Lupa Password?'. Masukkan nomor WhatsApp atau username.
* **Narasi Anda:**
  > *"Fitur keamanan unggulan berikutnya adalah pemulihan akun melalui **WhatsApp OTP Gateway**.*  
  > *Pada halaman login, pengguna yang lupa sandi cukup mengklik tautan 'Lupa Password?'. Modal pop-up modern akan muncul.*  
  >  
  > *Saya masukkan nomor WhatsApp terdaftar, lalu klik 'Kirim Kode OTP ke WhatsApp'.*  
  > *(Klik tombol $\rightarrow$ Indikator loading berjalan $\rightarrow$ Notifikasi sukses muncul)*  
  >  
  > *Sistem secara instan men-generate token 6 digit acak yang disimpan aman dengan hash di tabel `otp_codes` dengan masa berlaku 10 menit, dan menembakkannya secara real-time melalui HTTPS cURL ke API Gateway Fonnte langsung ke nomor WhatsApp pengguna.*  
  > *Formulir input 6-digit OTP dan password baru terbuka. Pengguna cukup memasukkan kode yang diterima di ponselnya dan akun berhasil dipulihkan dengan aman."*

---

### [07:15 - 09:30] MENIT 8 & 9: LIVE DEMO FITUR AI DATABASE REAL-TIME (TWO-TIER RESPONSE)
* **Visual di Layar:** Buka widget floating chat AI di pojok kanan bawah. Ketikkan pertanyaan bertahap sesuai instruksi PRD.
* **Narasi Anda:**
  > *"Sekarang kita masuk ke fitur yang paling menarik dan menjadi pemenuhan standar tertinggi kurikulum OBE: **Asisten AI Cerdas Database Real-Time**.*  
  >  
  > *Saya klik widget floating chat AI di pojok kanan bawah. Mari kita buktikan bahwa AI ini benar-benar membaca basis data operasional secara langsung dengan menerapkan **Pola Respon Dua Tahap (Two-Tier Response Pattern)**:*  
  >  
  > *Uji coba pertama: Saya ajukan pertanyaan agregat atau jumlah:*  
  > *(Ketik:* `'ada berapa teknisi?'` *$\rightarrow$ Tekan Kirim)*  
  > *Perhatikan responnya: AI langsung menjawab dengan **singkat dan padat**: **'Ada 3 teknisi yang tercatat dalam database.'** Sangat presisi tanpa basa-basi!*  
  >  
  > *Uji coba kedua: Sekarang saya minta rincian dari data tersebut:*  
  > *(Ketik:* `'siapa 3 teknisi tersebut?'` *$\rightarrow$ Tekan Kirim)*  
  > *Luar biasa! AI langsung mengeksekusi kueri relasional tabel teknisi dan pengguna, lalu menyajikan **jawaban rincian yang terstruktur**: menampilkan Nama lengkap teknisi, status ketersediaan, dan bidang keahlian spesialisasi masing-masing secara berpoin.*  
  >  
  > *Uji coba ketiga: Mari kita uji pertanyaan orderan yang telah selesai:*  
  > *(Ketik:* `'ada berapa orderan yang telah selesai'` *$\rightarrow$ Tekan Kirim)*  
  > *AI menjawab ringkas: 'Ada 1 order yang sudah selesai dalam database.'*  
  > *(Ketik:* `'siapa saja orderan yang telah selesai itu'` *$\rightarrow$ Tekan Kirim)*  
  > *AI langsung membeberkan rincian lengkap pesanan yang selesai, nama pemesan, dan layanannya.*  
  >  
  > *Perhatikan juga antarmukanya: respon AI tersaji sangat rapi, bersih dari simbol bintang markdown mentah, dengan balon chat modern yang sangat responsif!"*

---

### [09:30 - 10:45] MENIT 10: LIVE PENGUJIAN UNIT TESTING & GITHUB ACTIONS CI/CD
* **Visual di Layar:** Buka Terminal / PowerShell. Jalankan perintah unit test. Kemudian buka tab browser menampilkan repositori GitHub dan tab GitHub Actions.
* **Narasi Anda:**
  > *"Untuk membuktikan keandalan rekayasa perangkat lunak dan memenuhi kriteria Tahap 3 Testing & DevOps, saya telah menyusun rangkaian **Automated Unit Testing**.*  
  >  
  > *Mari kita jalankan pengujian otomatisnya secara live melalui terminal:*  
  > *(Jalankan perintah:* `php tests/run_tests.php` *di terminal)*  
  >  
  > *Bisa kita lihat bersama di terminal: pengujian mencakup wrapper Database, integritas data UserModel, ketersediaan 8 akun seed, validasi form tambah pengguna, enkripsi token OTP, hingga pemrosesan query AiModel. Hasilnya: **10 PASS, 0 FAIL — Status: ALL TESTS PASSED!***  
  >  
  > *Sekarang saya beralih ke halaman repositori GitHub pribadi saya. Di tab **GitHub Actions**, pipeline CI/CD yang didefinisikan pada `.github/workflows/ci.yml` berjalan secara otomatis pada lingkungan virtual Ubuntu dengan MySQL 8.0 container. Seluruh tahapan linting dan unit testing terverifikasi sukses dengan **indikator checklist hijau (Green Build)**!"*

---

### [10:45 - 11:30] MENIT 11: REFLEKSI PEMBELAJARAN MANDIRI & PENUTUP
* **Visual di Layar:** Kembali menampilkan webcam wajah dan halaman repositori GitHub / Dokumen Laporan Akhir.
* **Narasi Anda:**
  > *"Sebagai refleksi pembelajaran mandiri dalam pengerjaan proyek akhir ini, saya merasakan pengalaman yang sangat berharga dalam menerapkan siklus hidup rekayasa perangkat lunak (SDLC) secara komprehensif — mulai dari menganalisis kebutuhan riil menjadi dokumen PRD, memodelkan arsitektur dengan 4 diagram UML yang mematuhi prinsip SOLID, mengonstruksi kode MVC bersih, menanamkan fitur AI cerdas yang membaca database, hingga mengotomatisasi pengujian dengan pipeline CI/CD modern.*  
  >  
  > *Seluruh berkas laporan resmi (PDF tunggal), repositori publik GitHub, dan tautan rekaman video ini telah saya cantumkan secara lengkap sesuai panduan kurikulum OBE.*  
  >  
  > *Demikian presentasi final dan live demo proyek mandiri saya. Saya mengucapkan terima kasih yang sebesar-besarnya kepada Bapak Yudhistira Arie Wijaya atas bimbingan dan ilmu yang telah diberikan sepanjang perkuliahan Rekayasa Perangkat Lunak ini. Mohon maaf atas segala kekurangan.*  
  >  
  > *Wabillahi taufiq wal hidayah, Wassalamu’alaikum Warahmatullahi Wabarakatuh."*
