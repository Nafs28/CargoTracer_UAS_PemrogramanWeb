# Panduan Lengkap Deploy CargoTrace ke VPS (Ubuntu + Nginx + MySQL)

Dokumen ini berisi langkah-langkah lengkap untuk men-deploy proyek **CargoTrace** dari repositori GitHub `Nafs28/CargoTracer_UAS_PemrogramanWeb` ke server VPS Ubuntu (22.04 / 24.04).

---

## Prasyarat
- VPS dengan OS **Ubuntu 22.04** atau **Ubuntu 24.04** dan akses **SSH (`root` atau user dengan `sudo`)**.
- Kode terbaru sudah di-push ke GitHub (`https://github.com/Nafs28/CargoTracer_UAS_PemrogramanWeb.git`).
- *(Opsional)* Nama domain yang sudah diarahkan (A Record) ke IP publik VPS Anda.

---

## Langkah 1: Login SSH & Install Kebutuhan Server

Masuk ke VPS melalui terminal/PowerShell:
```bash
ssh root@IP_VPS_ANDA
```

Update paket sistem dan install **Nginx, MySQL, Git, Unzip, Curl, PHP 8.2/8.3 + ekstensi Laravel**, serta **Composer**:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server git unzip curl \
  php8.2-fpm php8.2-mysql php8.2-sqlite3 php8.2-mbstring \
  php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip

# Install Composer secara global
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```
> **Catatan:** Jika versi Ubuntu Anda menyediakan PHP 8.3 secara default, ganti `php8.2-*` menjadi `php8.3-*`.

---

## Langkah 2: Buat Database & User MySQL

Masuk ke shell MySQL:
```bash
sudo mysql
```

Jalankan perintah SQL berikut untuk membuat database dan user khusus aplikasi:
```sql
CREATE DATABASE cargotrace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cargotrace_user'@'localhost' IDENTIFIED BY 'PasswordKuatAnda123!';
GRANT ALL PRIVILEGES ON cargotrace.* TO 'cargotrace_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```
> Ganti `PasswordKuatAnda123!` dengan password yang kuat dan catat untuk dimasukkan ke file `.env`.

---

## Langkah 3: Clone Project & Setup Laravel

Clone repositori ke folder `/var/www/cargotrace`:
```bash
cd /var/www
sudo git clone https://github.com/Nafs28/CargoTracer_UAS_PemrogramanWeb.git cargotrace
cd /var/www/cargotrace

# Install dependency PHP (tanpa paket dev)
sudo composer install --no-dev --optimize-autoloader

# Salin file .env
sudo cp .env.example .env
sudo nano .env
```

Sesuaikan bagian berikut di dalam `.env` untuk lingkungan **produksi**:
```env
APP_NAME=CargoTrace
APP_ENV=production
APP_DEBUG=false
APP_URL=http://IP_VPS_ATAU_DOMAIN_ANDA

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cargotrace
DB_USERNAME=cargotrace_user
DB_PASSWORD=PasswordKuatAnda123!
```
Simpan file di `nano` dengan menekan `Ctrl + O`, `Enter`, lalu keluar dengan `Ctrl + X`.

Selanjutnya, generate key aplikasi, jalankan migrasi & seeder, optimasi cache, dan atur kepemilikan folder:
```bash
sudo php artisan key:generate
sudo php artisan migrate --force --seed
sudo php artisan config:cache
sudo php artisan route:cache

sudo chown -R www-data:www-data /var/www/cargotrace
sudo chmod -R 775 /var/www/cargotrace/storage /var/www/cargotrace/bootstrap/cache
```

---

## Langkah 4: Konfigurasi Virtual Host Nginx

Buat file konfigurasi Nginx untuk CargoTrace:
```bash
sudo nano /etc/nginx/sites-available/cargotrace
```

Isi dengan konfigurasi berikut:
```nginx
server {
    listen 80;
    server_name IP_VPS_ATAU_DOMAIN_ANDA;
    root /var/www/cargotrace/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
> **Catatan:** Jika memakai PHP 8.3, ubah `php8.2-fpm.sock` menjadi `php8.3-fpm.sock`.

Aktifkan konfigurasi, uji sintaks Nginx, dan restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/cargotrace /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

---

## Langkah 5: (Opsional) Pasang SSL HTTPS dengan Certbot

Jika Anda menggunakan nama domain (bukan sekadar IP), pasang sertifikat SSL gratis dari Let's Encrypt:
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d domainanda.com
```
Setelah HTTPS aktif, ubah `APP_URL=https://domainanda.com` di `/var/www/cargotrace/.env`, lalu jalankan `sudo php artisan config:cache`.

---

## Langkah 6: Verifikasi Aplikasi Live

1. Buka `http://IP_VPS_ATAU_DOMAIN_ANDA` dan `http://IP_VPS_ATAU_DOMAIN_ANDA/app` di browser.
2. Uji login dengan akun demo (password: `password`):
   - **Admin:** `admin@cargotrace.com`
   - **Operator:** `operator@cargotrace.com`
   - **Customer:** `customer@cargotrace.com`
3. Pastikan `APP_DEBUG=false` di `.env` agar pesan error tidak membocorkan *stack trace* internal.
4. Cantumkan URL live aplikasi ke dalam `README.md` sebelum pengumpulan UAS.

---

## Langkah 7: Cara Update Aplikasi Jika Ada Perubahan di GitHub

Jika suatu saat Anda melakukan `git push` pembaruan baru dari komputer lokal ke GitHub, cukup jalankan perintah berikut di VPS untuk memperbarui server:
```bash
cd /var/www/cargotrace
sudo git pull origin main
sudo composer install --no-dev --optimize-autoloader
sudo php artisan migrate --force
sudo php artisan config:cache
sudo php artisan route:cache
sudo chown -R www-data:www-data /var/www/cargotrace/storage /var/www/cargotrace/bootstrap/cache
```
