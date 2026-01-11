# 📚 Backend Toko Buku API

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Sistem API Backend untuk manajemen katalog buku yang dirancang untuk bekerja secara *decoupled* dengan frontend (seperti Next.js atau Vue.js). Dibangun menggunakan **Laravel 11**, API ini menyediakan fitur pengelolaan data buku yang komprehensif, mulai dari pencarian hingga sistem penyimpanan gambar.

---

## 🚀 Fitur Utama

* **RESTful API:** Arsitektur endpoint yang terstandarisasi untuk komunikasi data JSON.
* **Pencarian & Filter:** Fitur pencarian buku berdasarkan judul/penulis dan filter dinamis berdasarkan kategori.
* **Sistem Kategori Dinamis:** Endpoint otomatis yang mengambil daftar kategori unik langsung dari database.
* **Manajemen Gambar:** Integrasi dengan Laravel Storage untuk upload, update, dan penghapusan cover buku secara otomatis.
* **Soft Deletes:** Keamanan data menggunakan fitur `deleted_at`, sehingga data yang dihapus dapat dipulihkan jika diperlukan.
* **Validasi Data:** Validasi input yang ketat untuk memastikan integritas data (harga, tahun terbit, tipe file gambar, dll).

---

## 🛠️ Teknologi yang Digunakan

* **Framework:** [Laravel 11](https://laravel.com/)
* **Bahasa Pemrograman:** PHP 8.2+
* **Database:** MySQL / MariaDB
* **Tooling:** Composer, Artisan CLI
* **Storage:** Laravel Local Storage (Public Disk)

---

## 📋 Prasyarat Instalasi

Pastikan perangkat Anda sudah terpasang:
* PHP >= 8.2
* Composer
* MySQL Server
* Web Server (seperti Apache/Nginx melalui XAMPP atau Laragon)

---

## 📦 Instalasi & Konfigurasi

1.  **Clone Repository:**
    ```bash
    git clone [https://github.com/RfndUsdan/Backend-toko-buku.git](https://github.com/RfndUsdan/Backend-toko-buku.git)
    cd Backend-toko-buku
    ```

2.  **Install Dependensi:**
    ```bash
    composer install
    ```

3.  **Pengaturan Environment:**
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda.
    ```bash
    cp .env.example .env
    ```

4.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi Database:**
    ```bash
    php artisan migrate
    ```

6.  **Membuat Symbolic Link Storage:**
    Agar gambar yang diupload dapat diakses secara publik (URL).
    ```bash
    php artisan storage:link
    ```

7.  **Jalankan Server:**
    ```bash
    php artisan serve
    ```
    API akan berjalan di: `http://127.0.0.1:8000/api`

---

## 📁 Susunan Proyek (Backend)

[Image of a typical Laravel directory structure focusing on App/Http/Controllers, App/Models, and Routes]

```text
app/
 ├── Http/Controllers/Api/
 │    └── BookController.php  # Logika utama (CRUD, Search, Kategori)
 ├── Models/
 │    └── Book.php           # Definisi model, Fillable, dan SoftDeletes
database/
 └── migrations/             # Definisi skema tabel MySQL
routes/
 └── api.php                 # Daftar endpoint API toko buku
storage/app/public/books/    # Folder penyimpanan file cover buku
