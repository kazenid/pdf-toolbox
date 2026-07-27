# PDF Toolbox - Aplikasi Web Kompres & Konversi PDF

Aplikasi web modern untuk mengompres, mengonversi PDF ke JPG, mengabungkan (*merge*), memotong (*split*), serta mengelola halaman PDF dengan interface yang user-friendly berbasis Laravel dan Tailwind CSS.

## 🎯 Fitur Utama

### 1. **Kompresi PDF** 🗜️
- **Kualitas Tinggi**: Kompresi minimal, hasil visual terbaik (300 DPI)
- **Seimbang**: Keseimbangan sempurna antara ukuran dan kualitas (Rekomendasi - 200 DPI)
- **Extreme**: Kompresi maksimal untuk ukuran file paling kecil (150 DPI)
- Menampilkan persentase penghematan ukuran file & download langsung.

### 2. **Konversi PDF ke JPG** 🖼️
- Konversi setiap halaman PDF menjadi file gambar JPG beresolusi tinggi.
- Mengatasi transparansi background agar tidak menjadi hitam.
- Preview gambar interaktif dalam aplikasi & mendukung batch download.

### 3. **Penggabungan PDF (Merge)** 🔗
- Menggabungkan beberapa file PDF menjadi satu dokumen utuh.

### 4. **Pemotongan PDF (Split)** ✂️
- Memotong PDF per halaman terpisah atau berdasarkan rentang halaman (*range*).
- Hasil otomatis dikemas dalam file ZIP.

### 5. **Pengelolaan Halaman (Extract / Delete)** 📄
- Mengekstrak halaman spesifik atau menghapus halaman yang tidak diinginkan dari PDF.

---

## 🐳 Quick Start dengan Docker (Rekomendasi 100% Functional)

Aplikasi ini sudah **100% mendukung Docker** dengan penanganan dependensi otomatis (*Ghostscript*, *ImageMagick*, *Poppler-utils*, serta penyesuaian *security policy* PDF).

```bash
# 1. Clone / Masuk ke direktori project
cd pdf

# 2. Jalankan container dengan Docker Compose
docker compose up -d --build
```

Buka browser: **http://localhost:8001**

---

## 🚀 Quick Start Lokal (Windows / XAMPP)

```bash
# 1. Jalankan development server
cd c:\xampp\htdocs\pdf
php artisan serve --host=127.0.0.1 --port=8001
```

Buka browser: **http://127.0.0.1:8001**

> ⚠️ *Catatan Pengembang Windows*: Pastikan **ImageMagick** dan **Ghostscript** sudah terinstall dan ditambahkan ke System PATH di Windows Anda. Lihat [SETUP_GUIDE.md](SETUP_GUIDE.md) untuk panduan instalasi lengkap.

---

## 🛡️ Keamanan & Kinerja

✅ **Proses Lokal Server Only**: Tidak ada data yang dikirim ke server pihak ketiga.  
✅ **Pembersihan Otomatis**: File temporary langsung dihapus setelah pemrosesan selesai.  
✅ **Multi-OS Support**: Kompatibel dengan Windows (Local Dev) dan Linux (Docker/Production Container).

---

## 📄 License

MIT License - Free to use for personal and commercial purposes
