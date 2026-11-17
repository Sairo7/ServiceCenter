# TKJ Service Center - Unit Bisnis Jasa TEFA Berbasis Web & AI

[![TKJ Service Center CI/CD](https://github.com/username/proyek-rpl-nama-aplikasi/actions/workflows/ci.yml/badge.svg)](https://github.com/username/proyek-rpl-nama-aplikasi/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/MariaDB-10.4%2B-003545?logo=mariadb&logoColor=white)](https://mariadb.org)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com)

Aplikasi Sistem Informasi Manajemen Unit Bisnis Jasa *Teaching Factory* (TEFA) Jurusan Teknik Komputer dan Jaringan (TKJ) berbasis web dengan arsitektur MVC mandiri, basis data relasional 15 tabel (3NF), keamanan Multi-Role RBAC 4 peran, pemulihan akun via WhatsApp OTP Gateway (Fonnte API), serta **Asisten Kecerdasan Artifisial (AI) Cerdas Data-Grounded** yang terhubung langsung ke basis data secara *real-time*.

---

## 👤 IDENTITAS MAHASISWA & PROYEK
* **Nama Mahasiswa:** Sairo
* **NIM:** 41257157
* **Kelompok:** Cirtim Pride
* **Tema Topik Kelompok:** Sistem Informasi Berbasis Web untuk Pemberdayaan UMKM Cerdas / Unit Bisnis Pendidikan Vokasi TEFA
* **Mata Kuliah:** Rekayasa Perangkat Lunak (TIKB2503021) — 4 SKS
* **Institusi:** Program Studi S1 Teknik Informatika — STMIK IKMI Cirebon
* **Dosen Pengampu:** Yudhistira Arie Wijaya, M.Kom., CDS.

---

## 🎬 TAUTAN VIDEO PRESENTASI TUGAS (KURIKULUM OBE)

| Tahapan Video | Topik Pembahasan | Durasi | Tautan Video Presentasi |
| :--- | :--- | :---: | :--- |
| **Video Progres 1** | PRD & Konsep Aplikasi (Masalah, Nilai Guna, Kebutuhan & Fitur AI) | 3–5 Menit | [Tautan Video Progres 1 (YouTube / GDrive)](#) |
| **Video Progres 2** | Pemodelan 4 Diagram UML & Arsitektur Sistem Berorientasi Objek | 5–7 Menit | [Tautan Video Progres 2 (YouTube / GDrive)](#) |
| **Video Final** | Live Demo Aplikasi Utuh, AI Real-Time, Automated Unit Testing & CI/CD | 8–12 Menit | [Tautan Video Final Live Demo (YouTube / GDrive)](#) |

*Catatan:* Rekaman video mematuhi standar kurikulum OBE dengan perekaman layar resolusi tinggi (1080p) dan **webcam wajah mahasiswa aktif** di pojok layar sepanjang presentasi.

---

## 🚀 FITUR UTAMA SISTEM
1. **Multi-Role RBAC (4 Aktor):**
   - **Operator:** Kelola katalog jasa, inventaris sparepart, penugasan teknisi, verifikasi transaksi, dan manajemen akun pengguna (**Fitur Tambah Pengguna**).
   - **Kepala Sekolah:** Monitoring eksekutif, rekapitulasi omset, kepuasan pelanggan, dan produktivitas teknisi siswa.
   - **Teknisi Siswa:** Menerima disposisi penugasan servis, memperbarui status pengerjaan, dan mencatat log diagnosa serta foto bukti pengerjaan.
   - **Customer:** Pengajuan pesanan perbaikan perangkat/jaringan, pelacakan status servis *live*, pembayaran, dan rating/ulasan bintang 1–5.
2. **Pemulihan Akun via WhatsApp OTP:** Pengiriman kode verifikasi 6-digit sekali pakai langsung ke WhatsApp pengguna menggunakan API Gateway Fonnte.
3. **Data-Grounded Real-Time AI Assistant:** Chatbot AI asisten cerdas yang terhubung ke database operasional dengan pola respon dua tahap (*Two-Tier Response Logic*): menjawab singkat padat pada pertanyaan agregat, dan menyajikan rincian lengkap saat diminta pengguna.

---

## 🛠️ PANDUAN INSTALASI LOKAL (SETUP INSTRUCTIONS)

### Prasyarat Sistem:
- XAMPP for Windows (PHP 8.2+, Apache 2.4+, MySQL/MariaDB 10.4+)
- Web Browser modern (Google Chrome, Edge, atau Firefox)

### Langkah Pemasangan:
1. **Clone / Tempatkan Proyek:**  
   Pindahkan repositori ini ke dalam direktori root XAMPP:  
   `C:\xampp\htdocs\ADCom`
2. **Impor Basis Data:**  
   - Buka XAMPP Control Panel dan aktifkan **Apache** dan **MySQL**.
   - Buka phpMyAdmin di browser: `http://localhost/phpmyadmin/`.
   - Buat basis data baru bernama `db_tkj_service`.
   - Impor berkas SQL dari: `C:\xampp\htdocs\ADCom\database\schema.sql`.
3. **Jalankan Aplikasi:**  
   Buka peramban web dan akses alamat:  
   `http://localhost/ADCom/`

---

## 🧪 PENGUJIAN OTOMATIS (UNIT TESTING) & CI/CD

Jalankan skrip pengujian otomatis melalui terminal PHP CLI:
```bash
php tests/run_tests.php
```
**Hasil Pengujian:**  
`Total Pengujian: 10 | Status: 10 PASS / 0 FAIL | CI/CD Pipeline: Green Status (Passing)`

---

## 📚 DOKUMENTASI PROYEK (LENGKAP)
- **Laporan Akhir Terpadu (Capstone Report):** [docs/LAPORAN_AKHIR_PROYEK_TKJ_SERVICE_CENTER.md](docs/LAPORAN_AKHIR_PROYEK_TKJ_SERVICE_CENTER.md)
- **Dokumen PRD & SRS Standar IEEE 830:** [docs/PRD_dan_4_Diagram_UML.md](docs/PRD_dan_4_Diagram_UML.md)
- **Berkas Diagram UML Resolusi Tinggi (PNG):**
  1. [Use Case Diagram (docs/diagrams/1_use_case_diagram.png)](docs/diagrams/1_use_case_diagram.png)
  2. [Activity Diagram (docs/diagrams/2_activity_diagram.png)](docs/diagrams/2_activity_diagram.png)
  3. [Class Diagram (docs/diagrams/3_class_diagram.png)](docs/diagrams/3_class_diagram.png)
  4. [Sequence Diagram (docs/diagrams/4_sequence_diagram.png)](docs/diagrams/4_sequence_diagram.png)

