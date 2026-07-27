# 🔧 Panduan Instalasi System Requirements

File ini berisi panduan lengkap untuk menginstall ImageMagick dan Ghostscript di Windows.

## ⚠️ PENTING: System Requirements

Aplikasi ini memerlukan dua tools penting untuk berfungsi:
1. **ImageMagick** - untuk konversi dan kompresi gambar
2. **Ghostscript** - untuk kompresi PDF berkualitas tinggi

## 📥 Instalasi ImageMagick (Windows)

### Step 1: Download
1. Buka: https://imagemagick.org/script/download.php
2. Cari section "Windows Binaries"
3. Download file: **ImageMagick-7.x.x-Q16-x64-dll.exe** (untuk 64-bit Windows)
   - Atau **ImageMagick-7.x.x-Q16-x86-dll.exe** (untuk 32-bit Windows)

### Step 2: Install
1. Jalankan file installer yang sudah didownload
2. Ikuti wizard installation
3. **PENTING**: Pastikan checkbox berikut dicentang:
   - ✅ Add ImageMagick to system PATH
   - ✅ Install development headers and libraries
4. Selesaikan installation

### Step 3: Verifikasi
Buka Command Prompt/PowerShell dan jalankan:
```bash
magick --version
```

Jika berhasil, akan muncul versi ImageMagick. Jika error, restart komputer dan coba lagi.

## 📥 Instalasi Ghostscript (Windows)

### Step 1: Download
1. Buka: https://www.ghostscript.com/download/gsdnld.html
2. Download **AGPL Ghostscript** untuk Windows
3. Cari file: **gs###w64.exe** (untuk 64-bit) atau **gs###w32.exe** (untuk 32-bit)

### Step 2: Install
1. Jalankan file installer
2. Ikuti wizard installation dengan default settings
3. Catat lokasi instalasi (biasanya: `C:\Program Files\gs\gs10.x.x\bin\`)

### Step 3: Verifikasi
Buka Command Prompt dan jalankan:
```bash
gswin64c.exe -version
```

Jika error, tambahkan path Ghostscript ke System PATH:
1. Buka System Environment Variables
   - Windows 10/11: `Win + X` → System Settings → Advanced System Settings
2. Klik "Environment Variables"
3. Edit variabel "Path" dan tambahkan: `C:\Program Files\gs\gs10.x.x\bin`
4. Klik OK dan restart Command Prompt

## ✅ Tes Aplikasi

Setelah instalasi selesai:

1. Buka PowerShell/Command Prompt di folder project:
```bash
cd c:\xampp\htdocs\pdf
```

2. Jalankan development server:
```bash
php artisan serve --host=127.0.0.1 --port=8001
```

3. Buka browser: http://127.0.0.1:8001

4. Test fitur:
   - Upload file PDF test untuk kompresi
   - Verifikasi bisa download file terkompresi
   - Upload file PDF untuk konversi ke JPG
   - Verifikasi bisa melihat preview JPG

## 🔍 Troubleshooting

### Error: "imagemagick: command not found"
**Solusi**:
1. Cek apakah ImageMagick sudah installed: `magick --version`
2. Jika tidak ketemu, install ulang ImageMagick
3. Pastikan "Add to PATH" dicentang saat install
4. Restart komputer dan coba lagi

### Error: "gswin64c.exe: command not found"
**Solusi**:
1. Check instalasi Ghostscript: `gswin64c.exe -version`
2. Jika error, tambahkan path Ghostscript ke System PATH
3. Atau edit file `PdfController.php` line xx:
```php
private function getGhostscriptPath()
{
    return 'C:\\Program Files\\gs\\gs10.0.0\\bin\\gswin64c.exe';
}
```

### Error: "Failed to convert PDF"
**Solusi**:
1. Pastikan file PDF valid (tidak corrupt)
2. Coba upload file PDF yang berbeda
3. Check permissions folder `storage/app/temp`
4. Jalankan command: `php artisan cache:clear`

### File PDF tidak bisa dikompres
**Solusi**:
1. Pastikan Ghostscript sudah terinstall dengan benar
2. Verify dengan command: `gswin64c.exe -version`
3. Check file size (pastikan < 100MB)
4. Cek disk space yang tersedia

### Konversi PDF ke JPG lambat
**Solusi**:
1. Gunakan kualitas "Cepat" atau "Seimbang"
2. Untuk PDF dengan banyak halaman, split terlebih dahulu
3. Increase PHP timeout di `php.ini`:
```ini
max_execution_time = 300
```

## 🎯 Opsional: Path untuk Development

Untuk kemudahan development, buat shortcut atau tambahkan PATH ke tools:

### Opsi 1: Tambah ke System PATH (Recommended)
Sudah dilakukan saat instalasi dengan checkbox "Add to PATH"

### Opsi 2: Set PATH Manual
```bash
# Windows Command Prompt
set PATH=%PATH%;C:\Program Files\ImageMagick-7.x.x-Q16;C:\Program Files\gs\gs10.x.x\bin
```

### Opsi 3: Specify Full Path di Code
Edit `app/Http/Controllers/PdfController.php` dan set full path di method `getGhostscriptPath()`.

## 📞 Support

Jika masih ada masalah:
1. Verifikasi instalasi dengan command di atas
2. Check versi PHP: `php --version` (minimal 8.1)
3. Check extensions PHP: `php -m | findstr imagemagick`
4. Restart Apache/Development Server

## 🌐 References

- ImageMagick Documentation: https://imagemagick.org/
- Ghostscript Documentation: https://www.ghostscript.com/
- Laravel Documentation: https://laravel.com/docs
- Spatie PDF-to-Image: https://github.com/spatie/pdf-to-image
- Spatie PDF-Optimizer: https://github.com/spatie/pdf-optimizer

---

✨ Setelah semua setup, aplikasi siap untuk digunakan!
