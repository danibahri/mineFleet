# MineFleet — Vehicle Booking & Fleet Monitoring System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Livewire-4.x-4E56A6?style=flat-square&logo=livewire&logoColor=white" alt="Livewire">
  <img src="https://img.shields.io/badge/Flux_UI-2.x-10B981?style=flat-square" alt="Flux UI">
  <img src="https://img.shields.io/badge/TailwindCSS-4.x-38BDF8?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP">
</p>

**mineFleet** adalah aplikasi web manajemen armada kendaraan yang dirancang untuk perusahaan tambang. Sistem ini mengintegrasikan pemesanan kendaraan, approval berjenjang, monitoring BBM, jadwal service, dan pelaporan operasional dalam satu platform yang modern dan responsif.

---

## Fitur Utama

| Fitur                              | Deskripsi                                                    |
| ---------------------------------- | ------------------------------------------------------------ |
| **Authentication & Authorization** | Login multi-role dengan redirect otomatis per role           |
| **Vehicle Management**             | CRUD kendaraan operasional lengkap                           |
| **Driver Management**              | Pengelolaan driver dan SIM                                   |
| **Vehicle Booking**                | Pemesanan kendaraan dengan validasi double-booking           |
| **Multi-Level Approval**           | Approval berjenjang (Level 1 → Level 2)                      |
| **Fuel Monitoring**                | Input & riwayat konsumsi BBM per kendaraan                   |
| **Service & Maintenance**          | Jadwal service dengan indikator status otomatis              |
| **Reports & Export**               | Laporan multi-tipe dengan ekspor CSV/Excel                   |
| **Activity Logs**                  | Audit trail seluruh aktivitas sistem                         |
| **User Management**                | CRUD pengguna dengan role assignment                         |
| **Settings**                       | Company profile, approval config, region, kategori kendaraan |

---

## Role & Hak Akses

### Admin

Akses penuh ke seluruh sistem: dashboard, kendaraan, driver, booking, approval, BBM, service, laporan, activity logs, user management, dan settings.

### Approver Level 1

Melakukan validasi awal booking: lihat daftar booking, approve/reject, beri catatan, lihat riwayat approval pribadi.

### Approver Level 2

Memberikan persetujuan final booking yang telah disetujui Level 1: final approve/reject, beri catatan, lihat riwayat.

---

## Teknologi

- **Backend:** Laravel 13, PHP 8.3+
- **Frontend:** Livewire 4, Flux UI 2, TailwindCSS 4, Alpine.js
- **Database:** MySQL / MariaDB
- **Build Tool:** Vite + pnpm

---

## Instalasi

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+ & pnpm
- MySQL / MariaDB

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/your-username/mineFleet.git
cd mineFleet

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
pnpm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_DATABASE=minefleet
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Migrasi & seed database
php artisan migrate --seed

# 7. Build assets
pnpm run build
```

### Development Server

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Vite dev server
pnpm run dev
```

Aplikasi akan berjalan di: **http://localhost:8000**

---

## Demo Credentials

| Role                 | Email                      | Password   |
| -------------------- | -------------------------- | ---------- |
| **Admin**            | `admin@minefleet.test`     | `password` |
| **Approver Level 1** | `approver1@minefleet.test` | `password` |
| **Approver Level 2** | `approver2@minefleet.test` | `password` |

---

## Struktur Proyek

```
mineFleet/
├── app/
│   ├── Http/Middleware/       # RoleMiddleware
│   ├── Livewire/Pages/        # Semua Livewire components
│   ├── Models/                # Eloquent models
│   └── Services/              # ActivityLogger service
├── database/
│   ├── migrations/            # Schema database
│   └── seeders/               # Data awal (roles, users, dll)
├── resources/views/
│   ├── layouts/               # app.blade.php, auth.blade.php
│   ├── pages/                 # Halaman per modul
│   └── partials/              # sidebar, header, head
└── routes/web.php             # Route dengan middleware auth & role
```

---

## Alur Sistem Booking

```
Admin → Buat Booking → Pilih Driver & Approver
    ↓
Approver Level 1 → Review → Approve / Reject
    ↓ (jika disetujui)
Approver Level 2 → Final Review → Approve / Reject
    ↓ (jika disetujui final)
Kendaraan siap digunakan
```

Seluruh aktivitas tercatat otomatis di **Activity Logs**.

---

## Lisensi

Proyek ini dikembangkan sebagai technical test / portofolio.  
&copy; 2026 MineFleet
