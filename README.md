<p align="center">
  <img src="public/assets/LOGO CMD.png" width="200" alt="Capella Multidana Logo">
</p>

<h1 align="center">Sistem Pengajuan Pembiayaan — PT Capella Multidana</h1>

<p align="center">
  <strong>Internal Portal Pengajuan &amp; Persetujuan Kredit Kendaraan dan Multiguna</strong><br>
  Dibuat untuk memenuhi Coding Test IT Department PT Capella Multidana.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-v3-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Tests-18%20Passed%20(115%20Assertions)-10B981?style=for-the-badge" alt="Tests">
</p>

---

## 📌 Ringkasan Proyek

Portal aplikasi web **internal** ini dirancang khusus bagi tim operasional **PT Capella Multidana (CMD)** untuk mengelola seluruh siklus pengajuan kredit nasabah (Sepeda Motor, Mobil, dan Multiguna). Sistem menerapkan prinsip **Pemisahan Peran (_Maker-Checker / Four-Eyes Principle_)** antara petugas pencatat pengajuan dan petugas komite pemutus kredit, yang merupakan standar tata kelola pembiayaan yang baik.

Antarmuka dibangun dengan pendekatan: responsif di seluruh perangkat, filter tabel instan (0ms latency), kalkulasi angsuran finansial akurat dengan tabel amortisasi lengkap, dialog konfirmasi approve/reject interaktif, palet warna resmi CMD (_Solid Warm Honey Gold_ `#E59E15` & _Midnight Navy_ `#0B132B`), dan toast notifikasi dengan progress bar animasi.

---

## 🛠️ Stack Teknologi

| Layer                       | Teknologi             | Keterangan                               |
| :-------------------------- | :-------------------- | :--------------------------------------- |
| **Backend Framework**       | Laravel 13 (PHP 8.3+) | MVC, Eloquent ORM, Form Requests         |
| **Frontend Styling**        | Tailwind CSS v4       | Utility-first CSS, CMD brand palette     |
| **Frontend Interaktivitas** | Alpine.js v3          | Reactive UI tanpa build step             |
| **Database**                | MySQL / MariaDB       | Via Laragon / XAMPP                      |
| **Bundler**                 | Vite                  | Hot Module Replacement untuk development |
| **Testing**                 | Pest (via PHPUnit)    | 18 tests, 115 assertions                 |
| **Tipografi**               | Plus Jakarta Sans     | Via Google Fonts                         |

---

## 👥 Pemisahan Peran (_Role Separation: Maker vs Checker_)

Sistem membedakan hak akses pengguna internal menjadi 2 peran utama berdasarkan prinsip **Maker-Checker** (Four-Eyes Principle) yang umum diterapkan di industri pembiayaan:

| Peran                        | Kode Role   | Fungsi Utama                                    | Hak Akses                                                                                                    |
| :--------------------------- | :---------- | :---------------------------------------------- | :----------------------------------------------------------------------------------------------------------- |
| **Credit Marketing Officer** | `marketing` | Menginput berkas pengajuan nasabah baru         | ✅ Catat pengajuan baru · ✅ Lihat daftar · ❌ Tidak bisa approve/reject                                     |
| **Credit Analyst**           | `analyst`   | Menganalisis risiko kredit & kelayakan angsuran | ✅ Lihat detail & kalkulasi · ✅ Setujui pengajuan · ✅ Tolak pengajuan · ❌ Tidak bisa input pengajuan baru |

**Akun Demo (sudah tersedia setelah menjalankan seeder):**

| Role                      | Email                 | Password   |
| :------------------------ | :-------------------- | :--------- |
| Credit Analyst (Approver) | `analyst@cmd.co.id`   | `password` |
| Marketing Officer (Maker) | `marketing@cmd.co.id` | `password` |

> **Tip untuk Reviewer:** Gunakan tombol _"Akun Pengujian"_ di halaman login untuk mengisi email & password secara otomatis, lalu klik **Masuk ke Sistem**. Atau akses langsung via URL:
>
> - Login sebagai Analyst: `http://localhost:8000/demo-login/analyst`
> - Login sebagai Marketing: `http://localhost:8000/demo-login/marketing`

---

## ✨ Fitur & Pemenuhan Spesifikasi

| No         | Spesifikasi Soal                                               | Status | Implementasi                                                                                             |
| :--------- | :------------------------------------------------------------- | :----: | :------------------------------------------------------------------------------------------------------- |
| **Obj 1**  | UI Sederhana tapi Rapi dengan Tailwind CSS                     |   ✅   | Palet warna resmi CMD (`#E59E15` & `#0B132B`), kartu KPI statistik, responsif penuh smartphone & desktop |
| **Obj 2a** | Field: Nama Lengkap Nasabah                                    |   ✅   | Input teks, validasi `min:2 max:150`, wajib diisi                                                        |
| **Obj 2b** | Field: Tipe Pengajuan (Motor/Mobil/Multiguna)                  |   ✅   | Radio card interaktif, menggunakan `LoanType` enum                                                       |
| **Obj 2c** | Field: Nominal Pengajuan                                       |   ✅   | Input dengan currency mask otomatis (format Rupiah saat mengetik)                                        |
| **Obj 2d** | Field: Tenor (Bulan)                                           |   ✅   | Custom dropdown (1, 3, 6, 9, 12, 18, 24 bulan) dengan animasi                                            |
| **Obj 2e** | Field: Pendapatan Bulanan Nasabah                              |   ✅   | Input dengan currency mask otomatis                                                                      |
| **Obj 2f** | Field: Catatan                                                 |   ✅   | Textarea opsional, max 1.000 karakter                                                                    |
| **Obj 3**  | Tabel Pengajuan dengan semua kolom                             |   ✅   | Nama, Tipe, Nominal, Tenor, Tagihan/Bln, Tanggal, Status, Aksi                                           |
| **Obj 3i** | Tombol Setujui, Tolak, Detail                                  |   ✅   | Icon-only dengan tooltip, hanya tampil untuk role Analyst                                                |
| **Obj 4**  | Detail pengajuan + kalkulasi tagihan/bulan                     |   ✅   | Modal detail dengan breakdown pokok, bunga, DTI ratio, dan tabel amortisasi                              |
| **Obj 5**  | Dialog/popup konfirmasi approve & reject                       |   ✅   | Modal konfirmasi terpisah dengan input alasan penolakan                                                  |
| **Beh 1**  | Income < 1jt → pesan "Nasabah belum dapat mengajukan pinjaman" |   ✅   | Validasi backend (`min:1000000`) + frontend (Alpine.js)                                                  |
| **Beh 2**  | Nominal maksimal 200 juta                                      |   ✅   | Validasi backend (`max:200000000`) + frontend                                                            |
| **Beh 3**  | Tenor maksimal 24 bulan                                        |   ✅   | Validasi backend (`max:24`) + frontend; dropdown dibatasi maksimal 24 bulan                              |
| **Beh 4**  | Maksimal 3 pengajuan per nasabah                               |   ✅   | Validasi backend (custom rule, case-insensitive) + frontend (pengecekan `allLoans`)                      |

---

## 🛡️ Detail Implementasi Aturan Bisnis (Behaviour 1 – 4)

Setiap aturan bisnis diimplementasi pada **dua lapisan** untuk keamanan maksimal:

### Behaviour 1 — Pendapatan Minimum Rp 1.000.000

- **Backend** (`StoreLoanApplicationRequest`): Rule `'monthly_income' => ['required', 'numeric', 'min:1000000']` dengan pesan error persis: _"Nasabah belum dapat mengajukan pinjaman"_
- **Frontend** (`Alpine.js`): Cek `if (this.rawIncome < 1000000)` di `validateAndSubmit()` sebelum form di-submit, tampilkan modal error interaktif

### Behaviour 2 — Nominal Maksimal Rp 200.000.000

- **Backend**: Rule `'loan_amount' => ['max:200000000']`
- **Frontend**: Cek `if (this.rawAmount > 200000000)`, tampilkan modal error

### Behaviour 3 — Tenor Maksimal 24 Bulan

- **Backend**: Rule `'tenor_months' => ['max:24']`
- **Frontend**: Dropdown tenor dibatasi maksimal pilihan 24 bulan; tambahan cek `if (this.formData.tenor_months > 24)`

### Behaviour 4 — Maksimal 3 Pengajuan per Nasabah

- **Backend**: Custom closure rule yang query `LOWER(TRIM(customer_name))` — case-insensitive & trim-safe
- **Frontend**: Filter `allLoans` berdasarkan nama (lowercase) sebelum submit

---

## 🏗️ Arsitektur & Struktur Folder

```
app/
├── Enums/
│   ├── LoanStatus.php          # Enum: pending | approved | rejected (+ label, badge, isPending)
│   └── LoanType.php            # Enum: motor | mobil | multiguna (+ label, badgeClasses)
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # Login, Logout, Demo Login (untuk reviewer)
│   │   └── LoanApplicationController.php # Index, Store, Show, Approve, Reject, CalculatePreview
│   └── Requests/
│       ├── StoreLoanApplicationRequest.php  # Validasi form + Behaviour 1-4
│       └── UpdateLoanStatusRequest.php      # Validasi rejection_reason
├── Models/
│   ├── LoanApplication.php     # Model utama, scopes (search/status/type), accessors, approve/reject
│   └── User.php                # User model dengan role (analyst | marketing), helper methods
└── Services/
    └── LoanCalculationService.php  # Kalkulasi angsuran flat rate 0.9%/bln + generateSchedule()

resources/views/
├── auth/
│   └── login.blade.php          # Halaman login dengan auto-fill demo account
├── components/
│   ├── button.blade.php         # Reusable button (variant: primary/secondary/emerald/rose/ghost)
│   ├── modal.blade.php          # Reusable animated modal (sticky header + scrollable body + sticky footer)
│   ├── toast.blade.php          # Toast notifikasi floating top-right dengan shrinking progress bar
│   ├── stat-card.blade.php      # KPI card statistik dashboard
│   └── badge.blade.php          # Badge status/tipe
├── layouts/
│   └── app.blade.php            # Layout utama (navbar + footer + toast)
└── loans/
    └── index.blade.php          # Halaman utama: tabel + 5 modal + Alpine.js logic (~1090 baris)

database/
├── migrations/
│   └── 2026_09_19_100000_create_loan_applications_table.php
└── seeders/
    ├── DatabaseSeeder.php        # Seed 2 user demo (analyst + marketing)
    └── LoanApplicationSeeder.php # Seed 5 data pengajuan sample (pending/approved/rejected)

tests/Feature/
└── LoanApplicationTest.php      # 18 tests, 115 assertions
```

---

## 🗄️ Struktur Database

### Tabel `users`

| Kolom            | Tipe            | Keterangan                     |
| :--------------- | :-------------- | :----------------------------- |
| `id`             | bigint unsigned | Primary key                    |
| `name`           | varchar(255)    | Nama lengkap pengguna internal |
| `email`          | varchar(255)    | Email login (unique)           |
| `password`       | varchar(255)    | Bcrypt hash                    |
| `role`           | varchar(255)    | `analyst` atau `marketing`     |
| `remember_token` | varchar(100)    | Session remember me            |
| `created_at`     | timestamp       | —                              |
| `updated_at`     | timestamp       | —                              |

### Tabel `loan_applications`

| Kolom                   | Tipe            | Default   | Keterangan                                    |
| :---------------------- | :-------------- | :-------- | :-------------------------------------------- |
| `id`                    | bigint unsigned | auto      | Primary key                                   |
| `customer_name`         | varchar(255)    | —         | Nama nasabah (indexed)                        |
| `loan_type`             | varchar(50)     | —         | `motor` / `mobil` / `multiguna` (indexed)     |
| `loan_amount`           | decimal(15,2)   | —         | Nominal pokok pembiayaan                      |
| `tenor_months`          | unsigned int    | —         | Durasi pinjaman dalam bulan                   |
| `monthly_income`        | decimal(15,2)   | —         | Pendapatan bulanan nasabah                    |
| `monthly_installment`   | decimal(15,2)   | —         | Cicilan/bulan yang dihitung otomatis          |
| `interest_rate_monthly` | decimal(5,4)    | `0.0090`  | Bunga flat per bulan (0.9%)                   |
| `status`                | varchar(50)     | `pending` | `pending` / `approved` / `rejected` (indexed) |
| `notes`                 | text            | null      | Catatan pengajuan (opsional)                  |
| `rejection_reason`      | text            | null      | Alasan penolakan (diisi oleh Analyst)         |
| `actioned_at`           | timestamp       | null      | Waktu approve/reject dilakukan                |
| `actioned_by`           | varchar(255)    | null      | Nama petugas yang melakukan aksi              |
| `created_at`            | timestamp       | —         | Tanggal pengajuan masuk                       |
| `updated_at`            | timestamp       | —         | —                                             |

**Formula Kalkulasi Angsuran (Flat Rate):**

```
Pokok/Bulan     = Nominal Pinjaman ÷ Tenor
Bunga/Bulan     = Nominal Pinjaman × 0.9%
Cicilan/Bulan   = Pokok/Bulan + Bunga/Bulan
Total Bayar     = Cicilan/Bulan × Tenor
```

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### Prasyarat

| Kebutuhan       | Versi Minimum | Catatan                      |
| :-------------- | :------------ | :--------------------------- |
| PHP             | 8.3+          | Telah diuji pada PHP 8.5.8   |
| Composer        | 2.x           | —                            |
| Node.js & NPM   | 20.x+         | Untuk build assets frontend  |
| MySQL / MariaDB | 8.0+          | Via Laragon / XAMPP / Docker |

---

### Langkah Instalasi

#### 1. Clone Repository atau Ekstrak File ZIP

```bash
# Via Git
git clone <url-repository> coding-test-cmd
cd coding-test-cmd

# Atau, jika file ZIP:
# Ekstrak dan masuk ke folder hasil ekstrak
```

#### 2. Salin File Environment

```bash
copy .env.example .env
```

#### 3. Konfigurasi Database pada `.env`

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coding_test_cmd
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan:** Buat database `coding_test_cmd` di MySQL/MariaDB terlebih dahulu jika belum ada.

#### 4. Install Dependensi

```bash
# Install dependensi PHP
composer install

# Install dependensi frontend
npm install
```

#### 5. Generate Application Key

```bash
php artisan key:generate
```

#### 6. Jalankan Migrasi & Seeder

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:

- Membuat semua tabel database dari awal
- Membuat **2 akun pengguna demo** (`analyst@cmd.co.id` & `marketing@cmd.co.id`, password: `password`)
- Membuat **5 data pengajuan kredit** sample dengan status bervariasi (Menunggu, Disetujui, Ditolak)

#### 7. Build Assets Frontend

```bash
# Untuk development (dengan hot reload):
npm run dev

# Untuk production build:
npm run build
```

#### 8. Jalankan Server Aplikasi

```bash
php artisan serve
```

Buka browser di: **`http://localhost:8000`** atau **`http://127.0.0.1:8000`**

> **Rekomendasi:** Jalankan `npm run dev` dan `php artisan serve` secara bersamaan di dua terminal terpisah untuk pengalaman development terbaik.

---

## 🧪 Menjalankan Pengujian Otomatis

```bash
php artisan test
```

### Hasil Test Suite (18 Tests, 115 Assertions):

```
PASS  Tests\Feature\LoanApplicationTest
  ✓  test_unauthenticated_user_is_redirected_to_login
  ✓  test_can_render_loans_dashboard_page
  ✓  test_analyst_user_sees_action_column
  ✓  test_marketing_user_does_not_see_action_column
  ✓  test_can_create_loan_application_with_valid_data
  ✓  test_behaviour_1_income_below_1_million_fails_with_exact_error_message
  ✓  test_behaviour_2_loan_amount_exceeding_200_million_fails
  ✓  test_behaviour_3_tenor_exceeding_24_months_fails
  ✓  test_behaviour_4_customer_cannot_apply_more_than_3_times
  ✓  test_can_approve_pending_loan
  ✓  test_can_reject_pending_loan_with_reason
  ✓  test_cannot_reapprove_already_processed_loan
  ✓  test_credit_analyst_cannot_create_loan_application
  ✓  test_credit_analyst_does_not_see_create_button
  ✓  test_marketing_officer_sees_create_button
  ✓  test_can_get_loan_detail_json_with_schedule
  ✓  test_the_application_returns_a_successful_response
  ✓  test_marketing_user_does_not_see_action_column

  Tests:    18 passed (115 assertions)
  Duration: ~0.8s
```

---

## 🗺️ Ringkasan Alur Aplikasi

```
Pengguna Buka Aplikasi
        │
        ▼
   Halaman Login (/login)
   [Isi email & password]
        │
        ├── Login sebagai Marketing Officer ──▶ Dashboard (melihat tabel)
        │                                            │
        │                                            ▼
        │                                    [Catat Pengajuan Baru] → Form Modal → Simpan → Toast ✅
        │
        └── Login sebagai Credit Analyst ───▶ Dashboard (melihat tabel + kolom Aksi)
                                                     │
                                          ┌──────────┼──────────┐
                                          ▼          ▼          ▼
                                      [Detail]   [Setujui]   [Tolak]
                                          │          │          │
                                     Modal Info  Confirm    Confirm
                                     + Schedule  Modal      Modal + Alasan
                                                    │          │
                                               POST /approve  POST /reject
                                                    │          │
                                               Toast ✅    Toast ✅
```

---

## 👤 Pengembang

- **Posisi yang Dilamar:** Junior / Mid Fullstack Developer — IT Department
- **Target Perusahaan:** PT Capella Multidana (CMD)
- **Tujuan:** Coding Test — IT Department
