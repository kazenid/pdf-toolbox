<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Toolbox | Professional Document Utility</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        // Tailwind Configuration for custom colors if needed
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        [v-cloak] {
            display: none;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dragover {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }

        .dragging {
            opacity: 0.5;
            transform: scale(0.98);
        }

        /* Smooth Scroll for results */
        .scroll-mt-20 {
            scroll-margin-top: 5rem;
        }

        .pdf-thumbnail {
            width: 48px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #f1f5f9;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased h-full font-['Inter']">
    <!-- Sidebar (Desktop) -->
    <aside class="hidden md:flex w-72 bg-white border-r border-slate-200 h-screen fixed left-0 top-0 z-50 p-6 flex-col">
        <div class="flex items-center gap-3 mb-10 px-2">
            <div class="bg-blue-600 p-2.5 rounded-xl shadow-lg shadow-blue-200">
                <i data-lucide="file-text" class="text-white w-6 h-6"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-800">PDF Toolbox</h1>
        </div>

        <nav class="flex-1 space-y-1">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4 px-3">Utilitas PDF</p>

            <a href="javascript:void(0)" onclick="switchTab('compress')" id="compress-tab"
                class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 active-tab">
                <i data-lucide="minimize-2" class="w-5 h-5 transition-colors"></i>
                <span class="font-medium">Kompres PDF</span>
            </a>

            <a href="javascript:void(0)" onclick="switchTab('merge')" id="merge-tab"
                class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                <i data-lucide="layers" class="w-5 h-5 transition-colors"></i>
                <span class="font-medium">Gabungkan PDF</span>
            </a>

            <a href="javascript:void(0)" onclick="switchTab('convert')" id="convert-tab"
                class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                <i data-lucide="image" class="w-5 h-5 transition-colors"></i>
                <span class="font-medium">Konversi ke JPG</span>
            </a>

            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-6 mb-4 px-3">Manajemen</p>

            <a href="javascript:void(0)" onclick="switchTab('split')" id="split-tab"
                class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                <i data-lucide="scissors" class="w-5 h-5 transition-colors"></i>
                <span class="font-medium">Split PDF</span>
            </a>

            <a href="javascript:void(0)" onclick="switchTab('manage')" id="manage-tab"
                class="nav-link group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                <i data-lucide="file-cog" class="w-5 h-5 transition-colors"></i>
                <span class="font-medium">Kelola Halaman</span>
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-100">
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <div class="flex items-center gap-2 mb-2 text-blue-600">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span class="text-xs font-bold uppercase tracking-wider">Aman & Privat</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Privasi Anda prioritas kami. Semua file dihapus otomatis setelah proses selesai.
                </p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="md:ml-72 min-h-screen p-4 md:p-8 lg:p-12 pb-24 md:pb-12">
        <div class="max-w-[1400px] mx-auto">

            <!-- Mobile Header Logo -->
            <div class="md:hidden flex items-center justify-center gap-3 mb-8 pt-4">
                <div class="bg-blue-600 p-2 rounded-xl shadow-lg shadow-blue-200">
                    <i data-lucide="file-text" class="text-white w-6 h-6"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-800">PDF Toolbox</h1>
            </div>

            <!-- Kompres PDF -->
            <div id="compress-content" class="tab-content active">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- CENTER PANE: Upload & Result -->
                    <div class="flex-1 min-w-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-10 h-full">
                            <div class="mb-10 text-center md:text-left">
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold mb-4 uppercase tracking-wider">
                                    <i data-lucide="minimize-2" class="w-3 h-3"></i> Optimized Compression
                                </div>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-3 tracking-tight">Kompres PDF
                                </h2>
                                <p class="text-slate-500 text-base md:text-lg">Kecilkan ukuran file dokumen Anda tanpa
                                    kehilangan kualitas esensial.</p>
                            </div>

                            <div id="compressUploadBox"
                                class="relative group cursor-pointer border-2 border-dashed border-slate-200 rounded-3xl py-12 md:py-16 px-6 md:px-8 text-center transition-all hover:border-blue-400 hover:bg-blue-50/30">
                                <input type="file" id="compressFile" name="pdf" accept=".pdf" class="hidden" required>
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-16 h-16 md:w-20 md:h-20 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                        <i data-lucide="upload-cloud" class="w-8 h-8 md:w-10 md:h-10"></i>
                                    </div>
                                    <h3 class="text-lg md:text-xl font-bold text-slate-800 mb-2">Pilih atau Seret PDF</h3>
                                    <p class="text-slate-400 font-medium mb-4 text-sm md:text-base">Maksimal ukuran file
                                        100MB</p>
                                    <span id="compressFileName"
                                        class="inline-block px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-xs md:text-sm font-bold empty:hidden border border-emerald-100 break-all"></span>
                                </div>
                            </div>

                            <!-- Result Area (Inside Center Pane) -->
                            <div id="compressResult"
                                class="hidden mt-10 p-6 md:p-8 bg-slate-50 rounded-[2rem] border border-slate-100 scroll-mt-20">
                                <div class="flex items-center justify-between mb-8">
                                    <h3 class="text-xl font-bold text-slate-800">Berhasil Dikompres!</h3>
                                    <span id="reductionBadge"
                                        class="bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-full shadow-lg shadow-emerald-100"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 md:gap-6 mb-8">
                                    <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-100 shadow-sm text-center">
                                        <span
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Awal</span>
                                        <span id="origSize" class="text-lg md:text-2xl font-black text-slate-800"></span>
                                    </div>
                                    <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-100 shadow-sm text-center">
                                        <span
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Akhir</span>
                                        <span id="compSize" class="text-lg md:text-2xl font-black text-blue-600"></span>
                                    </div>
                                </div>

                                <a id="downloadLink" href="#" download
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg shadow-emerald-100 transition-all flex items-center justify-center gap-2 text-sm md:text-base">
                                    <i data-lucide="download" class="w-5 h-5"></i>
                                    <span>Download PDF</span>
                                </a>
                            </div>

                            <div id="compressError"
                                class="hidden mt-8 p-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 text-sm font-medium flex items-center gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <span id="compressErrorMsg"></span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDEBAR: Settings -->
                    <div class="w-full lg:w-80 xl:w-96 shrink-0">

                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 lg:sticky lg:top-8">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Pengaturan</h3>
                            
                            <form id="compressForm" class="space-y-6">
                                @csrf
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-bold text-slate-700">Kualitas Kompresi</h4>
                                        <span id="qualityLabel"
                                            class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Seimbang</span>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="relative cursor-pointer block group">
                                            <input type="radio" name="quality" value="extreme" class="hidden peer"
                                                onchange="updateQualityInfo('compress')">
                                            <div
                                                class="flex items-center gap-4 border-2 border-slate-50 rounded-xl p-4 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50/50 hover:border-slate-100">
                                                <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-blue-100 peer-checked:text-blue-600">
                                                    <i data-lucide="zap" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-slate-700 text-sm">Extreme</div>
                                                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Minimal Size</p>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer block group">
                                            <input type="radio" name="quality" value="seimbang" class="hidden peer"
                                                onchange="updateQualityInfo('compress')" checked>
                                            <div
                                                class="flex items-center gap-4 border-2 border-slate-50 rounded-xl p-4 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50/50 hover:border-slate-100">
                                                <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-blue-100 peer-checked:text-blue-600">
                                                    <i data-lucide="scale" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-slate-700 text-sm">Seimbang</div>
                                                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Balanced</p>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="relative cursor-pointer block group">
                                            <input type="radio" name="quality" value="kualitas" class="hidden peer"
                                                onchange="updateQualityInfo('compress')">
                                            <div
                                                class="flex items-center gap-4 border-2 border-slate-50 rounded-xl p-4 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50/50 hover:border-slate-100">
                                                <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-blue-100 peer-checked:text-blue-600">
                                                    <i data-lucide="award" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-slate-700 text-sm">High</div>
                                                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Best Quality</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" id="compressSubmit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-100 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                                    <i data-lucide="cpu" class="w-5 h-5"></i>
                                    <span>Kompres Sekarang</span>
                                </button>
                            </form>

                            <!-- Loading State -->
                            <div id="compressLoading" class="hidden mt-6 text-center">
                                <div class="inline-flex items-center gap-3 text-blue-600 font-bold text-sm">
                                    <div class="w-5 h-5 border-2 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                                    Memproses...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Merge PDF -->
            <div id="merge-content" class="tab-content">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- CENTER PANE: List Area -->
                    <div class="flex-1 min-w-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-10 min-h-[600px] flex flex-col">
                            <div class="mb-10 text-center md:text-left">
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold mb-4 uppercase tracking-wider">
                                    <i data-lucide="combine" class="w-3 h-3"></i> Document Combiner
                                </div>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-3 tracking-tight">Gabungkan PDF
                                </h2>
                                <p class="text-slate-500 text-base md:text-lg">Satukan beberapa dokumen PDF menjadi satu file
                                    secara instan.</p>
                            </div>

                            <!-- Upload Box (Middle) -->
                            <div id="mergeUploadBox"
                                class="relative group cursor-pointer border-2 border-dashed border-slate-200 rounded-3xl py-8 px-6 text-center transition-all hover:border-emerald-400 hover:bg-emerald-50/30 mb-8">
                                <input type="file" id="mergeFiles" name="pdfs[]" accept=".pdf" class="hidden" multiple>
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                        <i data-lucide="plus" class="w-6 h-6"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800 mb-1">Tambah File PDF</h3>
                                    <p class="text-slate-400 text-[10px] font-medium uppercase tracking-widest">Pilih minimal 2 dokumen</p>
                                </div>
                            </div>

                            <!-- File List (Middle) -->
                            <div id="mergeFileListArea" class="hidden flex-1 space-y-4">
                                <div class="flex items-center justify-between px-2">
                                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Urutan Antrian
                                    </h3>
                                    <span
                                        class="text-[9px] text-slate-400 font-bold uppercase tracking-widest flex items-center gap-1">
                                        <i data-lucide="mouse-pointer-2" class="w-3 h-3"></i> Geser untuk Mengatur
                                    </span>
                                </div>
                                <div id="fileListContainer" class="space-y-3">
                                    <!-- JS Injected -->
                                </div>
                            </div>

                            <!-- Result Area (Bottom of Center Pane) -->
                            <div id="mergeResult"
                                class="hidden mt-8 p-8 md:p-10 bg-emerald-50 rounded-[2.5rem] border border-emerald-100 text-center scroll-mt-20">
                                <div
                                    class="w-16 h-16 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm ring-8 ring-emerald-50/50">
                                    <i data-lucide="check-circle-2" class="text-emerald-600 w-10 h-10"></i>
                                </div>
                                <h3 class="text-xl md:text-2xl font-black text-slate-800 mb-2">Selesai!</h3>
                                <p id="mergeSuccessMsg" class="text-slate-500 mb-8 font-medium text-sm md:text-base"></p>
                                <a id="mergeDownloadLink" href="#" download
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-8 rounded-2xl shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2 text-sm md:text-base">
                                    <i data-lucide="download" class="w-5 h-5"></i>
                                    <span>Simpan File Gabungan</span>
                                </a>
                            </div>

                            <div id="mergeError"
                                class="hidden mt-8 p-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 text-sm font-medium flex items-center gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <span id="mergeErrorMsg"></span>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDEBAR: Actions -->
                    <div class="w-full lg:w-80 xl:w-96 shrink-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 lg:sticky lg:top-8">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Aksi</h3>
                            
                            <form id="mergeForm" class="space-y-6">
                                @csrf
                                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 mb-6">
                                    <div class="flex items-center gap-2 mb-2 text-blue-600">
                                        <i data-lucide="info" class="w-4 h-4"></i>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">Informasi</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed">
                                        Pastikan urutan file sudah benar sebelum menggabungkan. Gunakan ikon pegangan di sisi kiri file untuk mengatur ulang urutan dokumen.
                                    </p>
                                </div>

                                <button type="submit" id="mergeSubmit" disabled
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-emerald-100 transition-all active:scale-[0.98] flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i data-lucide="combine" class="w-5 h-5"></i>
                                    <span>Gabungkan Sekarang</span>
                                </button>
                            </form>

                            <div id="mergeLoading" class="hidden mt-6 text-center">
                                <div class="inline-flex items-center gap-3 text-emerald-600 font-bold text-sm">
                                    <div class="w-5 h-5 border-2 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                                    Menggabungkan...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konversi ke JPG -->
            <div id="convert-content" class="tab-content">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- CENTER PANE: Upload -->
                    <div class="flex-1 min-w-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-10 h-full flex flex-col">
                            <div class="mb-10 text-center md:text-left">
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-xs font-bold mb-4 uppercase tracking-wider">
                                    <i data-lucide="image-plus" class="w-3 h-3"></i> High Resolution Export
                                </div>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-3 tracking-tight">Konversi ke JPG
                                </h2>
                                <p class="text-slate-500 text-base md:text-lg">Ubah setiap halaman PDF Anda menjadi gambar JPG
                                    berkualitas tinggi.</p>
                            </div>

                            <div id="convertUploadBox"
                                class="relative group cursor-pointer border-2 border-dashed border-slate-200 rounded-3xl py-12 md:py-16 px-6 text-center transition-all hover:border-purple-400 hover:bg-purple-50/30 flex-1 flex flex-col items-center justify-center min-h-[300px]">
                                <input type="file" id="convertFile" name="pdf" accept=".pdf" class="hidden" required>
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                        <i data-lucide="image" class="w-8 h-8 md:w-10 md:h-10"></i>
                                    </div>
                                    <h3 class="text-lg md:text-xl font-bold text-slate-800 mb-2">Pilih File PDF</h3>
                                    <p class="text-slate-400 font-medium text-sm md:text-base mb-4 uppercase tracking-widest">Satu Gambar Per Halaman</p>
                                    <span id="convertFileName"
                                        class="inline-block px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-xs md:text-sm font-bold empty:hidden border border-purple-100 break-all"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT SIDEBAR: Settings -->
                    <div class="w-full lg:w-80 xl:w-96 shrink-0">

                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 lg:sticky lg:top-8">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Pengaturan</h3>
                            
                            <form id="convertForm" class="space-y-6">
                                @csrf
                                <div class="space-y-4">
                                    <h4 class="text-sm font-bold text-slate-700 block mb-2">Kualitas Galeri</h4>
                                    <div class="space-y-3">
                                        <label class="relative cursor-pointer block group">
                                            <input type="radio" name="quality" value="extreme" class="hidden peer"
                                                onchange="updateQualityInfo('convert')">
                                            <div
                                                class="flex items-center gap-4 border-2 border-slate-50 rounded-xl p-4 transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50/50 hover:border-slate-100">
                                                <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-purple-100 peer-checked:text-purple-600">
                                                    <i data-lucide="file-down" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-slate-700 text-sm">Basic</div>
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1">150 DPI</p>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="relative cursor-pointer block group">
                                            <input type="radio" name="quality" value="seimbang" class="hidden peer"
                                                onchange="updateQualityInfo('convert')" checked>
                                            <div
                                                class="flex items-center gap-4 border-2 border-slate-50 rounded-xl p-4 transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50/50 hover:border-slate-100">
                                                <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-purple-100 peer-checked:text-purple-600">
                                                    <i data-lucide="monitor" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-slate-700 text-sm">Standard</div>
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1">200 DPI</p>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="relative cursor-pointer block group">
                                            <input type="radio" name="quality" value="kualitas" class="hidden peer"
                                                onchange="updateQualityInfo('convert')">
                                            <div
                                                class="flex items-center gap-4 border-2 border-slate-50 rounded-xl p-4 transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50/50 hover:border-slate-100">
                                                <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-purple-100 peer-checked:text-purple-600">
                                                    <i data-lucide="highlighter" class="w-5 h-5"></i>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-slate-700 text-sm">Pro</div>
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1">300 DPI</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" id="convertSubmit"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-purple-100 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                                    <i data-lucide="gallery-thumbnails" class="w-5 h-5"></i>
                                    <span>Mulai Konversi</span>
                                </button>
                            </form>

                            <div id="convertLoading" class="hidden mt-6 text-center">
                                <div class="inline-flex items-center gap-3 text-purple-600 font-bold text-sm">
                                    <div class="w-5 h-5 border-2 border-purple-100 border-t-purple-600 rounded-full animate-spin"></div>
                                    Konversi...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gallery Result (Full Width Below Grid) -->
                <div id="convertResult" class="hidden mt-12 pt-12 border-t border-slate-100 scroll-mt-20">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">Hasil Galeri</h3>
                        <button onclick="downloadAllImages()"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-black transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="download-cloud" class="w-4 h-4"></i> Download ZIP
                        </button>
                    </div>
                    <div id="imagesContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                        <!-- JS Injected -->
                    </div>
                </div>

                <div id="convertError"
                    class="hidden mt-8 p-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 text-sm font-medium flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span id="convertErrorMsg"></span>
                </div>
            </div>

            <!-- Split PDF -->
            <div id="split-content" class="tab-content">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex-1 min-w-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-10 h-full">
                            <div class="mb-10 text-center md:text-left">
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-xs font-bold mb-4 uppercase tracking-wider">
                                    <i data-lucide="scissors" class="w-3 h-3"></i> PDF Splitter
                                </div>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-3 tracking-tight">Split PDF</h2>
                                <p class="text-slate-500 text-base md:text-lg">Pisahkan dokumen PDF menjadi beberapa file terpisah dengan mudah.</p>
                            </div>

                            <div id="splitUploadBox" class="relative group cursor-pointer border-2 border-dashed border-slate-200 rounded-3xl py-12 md:py-16 px-6 text-center transition-all hover:border-amber-400 hover:bg-amber-50/30 flex-1 flex flex-col items-center justify-center min-h-[300px]">
                                <input type="file" id="splitFile" name="pdf" accept=".pdf" class="hidden" required>
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                        <i data-lucide="scissors" class="w-8 h-8 md:w-10 md:h-10"></i>
                                    </div>
                                    <h3 class="text-lg md:text-xl font-bold text-slate-800 mb-2">Pilih File PDF</h3>
                                    <p id="splitFileName" class="text-slate-400 font-medium text-sm md:text-base mb-4 uppercase tracking-widest">Klik atau seret file ke sini</p>
                                </div>
                            </div>

                            <div id="splitResult" class="hidden mt-10 p-6 md:p-8 bg-amber-50 rounded-[2rem] border border-amber-100 scroll-mt-20 text-center">
                                <h3 class="text-xl font-bold text-slate-800 mb-4">PDF Berhasil Dipisahkan!</h3>
                                <a id="splitDownloadLink" href="#" download class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="download" class="w-5 h-5"></i>
                                    <span>Download ZIP Hasil Split</span>
                                </a>
                            </div>

                            <div id="splitError" class="hidden mt-8 p-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 text-sm font-medium flex items-center gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <span id="splitErrorMsg"></span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-80 xl:w-96 shrink-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 lg:sticky lg:top-8">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Pengaturan Split</h3>
                            <form id="splitForm" class="space-y-6">
                                <div class="space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-bold text-slate-700 mb-2 block">Mode Split</span>
                                        <select name="mode" id="splitMode" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-medium focus:border-amber-500 outline-none transition-all">
                                            <option value="range">Berdasarkan Range</option>
                                            <option value="per_page">Setiap Halaman</option>
                                        </select>
                                    </label>

                                    <div id="rangeInputArea" class="space-y-2">
                                        <label class="block">
                                            <span class="text-sm font-bold text-slate-700 mb-2 block">Range Halaman</span>
                                            <input type="text" name="ranges" id="splitRanges" placeholder="Contoh: 1-3, 4, 5-7" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-medium focus:border-amber-500 outline-none transition-all">
                                        </label>
                                        <p class="text-[10px] text-slate-400 font-medium leading-relaxed">Gunakan koma untuk memisahkan bagian, dan tanda hubung untuk range (misal: 1-5, 8, 10-12).</p>
                                    </div>
                                </div>

                                <button type="submit" id="splitSubmit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-amber-100 transition-all flex items-center justify-center gap-3">
                                    <i data-lucide="scissors" class="w-5 h-5"></i>
                                    <span>Mulai Split</span>
                                </button>
                            </form>
                            <div id="splitLoading" class="hidden mt-6 text-center">
                                <div class="inline-flex items-center gap-3 text-amber-600 font-bold text-sm">
                                    <div class="w-5 h-5 border-2 border-amber-100 border-t-amber-600 rounded-full animate-spin"></div>
                                    Memproses Split...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manage Pages -->
            <div id="manage-content" class="tab-content">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex-1 min-w-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 md:p-10 h-full">
                            <div class="mb-10 text-center md:text-left flex flex-col md:flex-row md:items-end justify-between gap-4">
                                <div>
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-xs font-bold mb-4 uppercase tracking-wider">
                                        <i data-lucide="file-cog" class="w-3 h-3"></i> Page Manager
                                    </div>
                                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-3 tracking-tight">Kelola Halaman</h2>
                                    <p class="text-slate-500 text-base md:text-lg">Hapus atau ambil halaman tertentu dari dokumen Anda.</p>
                                </div>
                                <div id="manageStats" class="hidden text-right">
                                    <span id="selectedCountBadge" class="inline-block bg-rose-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg shadow-rose-100">0 Dipilih</span>
                                </div>
                            </div>

                            <div id="manageUploadBox" class="relative group cursor-pointer border-2 border-dashed border-slate-200 rounded-3xl py-12 md:py-16 px-6 text-center transition-all hover:border-rose-400 hover:bg-rose-50/30 flex-1 flex flex-col items-center justify-center min-h-[300px]">
                                <input type="file" id="manageFile" name="pdf" accept=".pdf" class="hidden" required>
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                        <i data-lucide="file-cog" class="w-8 h-8 md:w-10 md:h-10"></i>
                                    </div>
                                    <h3 class="text-lg md:text-xl font-bold text-slate-800 mb-2">Pilih File PDF</h3>
                                    <p class="text-slate-400 font-medium text-sm md:text-base mb-4 uppercase tracking-widest">Preview visual akan muncul setelah upload</p>
                                </div>
                            </div>

                            <!-- Thumbnail Grid -->
                            <div id="manageThumbnails" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-8 pt-8 border-t border-slate-100">
                                <!-- JS Injected -->
                            </div>

                            <div id="manageResult" class="hidden mt-10 p-6 md:p-8 bg-emerald-50 rounded-[2rem] border border-emerald-100 text-center">
                                <h3 class="text-xl font-bold text-slate-800 mb-4">Selesai Berhasil!</h3>
                                <a id="manageDownloadLink" href="#" download class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-8 rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="download" class="w-5 h-5"></i>
                                    <span>Simpan Hasil Perubahan</span>
                                </a>
                            </div>

                            <div id="manageError" class="hidden mt-8 p-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 text-sm font-medium flex items-center gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <span id="manageErrorMsg"></span>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-80 xl:w-96 shrink-0">
                        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 lg:sticky lg:top-8">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-6">Aksi Halaman</h3>
                            <form id="manageForm" class="space-y-6">
                                <div class="space-y-4">
                                    <label class="block">
                                        <span class="text-sm font-bold text-slate-700 mb-2 block">Tindakan</span>
                                        <select name="action" id="manageAction" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 text-sm font-medium focus:border-rose-500 outline-none transition-all">
                                            <option value="extract">Ekstrak (Ambil Halaman)</option>
                                            <option value="delete">Hapus Halaman Terpilih</option>
                                        </select>
                                    </label>
                                    <input type="hidden" name="pages" id="selectedPagesInput">
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                        <p class="text-[10px] text-slate-500 leading-relaxed">Pilih halaman dengan mengklik thumbnail di sebelah kiri atau biarkan kosong untuk memasukkan manual (jika fitur manual tersedia).</p>
                                    </div>
                                </div>

                                <button type="submit" id="manageSubmit" disabled class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-rose-100 transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i data-lucide="play" class="w-5 h-5"></i>
                                    <span>Terapkan Perubahan</span>
                                </button>
                            </form>
                            <div id="manageLoading" class="hidden mt-6 text-center">
                                <div class="inline-flex items-center gap-3 text-rose-600 font-bold text-sm">
                                    <div class="w-5 h-5 border-2 border-rose-100 border-t-rose-600 rounded-full animate-spin"></div>
                                    Memproses Halaman...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Bottom Nav -->
    <nav
        class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-4 py-3 flex justify-around items-center z-50">
        <button onclick="switchTab('compress')"
            class="mobile-tab-btn flex flex-col items-center gap-1 active text-blue-600" id="compress-tab-mobile">
            <i data-lucide="minimize-2" class="w-6 h-6 transition-colors"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Kompres</span>
        </button>
        <button onclick="switchTab('merge')" class="mobile-tab-btn flex flex-col items-center gap-1 text-slate-400"
            id="merge-tab-mobile">
            <i data-lucide="layers" class="w-6 h-6 transition-colors"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Merge</span>
        </button>
        <button onclick="switchTab('convert')" class="mobile-tab-btn flex flex-col items-center gap-1 text-slate-400"
            id="convert-tab-mobile">
            <i data-lucide="image" class="w-6 h-6 transition-colors"></i>
            <span class="text-[9px] font-bold uppercase tracking-wider">JPG</span>
        </button>
        <button onclick="switchTab('split')" class="mobile-tab-btn flex flex-col items-center gap-1 text-slate-400"
            id="split-tab-mobile">
            <i data-lucide="scissors" class="w-6 h-6 transition-colors"></i>
            <span class="text-[9px] font-bold uppercase tracking-wider">Split</span>
        </button>
        <button onclick="switchTab('manage')" class="mobile-tab-btn flex flex-col items-center gap-1 text-slate-400"
            id="manage-tab-mobile">
            <i data-lucide="file-cog" class="w-6 h-6 transition-colors"></i>
            <span class="text-[9px] font-bold uppercase tracking-wider">Kelola</span>
        </button>
    </nav>

    <script>
        lucide.createIcons();
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const APP_URL = '{{ url("/") }}';

        function switchTab(tab) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));

            // Sidebar active state
            document.querySelectorAll('.nav-link').forEach(el => {
                el.classList.remove('bg-blue-50', 'text-blue-600', 'active-tab');
                el.classList.add('text-slate-500', 'hover:bg-slate-50', 'hover:text-slate-900');
            });

            // Mobile nav active state
            document.querySelectorAll('.mobile-tab-btn').forEach(el => {
                el.classList.remove('text-blue-600');
                el.classList.add('text-slate-400');
            });

            // Activate new content
            document.getElementById(tab + '-content').classList.add('active');

            // Activate sidebar link
            const activeLink = document.getElementById(tab + '-tab');
            activeLink.classList.remove('text-slate-500', 'hover:bg-slate-50', 'hover:text-slate-900');
            activeLink.classList.add('bg-blue-50', 'text-blue-600', 'active-tab');

            // Activate mobile link
            const mobileLink = document.getElementById(tab + '-tab-mobile');
            if (mobileLink) {
                mobileLink.classList.remove('text-slate-400');
                mobileLink.classList.add('text-blue-600');
            }

            hideAllElements('compress');
            hideAllElements('merge');
            hideAllElements('convert');
            hideAllElements('split');
            hideAllElements('manage');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function hideAllElements(type) {
            const elements = [type + 'Result', type + 'Loading', type + 'Error', type + 'Thumbnails'];
            elements.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
        }

        // File Handlers
        function setupFileUpload(type) {
            const box = document.getElementById(type + 'UploadBox');
            const input = document.getElementById(type + 'File');
            const display = document.getElementById(type + 'FileName');

            if (!box || !input) return;

            box.addEventListener('click', () => input.click());
            box.addEventListener('dragover', (e) => { e.preventDefault(); box.classList.add('dragover'); });
            box.addEventListener('dragleave', () => box.classList.remove('dragover'));
            box.addEventListener('drop', (e) => {
                e.preventDefault();
                box.classList.remove('dragover');
                input.files = e.dataTransfer.files;
                if (input.files.length > 0) display.innerText = '✅ ' + input.files[0].name;
            });
            input.addEventListener('change', () => {
                if (input.files.length > 0) display.innerText = '✅ ' + input.files[0].name;
            });
        }

        setupFileUpload('compress');
        setupFileUpload('convert');

        function updateQualityInfo(type) {
            const radio = document.querySelector(`#${type}-content input[name="quality"]:checked`);
            if (!radio) return;
            const quality = radio.value;
            const label = document.getElementById('qualityLabel');
            if (label && type === 'compress') {
                const labels = { extreme: 'Extreme', seimbang: 'Seimbang', kualitas: 'High Quality' };
                label.innerText = labels[quality];
            }
        }

        // --- COMPRESS ---
        document.getElementById('compressForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fileInput = document.getElementById('compressFile');
            if (!fileInput.files[0]) return;

            const formData = new FormData();
            formData.append('pdf', fileInput.files[0]);
            formData.append('quality', document.querySelector('#compress-content input[name="quality"]:checked').value);

            hideAllElements('compress');
            document.getElementById('compressLoading').classList.remove('hidden');

            try {
                const response = await axios.post(APP_URL + '/pdf/compress', formData, {
                    headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });

                if (response.data.success) {
                    document.getElementById('origSize').innerText = response.data.originalSize;
                    document.getElementById('compSize').innerText = response.data.compressedSize;
                    document.getElementById('reductionBadge').innerText = '-' + response.data.reduction + '%';

                    const link = document.getElementById('downloadLink');
                    const binary = atob(response.data.file);
                    const array = new Uint8Array(binary.length);
                    for (let i = 0; i < binary.length; i++) array[i] = binary.charCodeAt(i);
                    link.href = URL.createObjectURL(new Blob([array], { type: 'application/pdf' }));
                    link.download = response.data.filename;

                    document.getElementById('compressLoading').classList.add('hidden');
                    const resultArea = document.getElementById('compressResult');
                    resultArea.classList.remove('hidden');
                    setTimeout(() => resultArea.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
                } else { throw new Error(response.data.message); }
            } catch (error) {
                document.getElementById('compressLoading').classList.add('hidden');
                document.getElementById('compressErrorMsg').innerText = error.response?.data?.message || error.message;
                document.getElementById('compressError').classList.remove('hidden');
            }
        });

        // --- MERGE ---
        let mergeFilesArray = [];
        function setupMergeUpload() {
            const box = document.getElementById('mergeUploadBox');
            const input = document.getElementById('mergeFiles');
            if (!box || !input) return;

            box.addEventListener('click', () => input.click());
            input.addEventListener('change', () => { addFilesToMerge(input.files); input.value = ''; });
            box.addEventListener('dragover', (e) => { e.preventDefault(); box.classList.add('dragover'); });
            box.addEventListener('dragleave', () => box.classList.remove('dragover'));
            box.addEventListener('drop', (e) => { e.preventDefault(); box.classList.remove('dragover'); addFilesToMerge(e.dataTransfer.files); });
        }

        async function generatePDFThumbnail(file) {
            try {
                const fileURL = URL.createObjectURL(file);
                const loadingTask = pdfjsLib.getDocument(fileURL);
                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1);

                const scale = 0.5;
                const viewport = page.getViewport({ scale: scale });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({ canvasContext: context, viewport: viewport }).promise;

                const dataURL = canvas.toDataURL('image/jpeg', 0.8);
                URL.revokeObjectURL(fileURL);
                return dataURL;
            } catch (error) {
                console.error('Error generating thumbnail:', error);
                return null;
            }
        }

        async function addFilesToMerge(files) {
            for (let file of files) {
                if (file.type === 'application/pdf') {
                    const id = Math.random().toString(36).substr(2, 9);
                    const thumbnail = await generatePDFThumbnail(file);
                    mergeFilesArray.push({ id, file, thumbnail });
                }
            }
            renderMergeFileList();
        }

        function removeMergeFile(id) {
            mergeFilesArray = mergeFilesArray.filter(f => f.id !== id);
            renderMergeFileList();
        }

        function renderMergeFileList() {
            const container = document.getElementById('fileListContainer');
            const area = document.getElementById('mergeFileListArea');
            const submitBtn = document.getElementById('mergeSubmit');

            if (mergeFilesArray.length === 0) {
                area.classList.add('hidden');
                submitBtn.disabled = true;
                return;
            }

            area.classList.remove('hidden');
            submitBtn.disabled = mergeFilesArray.length < 2;
            container.innerHTML = '';

            mergeFilesArray.forEach((item, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-4 bg-white border border-slate-100 rounded-2xl shadow-sm transition-all duration-200 group relative';
                div.draggable = true;
                div.dataset.id = item.id;

                div.innerHTML = `
                    <div class="flex items-center gap-4 min-w-0 flex-1">
                        <div class="flex items-center justify-center w-8 h-8 bg-slate-50 text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 rounded-lg transition-colors cursor-grab flex-shrink-0">
                            <i data-lucide="grip-vertical" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-shrink-0">
                            ${item.thumbnail ?
                        `<img src="${item.thumbnail}" class="pdf-thumbnail border border-slate-100" alt="Thumbnail">` :
                        `<div class="pdf-thumbnail flex items-center justify-center bg-slate-50 border border-slate-100">
                                    <i data-lucide="file-text" class="w-6 h-6 text-slate-300"></i>
                                 </div>`
                    }
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-800 text-xs md:text-sm truncate pr-2">${item.file.name}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">${(item.file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeMergeFile('${item.id}')" class="w-10 h-10 rounded-xl hover:bg-red-50 text-slate-300 hover:text-red-500 transition-all flex items-center justify-center flex-shrink-0">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                `;
                container.appendChild(div);
                div.addEventListener('dragstart', () => div.classList.add('dragging'));
                div.addEventListener('dragend', () => div.classList.remove('dragging'));
            });

            lucide.createIcons();

            container.addEventListener('dragover', e => {
                e.preventDefault();
                const afterElement = getDragAfterElement(container, e.clientY);
                const dragging = document.querySelector('.dragging');
                if (dragging) {
                    if (afterElement == null) container.appendChild(dragging);
                    else container.insertBefore(dragging, afterElement);
                }
            });

            container.addEventListener('dragend', () => {
                const newOrder = [...container.querySelectorAll('[data-id]')].map(el => el.dataset.id);
                mergeFilesArray = newOrder.map(id => mergeFilesArray.find(f => f.id === id));
                renderMergeFileList();
            }, { once: true });
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('[data-id]:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) return { offset: offset, element: child };
                else return closest;
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        document.getElementById('mergeForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            if (mergeFilesArray.length < 2) return;

            const formData = new FormData();
            mergeFilesArray.forEach(item => formData.append('pdfs[]', item.file));

            hideAllElements('merge');
            document.getElementById('mergeLoading').classList.remove('hidden');

            try {
                const response = await axios.post(APP_URL + '/pdf/merge', formData, {
                    headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                if (response.data.success) {
                    document.getElementById('mergeSuccessMsg').innerText = `Siap! ${response.data.fileCount} dokumen telah digabungkan menjadi satu.`;
                    const link = document.getElementById('mergeDownloadLink');
                    const binary = atob(response.data.file);
                    const array = new Uint8Array(binary.length);
                    for (let i = 0; i < binary.length; i++) array[i] = binary.charCodeAt(i);
                    link.href = URL.createObjectURL(new Blob([array], { type: 'application/pdf' }));
                    link.download = response.data.filename;

                    document.getElementById('mergeLoading').classList.add('hidden');
                    const resultArea = document.getElementById('mergeResult');
                    resultArea.classList.remove('hidden');
                    setTimeout(() => resultArea.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
                } else throw new Error(response.data.message);
            } catch (error) {
                document.getElementById('mergeLoading').classList.add('hidden');
                document.getElementById('mergeErrorMsg').innerText = error.response?.data?.message || error.message;
                document.getElementById('mergeError').classList.remove('hidden');
            }
        });

        setupMergeUpload();

        // --- CONVERT ---
        document.getElementById('convertForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fileInput = document.getElementById('convertFile');
            if (!fileInput.files[0]) return;

            const formData = new FormData();
            formData.append('pdf', fileInput.files[0]);
            formData.append('quality', document.querySelector('#convert-content input[name="quality"]:checked').value);

            hideAllElements('convert');
            document.getElementById('convertLoading').classList.remove('hidden');

            try {
                const response = await axios.post(APP_URL + '/pdf/convert-to-jpg', formData, {
                    headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                if (response.data.success) {
                    const container = document.getElementById('imagesContainer');
                    container.innerHTML = '';
                    response.data.images.forEach((img, index) => {
                        const div = document.createElement('div');
                        div.className = 'group relative aspect-[3/4] bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-purple-100 transition-all cursor-pointer';
                        div.innerHTML = `
                            <img src="data:image/jpeg;base64,${img.data}" alt="Page ${index + 1}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                                <p class="text-white text-[10px] font-black uppercase tracking-widest">Halaman ${index + 1}</p>
                            </div>
                        `;
                        div.onclick = () => downloadImage(img.data, img.filename);
                        container.appendChild(div);
                    });
                    window.convertedImages = response.data.images;
                    document.getElementById('convertLoading').classList.add('hidden');
                    const resultArea = document.getElementById('convertResult');
                    resultArea.classList.remove('hidden');
                    setTimeout(() => resultArea.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
                } else throw new Error(response.data.message);
            } catch (error) {
                document.getElementById('convertLoading').classList.add('hidden');
                document.getElementById('convertErrorMsg').innerText = error.response?.data?.message || error.message;
                document.getElementById('convertError').classList.remove('hidden');
            }
        });

        function downloadImage(base64, filename) {
            const link = document.createElement('a');
            link.href = 'data:image/jpeg;base64,' + base64;
            link.download = filename;
            link.click();
        }

        function downloadAllImages() {
            if (!window.convertedImages) return;
            window.convertedImages.forEach((img, idx) => setTimeout(() => downloadImage(img.data, img.filename), idx * 300));
        }

        setupFileUpload('split');
        setupFileUpload('manage');

        // --- SPLIT ---
        document.getElementById('splitMode').addEventListener('change', (e) => {
            document.getElementById('rangeInputArea').classList.toggle('hidden', e.target.value === 'per_page');
        });

        document.getElementById('splitForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fileInput = document.getElementById('splitFile');
            if (!fileInput.files[0]) return;

            const formData = new FormData();
            formData.append('pdf', fileInput.files[0]);
            formData.append('mode', document.getElementById('splitMode').value);
            formData.append('ranges', document.getElementById('splitRanges').value);

            hideAllElements('split');
            document.getElementById('splitLoading').classList.remove('hidden');

            try {
                const response = await axios.post(APP_URL + '/pdf/split', formData, {
                    headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });

                if (response.data.success) {
                    const link = document.getElementById('splitDownloadLink');
                    const binary = atob(response.data.file);
                    const array = new Uint8Array(binary.length);
                    for (let i = 0; i < binary.length; i++) array[i] = binary.charCodeAt(i);
                    
                    const mimeType = response.data.mimeType || 'application/zip';
                    link.href = URL.createObjectURL(new Blob([array], { type: mimeType }));
                    link.download = response.data.filename;

                    const btnSpan = link.querySelector('span');
                    if (btnSpan) {
                        btnSpan.innerText = mimeType === 'application/pdf' ? 'Download PDF Hasil Split' : 'Download ZIP Hasil Split';
                    }

                    document.getElementById('splitLoading').classList.add('hidden');
                    document.getElementById('splitResult').classList.remove('hidden');
                } else throw new Error(response.data.message);
            } catch (error) {
                document.getElementById('splitLoading').classList.add('hidden');
                document.getElementById('splitErrorMsg').innerText = error.response?.data?.message || error.message;
                document.getElementById('splitError').classList.remove('hidden');
            }
        });

        // --- MANAGE PAGES ---
        let selectedPages = [];
        document.getElementById('manageFile').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            hideAllElements('manage');
            document.getElementById('manageLoading').classList.remove('hidden');
            document.getElementById('manageUploadBox').classList.add('hidden');

            try {
                const fileURL = URL.createObjectURL(file);
                const pdf = await pdfjsLib.getDocument(fileURL).promise;
                const container = document.getElementById('manageThumbnails');
                container.innerHTML = '';
                selectedPages = [];
                updateManageUI();

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const viewport = page.getViewport({ scale: 0.3 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    await page.render({ canvasContext: context, viewport: viewport }).promise;
                    
                    const div = document.createElement('div');
                    div.className = 'page-thumb group relative aspect-[3/4] bg-white border-2 border-slate-100 rounded-xl overflow-hidden cursor-pointer transition-all hover:border-rose-300';
                    div.dataset.page = i;
                    div.innerHTML = `
                        <img src="${canvas.toDataURL()}" class="w-full h-full object-cover">
                        <div class="absolute top-2 right-2 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-md border border-slate-100 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i data-lucide="check" class="w-3 h-3 text-rose-600"></i>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-slate-900/40 py-1 text-center">
                            <span class="text-[10px] font-black text-white">${i}</span>
                        </div>
                        <div class="selection-overlay absolute inset-0 bg-rose-600/10 border-4 border-rose-600 opacity-0 transition-all"></div>
                    `;
                    div.onclick = () => togglePageSelection(i, div);
                    container.appendChild(div);
                }
                
                lucide.createIcons();
                document.getElementById('manageLoading').classList.add('hidden');
                container.classList.remove('hidden');
                document.getElementById('manageStats').classList.remove('hidden');
                document.getElementById('manageSubmit').disabled = false;
            } catch (error) {
                console.error(error);
                document.getElementById('manageLoading').classList.add('hidden');
                document.getElementById('manageUploadBox').classList.remove('hidden');
            }
        });

        function togglePageSelection(page, el) {
            const index = selectedPages.indexOf(page);
            if (index > -1) {
                selectedPages.splice(index, 1);
                el.querySelector('.selection-overlay').classList.add('opacity-0');
            } else {
                selectedPages.push(page);
                el.querySelector('.selection-overlay').classList.remove('opacity-0');
            }
            updateManageUI();
        }

        function updateManageUI() {
            document.getElementById('selectedCountBadge').innerText = `${selectedPages.length} Dipilih`;
            document.getElementById('selectedPagesInput').value = selectedPages.sort((a, b) => a - b).join(',');
        }

        document.getElementById('manageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const fileInput = document.getElementById('manageFile');
            if (!fileInput.files[0] || selectedPages.length === 0) return;

            const formData = new FormData();
            formData.append('pdf', fileInput.files[0]);
            formData.append('pages', document.getElementById('selectedPagesInput').value);
            formData.append('action', document.getElementById('manageAction').value);

            hideAllElements('manage');
            document.getElementById('manageLoading').classList.remove('hidden');

            try {
                const response = await axios.post(APP_URL + '/pdf/manage-pages', formData, {
                    headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });

                if (response.data.success) {
                    const link = document.getElementById('manageDownloadLink');
                    const binary = atob(response.data.file);
                    const array = new Uint8Array(binary.length);
                    for (let i = 0; i < binary.length; i++) array[i] = binary.charCodeAt(i);
                    link.href = URL.createObjectURL(new Blob([array], { type: 'application/pdf' }));
                    link.download = response.data.filename;

                    document.getElementById('manageLoading').classList.add('hidden');
                    document.getElementById('manageResult').classList.remove('hidden');
                } else throw new Error(response.data.message);
            } catch (error) {
                document.getElementById('manageLoading').classList.add('hidden');
                document.getElementById('manageErrorMsg').innerText = error.response?.data?.message || error.message;
                document.getElementById('manageError').classList.remove('hidden');
            }
        });

        // Apply initial active tab style
        const initialActive = document.querySelector('.nav-link.active-tab');
        if (initialActive) {
            initialActive.classList.remove('text-slate-500', 'hover:bg-slate-50', 'hover:text-slate-900');
            initialActive.classList.add('bg-blue-50', 'text-blue-600');
        }
    </script>
</body>

</html>