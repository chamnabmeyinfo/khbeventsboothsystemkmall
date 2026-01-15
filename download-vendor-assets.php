<?php
/**
 * Vendor Assets Downloader
 * Downloads all required vendor CSS/JS files from CDN to local vendor directory
 * Run this script once: php download-vendor-assets.php
 */

$baseDir = __DIR__ . '/public/vendor';
$files = [
    // Font Awesome 6.4.0
    'fontawesome/css/all.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'fontawesome/webfonts/fa-brands-400.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-brands-400.woff2',
    'fontawesome/webfonts/fa-regular-400.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-regular-400.woff2',
    'fontawesome/webfonts/fa-solid-900.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2',
    'fontawesome/webfonts/fa-v4compatibility.woff2' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-v4compatibility.woff2',
    
    // Ionicons 2.0.1
    'ionicons/css/ionicons.min.css' => 'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css',
    'ionicons/fonts/ionicons.woff' => 'https://code.ionicframework.com/ionicons/2.0.1/fonts/ionicons.woff',
    'ionicons/fonts/ionicons.ttf' => 'https://code.ionicframework.com/ionicons/2.0.1/fonts/ionicons.ttf',
    
    // AdminLTE 3.2
    'adminlte/css/adminlte.min.css' => 'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css',
    'adminlte/js/adminlte.min.js' => 'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js',
    
    // Bootstrap 4.6.2
    'bootstrap/css/bootstrap.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css',
    'bootstrap/js/bootstrap.bundle.min.js' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
    
    // Bootstrap 5.3.0 (for app.blade.php)
    'bootstrap5/css/bootstrap.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'bootstrap5/js/bootstrap.bundle.min.js' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    
    // DataTables 1.13.7
    'datatables/css/dataTables.bootstrap4.min.css' => 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css',
    'datatables/css/dataTables.bootstrap5.min.css' => 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css',
    'datatables/js/jquery.dataTables.min.js' => 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
    'datatables/js/dataTables.bootstrap4.min.js' => 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js',
    'datatables/js/dataTables.bootstrap5.min.js' => 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js',
    
    // SweetAlert2 11
    'sweetalert2/css/sweetalert2.min.css' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
    'sweetalert2/js/sweetalert2.min.js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js',
    
    // Toastr
    'toastr/css/toastr.min.css' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css',
    'toastr/js/toastr.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js',
    
    // Select2 4.1.0
    'select2/css/select2.min.css' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
    'select2/css/select2-bootstrap4.min.css' => 'https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css',
    'select2/js/select2.min.js' => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
    
    // Chart.js 4.4.0
    'chartjs/chart.min.css' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css',
    'chartjs/chart.umd.min.js' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
    
    // jQuery
    'jquery/jquery-3.7.1.min.js' => 'https://code.jquery.com/jquery-3.7.1.min.js',
    'jquery/jquery-3.7.0.min.js' => 'https://code.jquery.com/jquery-3.7.0.min.js',
    'jquery/jquery-3.6.0.min.js' => 'https://code.jquery.com/jquery-3.6.0.min.js',
    
    // jQuery UI 1.13.2
    'jquery-ui/css/jquery-ui.min.css' => 'https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css',
    'jquery-ui/jquery-ui.min.js' => 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
    
    // Moment.js 2.29.4
    'moment/moment.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js',
    
    // Panzoom 4.5.1
    'panzoom/panzoom.min.js' => 'https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js',
    
    // HTML2Canvas 1.4.1
    'html2canvas/html2canvas.min.js' => 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js',
];

function downloadFile($url, $destination) {
    $dir = dirname($destination);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $ch = curl_init($url);
    $fp = fopen($destination, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    return $httpCode === 200;
}

echo "Starting vendor assets download...\n\n";

$success = 0;
$failed = 0;

foreach ($files as $localPath => $url) {
    $destination = $baseDir . '/' . $localPath;
    
    // Skip if file already exists
    if (file_exists($destination)) {
        echo "✓ Skipping (exists): $localPath\n";
        $success++;
        continue;
    }
    
    echo "Downloading: $localPath... ";
    
    if (downloadFile($url, $destination)) {
        echo "✓ Success\n";
        $success++;
    } else {
        echo "✗ Failed\n";
        $failed++;
    }
}

echo "\n";
echo "========================================\n";
echo "Download Complete!\n";
echo "Success: $success\n";
echo "Failed: $failed\n";
echo "========================================\n";

if ($failed > 0) {
    echo "\nSome files failed to download. You may need to download them manually.\n";
    echo "See REMOVE_CDN_GUIDE.md for manual download instructions.\n";
}
