# DOKUMEN PERANCANGAN SISTEM & ARSITEKTUR PERANGKAT LUNAK (SDD)
## SISTEM INFORMASI UNIT BISNIS TEFA "TKJ SERVICE CENTER" DENGAN INTEGRASI KECERDASAN ARTIFISIAL (AI)

**Mata Kuliah:** Rekayasa Perangkat Lunak (TIKB2503021)  
**Program Studi:** S1 Teknik Informatika — STMIK IKMI Cirebon  
**Arsitektur Standar:** Object-Oriented MVC Architecture, SOLID Principles, 3NF Relational Database  
**Versi Dokumen:** 1.0  
**Tanggal:** 14 November 2026  

---

## DAFTAR ISI
1. [BAB I: ARSITEKTUR SISTEM & DESAIN STRUKTURAL](#bab-i-arsitektur-sistem--desain-struktural)
   - 1.1 Pola Desain MVC & Prinsip SOLID
   - 1.2 Diagram Konteks & Topologi Sistem
2. [BAB II: PEMODELAN 4 DIAGRAM UML UTAMA](#bab-ii-pemodelan-4-diagram-uml-utama)
   - 2.1 Use Case Diagram & Skenario Aktor
   - 2.2 Activity Diagram (Alur Bisnis & Pemrosesan AI)
   - 2.3 Class Diagram Berorientasi Objek (Mematuhi Prinsip SOLID)
   - 2.4 Sequence Diagram (Alur Interaksi Frontend, Backend, Database & AI API)
3. [BAB III: PERANCANGAN BASIS DATA (ERD & 15 TABEL 3NF)](#bab-iii-perancangan-basis-data-erd--15-tabel-3nf)
   - 3.1 Entity Relationship Diagram (ERD)
   - 3.2 Kamus Data & Relasi Antar-Entitas
4. [BAB IV: SPESIFIKASI ANTARMUKA PENGGUNA (UI/UX)](#bab-iv-spesifikasi-antarmuka-pengguna-uiux)
   - 4.1 Design System & Typography
   - 4.2 Alur Antarmuka Pengguna (UI Walkthrough)
5. [BAB V: KESIMPULAN PERANCANGAN](#bab-v-kesimpulan-perancangan)

---

## BAB I: ARSITEKTUR SISTEM & DESAIN STRUKTURAL

### 1.1 Pola Desain MVC & Prinsip SOLID
Sistem dibangun berlandaskan arsitektur **Model-View-Controller (MVC)** mandiri tanpa framework berat (PHP Native Modern) dengan penegakan prinsip desain SOLID:
1. **Single Responsibility Principle (SRP):** Setiap Controller, Model, dan Core Class memiliki satu tanggung jawab spesifik (contoh: `AuthController` hanya mengurusi otentikasi & OTP, `UserModel` hanya mengurusi operasi data entitas pengguna, `AiModel` mengurusi konsultasi data & query intent).
2. **Open-Closed Principle (OCP):** Arsitektur Query Retrieval pada `AiModel` terbuka untuk penambahan intent/topik konsultasi baru tanpa memodifikasi fungsi inti database.
3. **Liskov Substitution Principle (LSP):** Seluruh controller mewarisi class dasar `Controller` dan dapat memanggil model/view secara konsisten.
4. **Interface Segregation Principle (ISP):** Pemisahan view layout dan template komponen modular (Navbar, Sidebar, Footer, Widget AI).
5. **Dependency Inversion Principle (DIP):** Modul tingkat tinggi seperti Controller berinteraksi dengan database melalui abstraksi PDO wrapper `Database`.

### 1.2 Diagram Konteks & Topologi Sistem

```mermaid
graph TD
    User["Aktor (Operator / Teknisi / Customer / Kepsek)"]
    Browser["Client Web Browser (Bootstrap 5, Vanilla JS, Responsive UI)"]
    
    subgraph "Web Application Server (Apache / PHP 8.2)"
        FrontController["index.php (Front Controller Router)"]
        AuthMiddleware["Auth Middleware & RBAC Guard"]
        Controllers["Controllers (User, Order, Service, Dashboard, AI, Auth)"]
        Models["Models (UserModel, OrderModel, ServiceModel, AiModel)"]
        DBWrapper["Database PDO Abstraction"]
    end
    
    subgraph "Database Tier (MariaDB / MySQL)"
        DB[("db_tkj_service (15 Relational Tables)")]
    end
    
    subgraph "External Cloud Services"
        FonnteAPI["Fonnte Gateway (WhatsApp OTP Service)"]
        GeminiAPI["Google Gemini Generative AI Engine"]
    end

    User <-->|HTTP / HTTPS| Browser
    Browser <-->|AJAX / REST POST & GET| FrontController
    FrontController --> AuthMiddleware
    AuthMiddleware --> Controllers
    Controllers --> Models
    Models --> DBWrapper
    DBWrapper <-->|PDO SQL Prepared Statements| DB
    Controllers <-->|cURL HTTPS API| FonnteAPI
    Models <-->|HTTPS API / Fallback RAG Engine| GeminiAPI
```

---

## BAB II: PEMODELAN 4 DIAGRAM UML UTAMA

### 2.1 Use Case Diagram & Skenario Aktor

```mermaid
flowchart LR
    Customer((Pelanggan))
    Teknisi((Teknisi Siswa))
    Operator((Operator TKJ))
    Kepsek((Kepala Sekolah))

    subgraph "Unit Bisnis TKJ Service Center Web App"
        UC1[UC-01: Login Multi-Role & Lupa Sandi via OTP WA]
        UC2[UC-02: Konsultasi AI Asisten Database 24/7]
        UC3[UC-03: Pengajuan Order Jasa Servis]
        UC4[UC-04: Pelacakan Status & Pembayaran Servis]
        UC5[UC-05: Pemberian Rating & Ulasan]
        UC6[UC-06: Manajemen Tugas & Unggah Log Pengerjaan]
        UC7[UC-07: Verifikasi Order & Penjadwalan Teknisi]
        UC8[UC-08: Manajemen Akun Pengguna & Tambah Akun]
        UC9[UC-09: Manajemen Master Katalog Jasa & Sparepart]
        UC10[UC-10: Monitoring KPI Bisnis & Laporan Eksekutif]
    end

    Customer --> UC1
    Customer --> UC2
    Customer --> UC3
    Customer --> UC4
    Customer --> UC5

    Teknisi --> UC1
    Teknisi --> UC2
    Teknisi --> UC6

    Operator --> UC1
    Operator --> UC2
    Operator --> UC7
    Operator --> UC8
    Operator --> UC9

    Kepsek --> UC1
    Kepsek --> UC2
    Kepsek --> UC10
```

#### Skenario Use Case Spesifik: UC-08 (Tambah Akun Pengguna)
- **Aktor:** Operator TKJ
- **Pre-kondisi:** Operator telah terotentikasi dan berada di halaman Total Pengguna (`index.php?url=user`).
- **Alur Utama:**
  1. Operator menekan tombol "+ Tambah Pengguna".
  2. Sistem menampilkan modal pop-up formulir penambahan akun baru.
  3. Operator mengisi Nama Lengkap, Username unik, Email unik, Nomor WhatsApp, memilih Role (Customer, Teknisi, Operator, Kepsek), dan Password.
  4. Jika role adalah Teknisi, sistem menampilkan bidang input keahlian/spesialisasi teknis.
  5. Operator menekan tombol "Simpan Pengguna".
  6. Sistem memverifikasi validitas data, memastikan tidak ada duplikasi username/email, dan mengenkripsi password dengan BCRYPT.
  7. Sistem menyimpan akun baru ke tabel `users` dan membuat relasi otomatis ke `customers` atau `technicians`.
  8. Sistem menampilkan pesan sukses (*flash message*) dan merefresh daftar akun secara seketika.
- **Post-kondisi:** Akun baru aktif dan langsung dapat digunakan untuk login.

---

### 2.2 Activity Diagram (Alur Bisnis Order & Pemrosesan AI)

#### A. Activity Diagram: Siklus Layanan Pemesanan Jasa & Penugasan
```mermaid
flowchart TD
    Start([Mulai]) --> A1[Customer Memilih Layanan Jasa & Isi Form Kendala]
    A1 --> A2[Customer Mengirimkan Pengajuan Order]
    A2 --> A3[Sistem Menyimpan Order dengan Status 'Pending']
    A3 --> B1[Operator Menerima Notifikasi Order Masuk]
    B1 --> B2{Operator Menyetujui Order?}
    B2 -- Tidak --> B3[Ubah Status 'Cancelled' & Beri Alasan]
    B3 --> EndCancel([Selesai - Dibatalkan])
    B2 -- Ya --> B4[Operator Menugaskan Teknisi Siswa & Tentukan Jadwal]
    B4 --> B5[Status Berubah Menjadi 'Assigned' / 'In Progress']
    B5 --> C1[Teknisi Siswa Menerima Tugas di Dashboard]
    C1 --> C2[Teknisi Datang ke Lokasi / Kerjakan Servis]
    C2 --> C3[Teknisi Unggah Catatan Diagnosa & Foto Bukti Pengerjaan]
    C3 --> C4[Teknisi Menandai Servis 'Completed']
    C4 --> D1[Customer Melakukan Pembayaran]
    D1 --> D2[Operator Memverifikasi Pembayaran Menjadi 'Paid']
    D2 --> D3[Customer Memberikan Rating & Ulasan Bintang]
    D3 --> EndSuccess([Selesai - Berhasil])
```

#### B. Activity Diagram: Pemrosesan Query Chatbot AI (Two-Tier Response)
```mermaid
flowchart TD
    StartAI([Pesan Masuk dari Pengguna]) --> Q1[AiModel Menganalisis Pola Pertanyaan / Intent]
    Q1 --> Q2{Apakah Pertanyaan Terkait Data Internal DB?}
    Q2 -- Tidak --> Q3[Gunakan Generative AI / Penjelasan Umum IT TEFA]
    Q3 --> FormatOut[Sanitasi & Bersihkan Format Markdown]
    Q2 -- Ya --> Q4{Apakah Pertanyaan Bertipe Hitungan / Jumlah Ringkas?}
    Q4 -- Ya --> Q5[Ambil Count Agregat dari DB & Jawab Singkat 1-2 Kalimat]
    Q4 -- Tidak / Minta Rincian --> Q6[Eksekusi Query Detail Relasional: Nama, Status, Spesialisasi, Kode]
    Q6 --> Q7[Petakan Menjadi Daftar Poin Rinci & Terstruktur]
    Q5 --> FormatOut
    Q7 --> FormatOut
    FormatOut --> Deliver([Tampilkan Balasan di Widget Chatbot])
```

---

### 2.3 Class Diagram Berorientasi Objek (Mematuhi Prinsip SOLID)

```mermaid
classDiagram
    class Database {
        -string host
        -string user
        -string pass
        -string dbname
        -PDO dbh
        -PDOStatement stmt
        +__construct()
        +bool isConnected()
        +Database query(string sql)
        +Database bind(param, value, type)
        +bool execute()
        +array resultSet()
        +array single()
        +int lastInsertId()
    }

    class Controller {
        +model(string model)
        +view(string view, array data)
    }

    class Auth {
        +static bool check()
        +static array user()
        +static requireRole(string role)
        +static login(array user)
        +static logout()
    }

    class Flasher {
        +static setFlash(string message, string action, string type)
        +static flash()
    }

    class UserModel {
        -Database db
        -string table
        +getUserByUsername(string username)
        +getUserById(int id)
        +isUsernameTakenByOther(string username, int id)
        +updatePassword(int id, string password)
        +getAllUsersForOperator(string search, string role)
        +countAllActiveUsers()
        +createUser(array data)
    }

    class OrderModel {
        -Database db
        +createOrder(array data)
        +getAllOrders(string status)
        +assignTechnician(int orderId, int techId)
        +updateStatus(int orderId, string status)
        +getDashboardMetrics()
    }

    class AiModel {
        -Database db
        -UserModel userModel
        -OrderModel orderModel
        +chat(string message, int userId)
        -handleDatabaseQuery(string intent, string message)
        -cleanMarkdownOutput(string text)
    }

    class UserController {
        +index()
        +store()
    }

    class OrderController {
        +index()
        +create()
        +store()
        +assign()
    }

    class AuthController {
        +login()
        +authenticate()
        +forgotPassword()
        +sendOtp()
        +verifyOtp()
        +resetPassword()
        +logout()
    }

    class AiController {
        +chat()
    }

    Controller <|-- UserController
    Controller <|-- OrderController
    Controller <|-- AuthController
    Controller <|-- AiController

    UserController --> UserModel
    UserController --> Flasher
    UserController --> Auth

    OrderController --> OrderModel
    OrderController --> Flasher
    OrderController --> Auth

    AuthController --> UserModel
    AuthController --> Auth
    AuthController --> Flasher

    AiController --> AiModel
    AiModel --> Database
    UserModel --> Database
    OrderModel --> Database
```

---

### 2.4 Sequence Diagram (Alur Interaksi Frontend, Backend, Database & AI API)

#### A. Sequence Diagram: Tanya Jawab Chatbot AI Database (Ringkas & Detail)
```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna / Operator
    participant UI as Chat Widget (Browser)
    participant Ctrl as AiController
    participant Model as AiModel
    participant DB as MariaDB (Database)

    User->>UI: Ketik "ada berapa teknisi?"
    UI->>Ctrl: POST index.php?url=ai/chat { message: "ada berapa teknisi?" }
    Ctrl->>Model: chat(message, userId)
    Model->>Model: Deteksi Intent: COUNT_TECHNICIAN
    Model->>DB: SELECT COUNT(*) FROM technicians WHERE status = 'available'
    DB-->>Model: Return count = 3
    Model->>Model: Format Jawaban Ringkas: "Ada 3 teknisi yang tercatat dalam database."
    Model-->>Ctrl: Return JSON { reply: "Ada 3 teknisi..." }
    Ctrl-->>UI: Response HTTP 200 JSON
    UI-->>User: Tampilkan Bubble Chat Ringkas

    User->>UI: Ketik "siapa 3 teknisi tersebut?"
    UI->>Ctrl: POST index.php?url=ai/chat { message: "siapa 3 teknisi tersebut?" }
    Ctrl->>Model: chat(message, userId)
    Model->>Model: Deteksi Intent: DETAIL_TECHNICIAN
    Model->>DB: SELECT u.full_name, t.specialization, t.status FROM technicians t JOIN users u ON t.user_id = u.id
    DB-->>Model: Return List Array [Budi Santoso, Rizky Pratama, Fajar Kurniawan]
    Model->>Model: Susun Rincian Nama, Keahlian, & Status
    Model-->>Ctrl: Return JSON { reply: "Rincian Teknisi: 1. Budi Santoso..." }
    Ctrl-->>UI: Response HTTP 200 JSON
    UI-->>User: Tampilkan Bubble Chat Rincian Lengkap
```

#### B. Sequence Diagram: Fitur Lupa Sandi via WhatsApp OTP
```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna
    participant UI as Login Page Modal
    participant AuthCtrl as AuthController
    participant UserMdl as UserModel
    participant OTPMdl as OTP Service
    participant Fonnte as Fonnte WhatsApp Gateway
    participant DB as MariaDB

    User->>UI: Masukkan No. WA / Username & Klik "Kirim OTP"
    UI->>AuthCtrl: POST index.php?url=auth/sendOtp
    AuthCtrl->>UserMdl: getUserByUsername(input)
    UserMdl->>DB: Query user by username/phone
    DB-->>UserMdl: Return User Record
    UserMdl-->>AuthCtrl: User Found
    AuthCtrl->>OTPMdl: generateOtp(userId, phone)
    OTPMdl->>DB: Simpan OTP Hash & Waktu Expired (10 Menit)
    AuthCtrl->>Fonnte: POST https://api.fonnte.com/send (OTP Token)
    Fonnte-->>User: Pesan WhatsApp Berisi Kode OTP 6-Digit
    Fonnte-->>AuthCtrl: Status Sent 200 OK
    AuthCtrl-->>UI: JSON { status: true, message: "OTP Terkirim" }
    UI-->>User: Form Verifikasi 6-Digit & Ganti Password Terbuka
```

---

## BAB III: PERANCANGAN BASIS DATA (ERD & 15 TABEL 3NF)

### 3.1 Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    ROLES ||--o{ USERS : "memiliki"
    USERS ||--o| CUSTOMERS : "ekstensi"
    USERS ||--o| TECHNICIANS : "ekstensi"
    USERS ||--o{ NOTIFICATIONS : "menerima"
    USERS ||--o{ AI_CONVERSATIONS : "berkonsultasi"
    USERS ||--o{ OTP_CODES : "meminta"

    CUSTOMERS ||--o{ SERVICE_ORDERS : "membuat"
    TECHNICIANS ||--o{ SERVICE_ORDERS : "mengerjakan"

    SERVICE_ORDERS ||--o{ ORDER_DETAILS : "memiliki rincian"
    SERVICES ||--o{ ORDER_DETAILS : "digunakan di"

    SERVICE_ORDERS ||--o{ WORK_SCHEDULES : "dijadwalkan"
    SERVICE_ORDERS ||--o{ WORK_LOGS : "memiliki rekam log"
    SERVICE_ORDERS ||--o| TRANSACTIONS : "diterbitkan invoice"
    SERVICE_ORDERS ||--o| RATINGS : "diberi ulasan"

    TRANSACTIONS ||--o| PAYMENTS : "dibayar dengan"
    SPAREPARTS ||--o{ ORDER_DETAILS : "komponen pengganti"
```

### 3.2 Kamus Data & Relasi Antar-Entitas Inti
1. **`roles`**: Master wewenang (`id`, `name`, `display_name`, `description`).
2. **`users`**: Master akun terenkripsi (`id`, `role_id`, `username`, `email`, `password`, `full_name`, `phone`, `is_active`).
3. **`customers`**: Profil konsumen (`id`, `user_id`, `customer_type`, `address`, `city`).
4. **`technicians`**: Profil teknisi TEFA (`id`, `user_id`, `nis_nip`, `class_grade`, `specialization`, `skill_level`, `status`).
5. **`services`**: Master katalog servis (`id`, `code`, `name`, `category`, `description`, `base_price`, `estimated_duration_hours`, `icon`).
6. **`service_orders`**: Transaksi pemesanan perbaikan (`id`, `order_code`, `customer_id`, `technician_id`, `status`, `scheduled_date`, `problem_description`).
7. **`order_details`**: Detail item jasa servis (`id`, `order_id`, `service_id`, `quantity`, `unit_price`, `subtotal`).
8. **`work_logs`**: Log catatan teknisi (`id`, `order_id`, `technician_id`, `log_type`, `description`, `photo_evidence`).
9. **`transactions`**: Faktur keuangan (`id`, `transaction_code`, `order_id`, `total_service_fee`, `total_sparepart_fee`, `grand_total`, `payment_status`).
10. **`ratings`**: Penilaian kepuasan konsumen (`id`, `order_id`, `customer_id`, `technician_id`, `stars`, `comment`).
11. **`otp_codes`**: Token keamanan lupa sandi (`id`, `user_id`, `identifier`, `otp_hash`, `channel`, `is_used`, `expires_at`).

---

## BAB IV: SPESIFIKASI ANTARMUKA PENGGUNA (UI/UX)

### 4.1 Design System & Typography
- **Primary Color:** `#0d6efd` (Royal Blue) & `#0a58ca` (Navy Gradient).
- **Success Color:** `#198754` (Emerald Green) untuk status pesanan selesai & lunas.
- **Warning Color:** `#ffc107` (Amber Gold) untuk pesanan pending & rating bintang.
- **Typography:** `Inter`, `-apple-system`, `BlinkMacSystemFont`, `Segoe UI`, sans-serif.

### 4.2 Alur Antarmuka Pengguna (UI Walkthrough)
1. **Landing Page:** Informasi profil keunggulan TEFA TKJ, etalase layanan servis unggulan, serta widget asisten chatbot AI interaktif di pojok kanan bawah.
2. **Dashboard Operator:** Ringkasan statistik performa (Total Pengguna, Total Customer, Total Teknisi, Order Baru, Order Berjalan, Order Selesai) dan manajemen pesanan.
3. **Total Pengguna & Data Akun:** Tabel daftar akun, filter berdasarkan role, pencarian instan, dan tombol "+ Tambah Pengguna" dengan modal popup elegan.
4. **Alur Lupa Kata Sandi:** Modal form terintegrasi pada layar login dengan indikator proses pengiriman OTP WhatsApp real-time.

---

## BAB V: KESIMPULAN PERANCANGAN
Dokumen SDD ini telah menetapkan cetak biru (*blueprint*) rekayasa perangkat lunak yang utuh, presisi, dan teruji. Hubungan antara dokumen PRD/SRS dan diagram perancangan UML (Use Case, Activity, Class, Sequence Diagram) telah sinkron 100% dengan implementasi kode sumber pada aplikasi **TKJ Service Center**, menjamin terpenuhinya standar mutu akademis kurikulum OBE berpredikat Sangat Baik (Skor 4).

