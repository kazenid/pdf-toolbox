# PDF Toolbox - Aplikasi Web Kompres & Konversi PDF

Aplikasi web modern untuk mengompres file PDF dan mengkonversi PDF menjadi JPG dengan interface yang user-friendly menggunakan Laravel dan Tailwind CSS.

## 🎯 Fitur Utama

### 1. **Kompresi PDF** 🗜️
- **Kualitas Tinggi**: Kompresi minimal, hasil terbaik
- **Seimbang**: Keseimbangan sempurna antara ukuran dan kualitas (Rekomendasi)
- **Extreme**: Kompresi maksimal untuk ukuran file paling kecil
- Menampilkan persentase pengurangan ukuran file
- Download file terkompresi langsung

### 2. **Konversi PDF ke JPG** 🖼️
- **Kualitas Tinggi**: Resolusi 300 DPI, hasil terbaik
- **Seimbang**: Resolusi 200 DPI, pilihan terbaik (Rekomendasi)  
- **Cepat**: Resolusi 150 DPI, tercepat
- Konversi setiap halaman PDF menjadi file JPG terpisah
- Preview gambar dalam aplikasi
- Download individual atau batch download semua gambar

## 🛡️ Keamanan

✅ **Semua proses dijalankan di SERVER lokal**
- Tidak ada data terkirim keluar server
- Tidak ada data disimpan secara permanen
- File temporary dihapus setelah processing selesai

## 🚀 Quick Start

```bash
cd c:\xampp\htdocs\pdf
php artisan serve --host=127.0.0.1 --port=8001
```

Buka: **http://127.0.0.1:8001**

## 📋 Requirements

- PHP 8.1+
- Laravel 11
- ImageMagick
- Ghostscript
- Composer, Node.js & NPM

## 📄 License

MIT License - Free to use for personal and commercial purposes
