<?php
require_once 'koneksi.php';

// Set header JSON di awal
header('Content-Type: application/json');

try {
    $db = new database();
    
    if(isset($_GET['nisn']) && !empty($_GET['nisn'])) {
        // Validasi dan sanitize input
        $nisn = trim($_GET['nisn']);
        
        // Validasi format NISN (biasanya 10 digit angka)
        if (!preg_match('/^\d{10}$/', $nisn)) {
            echo json_encode([
                'error' => true,
                'message' => 'Format NISN tidak valid'
            ]);
            exit;
        }
        
        $exists = $db->check_nisn_exists($nisn);
        
        echo json_encode([
            'error' => false,
            'exists' => $exists
        ]);
    } else {
        echo json_encode([
            'error' => true,
            'message' => 'Parameter NISN tidak ditemukan'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Terjadi kesalahan server'
    ]);
}
?>