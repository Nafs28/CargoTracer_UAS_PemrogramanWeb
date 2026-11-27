# Panduan Lengkap Deploy CargoTrace ke Render (Docker)

Dokumen ini berisi langkah-langkah lengkap untuk men-deploy proyek **CargoTrace** ke **Render** secara gratis menggunakan **Docker**.

File konfigurasi yang dibutuhkan sudah disiapkan di proyek ini:
- `Dockerfile` — Image PHP 8.2 + Apache + ekstensi Laravel + Composer
- `.dockerignore` — Mengabaikan file lokal seperti `vendor/`, `.env`, dan `*.sqlite`
- `docker/start.sh` — Skrip startup otomatis (`migrate --force --seed`, cache config/route, dan menjalankan Apache)
- `render.yaml` — Konfigurasi otomatis untuk **Render Blueprint**

---

## Langkah 1: Pastikan Kode Terbaru Sudah Di-Push ke GitHub

Sebelum membuka Render, pastikan `Dockerfile`, `docker/start.sh`, `render.yaml`, dan `.dockerignore` sudah ada di repositori GitHub `Nafs28/CargoTracer_UAS_PemrogramanWeb`:
```bash
git add .
git commit -m "chore: add Docker and Render deployment configuration"
git push origin main
```

---

## Langkah 2: Buat Akun / Login ke Render

1. Buka [https://dashboard.render.com](https://dashboard.render.com).
2. Pilih **Sign in with GitHub** agar Render langsung terhubung dengan repositori GitHub Anda.
3. Berikan izin akses Render ke repositori `CargoTracer_UAS_PemrogramanWeb`.

---

## Langkah 3: Deploy Menggunakan Render Blueprint (Cara Tercepat)

1. Di dashboard Render, klik tombol **New +** di pojok kanan atas, lalu pilih **Blueprint**.
2. Pilih repositori **Nafs28/CargoTracer_UAS_PemrogramanWeb** dan branch **`main`**.
3. Render akan otomatis membaca file `render.yaml` yang sudah ada di repositori.
4. Klik **Apply** / **Deploy Blueprint**.
5. Tunggu proses *build* Docker selesai (sekitar **3–5 menit** untuk build pertama).

> **Alternatif Manual (Web Service):** Jika tidak memakai Blueprint, pilih **New + → Web Service**, pilih repo `CargoTracer_UAS_PemrogramanWeb`, atur **Language: Docker**, **Instance Type: Free**, lalu tambahkan Environment Variables secara manual seperti di Langkah 4.

---

## Langkah 4: Cek & Sesuaikan Environment Variables

Setelah layanan selesai dibuat di Render, buka menu **Environment** pada service `cargotrace-uas` dan pastikan variabel berikut sudah terisi:

| Key | Value | Keterangan |
| :--- | :--- | :--- |
| `APP_NAME` | `CargoTrace` | Nama aplikasi |
| `APP_ENV` | `production` | Mode produksi |
| `APP_DEBUG` | `false` | Wajib `false` agar error tidak membocorkan *stack trace* |
| `APP_KEY` | *(otomatis dari Render)* | Key enkripsi Laravel (`base64:...`) |
| `APP_URL` | `https://cargotrace-uas.onrender.com` | Sesuaikan dengan URL publik Render Anda |
| `DB_CONNECTION` | `sqlite` | Default siap pakai tanpa setup database terpisah |

Jika Anda mengubah `APP_URL`, klik **Save Changes** — Render akan otomatis me-restart service.

---

## Langkah 5: Verifikasi Aplikasi Live

1. Klik URL publik dari Render di bagian atas halaman service (misal: `https://cargotrace-uas.onrender.com`).
2. Pastikan halaman utama (`/`) dan halaman workspace (`/app`) tampil dengan benar.
3. Uji login dengan akun demo (kata sandi: `password`):
   - **Admin:** `admin@cargotrace.com`
   - **Operator:** `operator@cargotrace.com`
   - **Customer:** `customer@cargotrace.com`
4. Uji fitur utama: Dasbor & grafik, Direktori Kargo, Peta GIS, Berita Pasar Global, dan API.

---

## Catatan Penting Render Free Tier

- **Cold Start (Sleep):** Pada paket gratis (*Free*), aplikasi akan *sleep* jika tidak diakses selama 15 menit. Saat dibuka lagi, container membutuhkan waktu sekitar **30–50 detik** untuk menyala kembali.
- **Database SQLite di Free Tier:** Filesystem container Render Free bersifat sementara (*ephemeral*), sehingga data baru yang Anda input akan kembali ke data awal (*seed*) setiap kali container restart atau redeploy. Karena `docker/start.sh` menjalankan `php artisan migrate --force --seed` otomatis, data demo UAS selalu siap digunakan.
- **Jika Ingin Data Permanen:** Buat **PostgreSQL** gratis di Render (**New + → PostgreSQL**), lalu ubah `DB_CONNECTION=pgsql` dan isi `DB_URL` (Internal Database URL) di tab **Environment**.
- **Auto-Deploy:** Setiap kali Anda melakukan `git push origin main` dari komputer lokal, Render akan otomatis membangun ulang dan memperbarui website live Anda.
