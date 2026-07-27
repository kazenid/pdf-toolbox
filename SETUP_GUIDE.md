# 🔧 Panduan Setup & Instalasi PDF Toolbox

File ini berisi panduan setup aplikasi **PDF Toolbox** menggunakan **Docker (Rekomendasi Instan)** maupun **Instalasi Manual di Windows**.

---

## 🐳 Opsi 1: Menjalankan via Docker (Rekomendasi Instan & 100% Functional)

Menjalankan aplikasi via Docker adalah cara tercepat dan paling stabil karena seluruh system requirements (*Ghostscript*, *ImageMagick*, *Poppler-utils*, serta ekstensi PHP) sudah dikonfigurasi secara otomatis di dalam container Linux.

### Syarat:
- Docker Desktop terinstall di komputer Anda.

### Langkah-langkah:
1. Buka Terminal / PowerShell di folder project:
   ```bash
   cd c:\xampp\htdocs\pdf
   ```

2. Jalankan perintah Docker Compose:
   ```bash
   docker compose up -d --build
   ```

3. Akses aplikasi melalui browser:
   **http://localhost:8001**

### ⚙️ Apa yang Ditangani Otomatis oleh Dockerfile?
- ✅ Meng-install **Ghostscript (`gs`)**, **ImageMagick (`magick`)**, **`poppler-utils`**, dan **PHP `ext-zip`**.
- ✅ Membuka pembatasan keamanan ImageMagick untuk PDF (`/etc/ImageMagick-*/policy.xml` -> `rights="read|write"`).
- ✅ Menaikkan batas upload PHP (`upload_max_filesize = 128M`, `memory_limit = 512M`, `max_execution_time = 300`).
- ✅ Otomatis mem-build asset frontend (Vite & Tailwind CSS) via multi-stage build.

---

## 📥 Opsi 2: Instalasi Manual System Requirements di Windows

Jika Anda ingin menjalankan aplikasi secara lokal menggunakan PHP Native / XAMPP di Windows:

### ⚠️ System Requirements Windows:
1. **PHP 8.2+** (dengan ekstensi `zip`, `gd`, `fileinfo`)
2. **ImageMagick** (untuk konversi PDF ke JPG)
3. **Ghostscript** (untuk kompresi & ekstraksi PDF)
4. **Composer & Node.js**

---

### Step 1: Instalasi ImageMagick (Windows)
1. Buka: https://imagemagick.org/script/download.php
2. Download installer: **ImageMagick-7.x.x-Q16-x64-dll.exe** (64-bit)
3. Jalankan installer dan **WAJIB CENTANG**:
   - ✅ **Add ImageMagick to system PATH**
   - ✅ **Install development headers and libraries**
4. Verifikasi di Command Prompt:
   ```cmd
   magick --version
   ```

---

### Step 2: Instalasi Ghostscript (Windows)
1. Buka: https://www.ghostscript.com/download/gsdnld.html
2. Download **AGPL Ghostscript** (file `gs###w64.exe` untuk 64-bit)
3. Jalankan installer dengan setting default.
4. Verifikasi di Command Prompt:
   ```cmd
   gswin64c.exe -version
   ```
5. Jika `command not found`, tambahkan path instalasi (misal `C:\Program Files\gs\gs10.x.x\bin`) ke **System Environment Variables (PATH)**.

---

### Step 3: Jalankan Aplikasi di Windows
1. Install dependensi composer & npm:
   ```bash
   composer install
   npm install
   npm run build
   ```
2. Salin file `.env` dan generate key:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
3. Jalankan server Laravel:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8001
   ```
4. Buka browser di **http://127.0.0.1:8001**

---

## 🔍 Troubleshooting & Pertanyaan Umum

### Error: `attempt to perform an operation not allowed by the security policy 'PDF'`
- **Penyebab**: Terjadi jika menggunakan Linux tanpa mengubah file `policy.xml` ImageMagick.
- **Solusi**: Jika memakai Docker, `Dockerfile` aplikasi ini sudah otomatis memperbaiki hal ini. Jika di Linux native, edit `/etc/ImageMagick-7/policy.xml` dan ganti `rights="none" pattern="PDF"` menjadi `rights="read|write" pattern="PDF"`.

### Error: `gswin64c.exe` atau `gs` not found
- **Solusi**: Di Windows, pastikan folder `bin` Ghostscript sudah terdaftar di System PATH. Di Linux/Docker, pastikan paket `ghostscript` sudah terinstall (`apt-get install ghostscript` atau `apk add ghostscript`).

### File PDF tidak dapat dikompres atau terpotong
- **Solusi**:
  1. Pastikan ukuran file tidak melebihi 100MB.
  2. Periksa batas timeout PHP (`max_execution_time`) jika memproses dokumen dengan ratusan halaman.

---

✨ Aplikasi siap digunakan baik di environment Windows Lokal maupun Containerized Docker!
