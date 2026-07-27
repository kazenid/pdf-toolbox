<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use ZipArchive;

class PdfController extends Controller
{
    public function index()
    {
        return view('pdf.index');
    }

    public function compress(Request $request)
    {
        try {
            $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:102400', // Max 100MB
                'quality' => 'required|in:kualitas,seimbang,extreme'
            ]);

            $file = $request->file('pdf');
            $quality = $request->input('quality');
            
            // Store original file temporarily
            $originalPath = storage_path('app/temp/' . uniqid() . '_original.pdf');
            $compressedPath = storage_path('app/temp/' . uniqid() . '_compressed.pdf');
            
            // Create temp directory if not exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Save uploaded file
            $file->move(dirname($originalPath), basename($originalPath));

            // Get file size before compression
            $originalSize = filesize($originalPath);

            // Optimize based on quality
            $this->optimizePdf($originalPath, $compressedPath, $quality);

            // Get file size after compression
            $compressedSize = filesize($compressedPath);
            $reduction = round(((($originalSize - $compressedSize) / $originalSize) * 100), 2);

            // Read compressed file
            $fileContent = file_get_contents($compressedPath);
            
            // Clean up
            unlink($originalPath);
            unlink($compressedPath);

            return response()->json([
                'success' => true,
                'message' => "Kompres berhasil! Ukuran berkurang {$reduction}%",
                'file' => base64_encode($fileContent),
                'originalSize' => $this->formatBytes($originalSize),
                'compressedSize' => $this->formatBytes($compressedSize),
                'reduction' => $reduction,
                'filename' => 'compressed_' . $file->getClientOriginalName()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function convertToJpg(Request $request)
    {
        try {
            $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:102400', // Max 100MB
                'quality' => 'required|in:kualitas,seimbang,extreme'
            ]);

            $file = $request->file('pdf');
            $quality = $request->input('quality');
            
            // Store original file temporarily
            $pdfPath = storage_path('app/temp/' . uniqid() . '.pdf');
            $outputPath = storage_path('app/temp/' . uniqid());
            
            // Create temp directory if not exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Save uploaded file
            $file->move(dirname($pdfPath), basename($pdfPath));

            // Convert PDF to images
            $images = $this->convertPdfToImages($pdfPath, $outputPath, $quality);

            if (empty($images)) {
                throw new Exception('Konversi gagal');
            }

            // Prepare response with images
            $imageData = [];
            foreach ($images as $imagePath) {
                $imageData[] = [
                    'filename' => basename($imagePath),
                    'data' => base64_encode(file_get_contents($imagePath))
                ];
            }

            // Clean up
            unlink($pdfPath);
            foreach ($images as $imagePath) {
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            if (is_dir($outputPath)) {
                rmdir($outputPath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Konversi berhasil! ' . count($images) . ' halaman dikonversi',
                'images' => $imageData,
                'pageCount' => count($images)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function merge(Request $request)
    {
        try {
            $request->validate([
                'pdfs' => 'required|array|min:2',
                'pdfs.*' => 'required|file|mimes:pdf|max:102400'
            ]);

            $files = $request->file('pdfs');
            $tempPaths = [];
            $mergedPath = storage_path('app/temp/' . uniqid() . '_merged.pdf');

            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            foreach ($files as $file) {
                $tempPath = storage_path('app/temp/' . uniqid() . '.pdf');
                $file->move(dirname($tempPath), basename($tempPath));
                $tempPaths[] = $tempPath;
            }

            $ghostscriptPath = $this->getGhostscriptPath();
            
            // Ghostscript command to merge PDFs
            $inputFiles = implode(' ', array_map(fn($p) => "\"$p\"", $tempPaths));
            $cmd = "\"{$ghostscriptPath}\" -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=\"{$mergedPath}\" {$inputFiles}";

            \Log::info("Merge Command: {$cmd}");
            exec($cmd, $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($mergedPath)) {
                throw new Exception('Gagal menggabungkan PDF. Pastikan file valid.');
            }

            $fileContent = file_get_contents($mergedPath);
            
            // Cleanup
            foreach ($tempPaths as $path) {
                if (file_exists($path)) unlink($path);
            }
            if (file_exists($mergedPath)) unlink($mergedPath);

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil digabungkan!',
                'file' => base64_encode($fileContent),
                'filename' => 'merged_toolbox_' . date('Ymd_His') . '.pdf',
                'fileCount' => count($tempPaths)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function split(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        try {
            $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:102400',
                'mode' => 'required|in:range,per_page',
                'ranges' => 'required_if:mode,range'
            ]);

            $file = $request->file('pdf');
            $mode = $request->input('mode');
            $rangesInput = $request->input('ranges');

            $pdfPath = storage_path('app/temp/' . uniqid() . '.pdf');
            $pdfPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $pdfPath);
            $file->move(dirname($pdfPath), basename($pdfPath));

            $pageCount = $this->getPdfPageCount($pdfPath);
            \Log::info("Starting split for file with {$pageCount} pages, mode: {$mode}");

            $outputFiles = [];
            $tempDir = storage_path('app/temp/' . uniqid());
            $tempDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $tempDir);
            
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            if ($mode === 'per_page') {
                for ($i = 1; $i <= $pageCount; $i++) {
                    $outputPath = $tempDir . DIRECTORY_SEPARATOR . "page_{$i}.pdf";
                    $this->extractPdfPages($pdfPath, $outputPath, $i, $i);
                    if (file_exists($outputPath)) {
                        $outputFiles[] = $outputPath;
                    }
                }
            } else {
                $ranges = explode(',', $rangesInput);
                foreach ($ranges as $index => $range) {
                    $range = trim($range);
                    if (empty($range)) continue;
                    
                    if (strpos($range, '-') !== false) {
                        $parts = explode('-', $range);
                        $start = (int)trim($parts[0]);
                        $end = (int)trim($parts[1] ?? $parts[0]);
                    } else {
                        $start = $end = (int)$range;
                    }

                    if ($start > 0 && $end >= $start && $start <= $pageCount) {
                        $end = min($end, $pageCount);
                        $outputPath = $tempDir . DIRECTORY_SEPARATOR . "part_" . ($index + 1) . ".pdf";
                        $this->extractPdfPages($pdfPath, $outputPath, $start, $end);
                        if (file_exists($outputPath)) {
                            $outputFiles[] = $outputPath;
                        }
                    }
                }
            }

            if (empty($outputFiles)) {
                throw new Exception('Gagal memproses bagian PDF. Pastikan range halaman benar.');
            }

            $isSingleFile = count($outputFiles) === 1;

            if ($isSingleFile) {
                // Return single PDF directly
                $finalFileContent = file_get_contents($outputFiles[0]);
                $finalFilename = 'split_' . ($mode === 'range' ? 'part1_' : 'page1_') . $file->getClientOriginalName();
                $mimeType = 'application/pdf';
            } else {
                // Create ZIP
                $zipPath = storage_path('app/temp/' . uniqid() . '.zip');
                $zipPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $zipPath);
                
                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                    foreach ($outputFiles as $f) {
                        $zip->addFile($f, basename($f));
                    }
                    $zip->close();
                } else {
                    throw new Exception('Gagal membuat file ZIP.');
                }

                if (!file_exists($zipPath)) {
                    throw new Exception('File ZIP tidak ditemukan setelah pembuatan.');
                }

                $finalFileContent = file_get_contents($zipPath);
                $finalFilename = 'split_' . str_replace('.pdf', '', $file->getClientOriginalName()) . '.zip';
                $mimeType = 'application/zip';
                unlink($zipPath);
            }

            // Cleanup temp files
            if (file_exists($pdfPath)) unlink($pdfPath);
            foreach ($outputFiles as $f) {
                if (file_exists($f)) unlink($f);
            }
            if (is_dir($tempDir)) rmdir($tempDir);

            \Log::info("Split success, returning " . ($isSingleFile ? "PDF" : "ZIP") . " content");

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil di-split!',
                'file' => base64_encode($finalFileContent),
                'filename' => $finalFilename,
                'mimeType' => $mimeType
            ]);

        } catch (Exception $e) {
            \Log::error("Split PDF Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function managePages(Request $request)
    {
        try {
            $request->validate([
                'pdf' => 'required|file|mimes:pdf|max:102400',
                'pages' => 'required|string',
                'action' => 'required|in:extract,delete'
            ]);

            $file = $request->file('pdf');
            $selectedPagesInput = $request->input('pages');
            $action = $request->input('action');

            $pdfPath = storage_path('app/temp/' . uniqid() . '.pdf');
            $file->move(dirname($pdfPath), basename($pdfPath));

            $pageCount = $this->getPdfPageCount($pdfPath);
            
            if ($pageCount <= 0) {
                unlink($pdfPath);
                throw new Exception('Gagal membaca informasi halaman PDF. Pastikan file tidak terenkripsi.');
            }

            $selectedPages = $this->parsePageRanges($selectedPagesInput, $pageCount);

            if ($action === 'delete') {
                $pagesToKeep = [];
                for ($i = 1; $i <= $pageCount; $i++) {
                    if (!in_array($i, $selectedPages)) {
                        $pagesToKeep[] = $i;
                    }
                }
                $finalPages = $pagesToKeep;
            } else {
                $finalPages = $selectedPages;
            }

            if (empty($finalPages)) {
                throw new Exception('Hasil akhir tidak memiliki halaman');
            }

            $outputPath = storage_path('app/temp/' . uniqid() . '.pdf');
            
            // Re-assemble PDF from selected pages
            $ghostscriptPath = $this->getGhostscriptPath();
            $pageList = implode(',', $finalPages);
            
            // GS 9.5+ supports -sPageList
            $cmd = "\"{$ghostscriptPath}\" -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sPageList={$pageList} -sOutputFile=\"{$outputPath}\" \"{$pdfPath}\"";
            
            exec($cmd, $output, $returnVar);

            if ($returnVar !== 0) {
                // Fallback for older GS: combine individual pages
                $tempFiles = [];
                foreach ($finalPages as $p) {
                    $tempP = storage_path('app/temp/' . uniqid() . '_p' . $p . '.pdf');
                    $this->extractPdfPages($pdfPath, $tempP, $p, $p);
                    $tempFiles[] = $tempP;
                }
                $inputs = implode(' ', array_map(fn($f) => "\"$f\"", $tempFiles));
                $cmdFallback = "\"{$ghostscriptPath}\" -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=\"{$outputPath}\" {$inputs}";
                exec($cmdFallback);
                foreach ($tempFiles as $f) unlink($f);
            }

            $content = file_get_contents($outputPath);
            unlink($pdfPath);
            if (file_exists($outputPath)) unlink($outputPath);

            return response()->json([
                'success' => true,
                'message' => 'Halaman berhasil dikelola!',
                'file' => base64_encode($content),
                'filename' => ($action === 'extract' ? 'extracted_' : 'edited_') . $file->getClientOriginalName()
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function optimizePdf($inputPath, $outputPath, $quality)
    {
        $ghostscriptPath = $this->getGhostscriptPath();

        $settings = match($quality) {
            'extreme' => [
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                '-dPDFSETTINGS=/screen',
                '-dNOPAUSE',
                '-dQUIET',
                '-dBATCH',
                '-r150',
            ],
            'seimbang' => [
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                '-dPDFSETTINGS=/ebook',
                '-dNOPAUSE',
                '-dQUIET',
                '-dBATCH',
                '-r200',
            ],
            'kualitas' => [
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                '-dPDFSETTINGS=/printer',
                '-dNOPAUSE',
                '-dQUIET',
                '-dBATCH',
                '-r300',
            ],
        };

        $cmd = implode(' ', array_merge(
            ["\"{$ghostscriptPath}\""],
            $settings,
            ["-sOutputFile=\"{$outputPath}\"", "\"{$inputPath}\""]
        ));

        \Log::info("Ghostscript Path: {$ghostscriptPath}");
        \Log::info("Compression Command: {$cmd}");

        exec($cmd, $output, $returnVar);

        \Log::info("Ghostscript Return Code: {$returnVar}", ['output' => $output]);

        if ($returnVar !== 0) {
            \Log::error("Ghostscript compression failed, using fallback copy", ['return_code' => $returnVar]);
            // Fallback to direct copy if optimization fails
            copy($inputPath, $outputPath);
        } else {
            \Log::info("Ghostscript compression succeeded");
        }
    }

    private function convertPdfToImages($pdfPath, $outputPath, $quality)
    {
        $dpi = match($quality) {
            'extreme' => 150,
            'seimbang' => 200,
            'kualitas' => 300,
        };

        $quality_val = match($quality) {
            'extreme' => 60,
            'seimbang' => 80,
            'kualitas' => 90,
        };

        // Use ImageMagick CLI directly as it is more reliable in this environment
        return $this->convertPdfWithImageMagick($pdfPath, $outputPath, $dpi, $quality_val);
    }

    private function convertPdfWithImageMagick($pdfPath, $outputPath, $dpi, $quality)
    {
        // -background white -alpha remove -alpha off -flatten: 
        //   Memastikan background transparan di-composite ke putih sebelum export ke JPG
        //   (JPG tidak support transparency, tanpa ini background jadi hitam)
        $cmd = sprintf(
            'magick -density %d "%s" -background white -alpha remove -alpha off -flatten -quality %d "%s-%%d.jpg"',
            $dpi,
            $pdfPath,
            $quality,
            $outputPath
        );

        exec($cmd, $output, $returnVar);

        $images = [];
        $files = glob($outputPath . '-*.jpg');
        foreach ($files as $file) {
            if (file_exists($file)) {
                $images[] = $file;
            }
        }

        natsort($images); // Sort by page number naturally (1, 2, 10...)
        $images = array_values($images); // Reset array keys after natsort
        return $images;
    }

    private function compressImage($imagePath, $quality)
    {
        $quality_val = match($quality) {
            'extreme' => 50,
            'seimbang' => 75,
            'kualitas' => 85,
        };

        try {
            $cmd = sprintf('magick "%s" -quality %d "%s"', $imagePath, $quality_val, $imagePath);
            exec($cmd);
        } catch (Exception $e) {
            // Continue even if compression fails
        }
    }

    private function getGhostscriptPath()
    {
        // Try common Ghostscript paths on Windows
        $paths = [
            'C:\\Program Files\\gs\\gs10.01.2\\bin\\gswin64c.exe',
            'C:\\Program Files\\gs\\gs10.0.0\\bin\\gswin64c.exe',
            'C:\\Program Files (x86)\\gs\\gs10.0.0\\bin\\gswin32c.exe',
            'C:\\Program Files\\gs\\gs9.56.1\\bin\\gswin64c.exe',
            'gswin64c.exe',
            'gswin32c.exe',
        ];

        foreach ($paths as $path) {
            if (file_exists($path) || shell_exec("where {$path} 2>nul")) {
                return $path;
            }
        }

        // Default fallback
        return 'gswin64c.exe';
    }

    private function extractPdfPages($inputPath, $outputPath, $start, $end)
    {
        $ghostscriptPath = $this->getGhostscriptPath();
        $cmd = "\"{$ghostscriptPath}\" -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dFirstPage={$start} -dLastPage={$end} -sOutputFile=\"{$outputPath}\" \"{$inputPath}\"";
        
        exec($cmd, $output, $returnVar);
        
        if ($returnVar !== 0) {
            \Log::error("Ghostscript extract failed for page {$start}-{$end}", [
                'return_var' => $returnVar,
                'output' => $output,
                'cmd' => $cmd
            ]);
        }
    }

    private function getPdfPageCount($path)
    {
        $ghostscriptPath = $this->getGhostscriptPath();
        $escapedPath = str_replace('\\', '/', $path);
        
        // Command to print page count
        $cmd = "\"{$ghostscriptPath}\" -q -dNODISPLAY -dNOSAFER -c \"({$escapedPath}) (r) file runpdfbegin pdfpagecount = quit\"";
        
        $output = shell_exec($cmd);
        
        // Extract the last number from output (in case of warnings)
        if (preg_match('/(\d+)\s*$/', trim($output), $matches)) {
            $count = (int)$matches[1];
        } else {
            $count = 0;
        }

        // Fallback using identify (ImageMagick) if GS fails
        if ($count <= 0) {
            $cmdIdentify = "magick identify -format %n \"{$path}\"";
            $outputIdentify = shell_exec($cmdIdentify);
            $count = (int)trim($outputIdentify);
        }

        \Log::info("PDF Page Count for {$path}: {$count} (Raw Output: " . trim($output) . ")");
        return $count;
    }

    private function parsePageRanges($input, $maxPages)
    {
        $pages = [];
        $parts = explode(',', $input);
        foreach ($parts as $part) {
            $part = trim($part);
            if (strpos($part, '-') !== false) {
                list($start, $end) = explode('-', $part);
                $start = (int)$start;
                $end = (int)$end;
                for ($i = $start; $i <= min($end, $maxPages); $i++) {
                    if ($i > 0) $pages[] = $i;
                }
            } else {
                $p = (int)$part;
                if ($p > 0 && $p <= $maxPages) $pages[] = $p;
            }
        }
        return array_unique($pages);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
