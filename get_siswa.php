<?php
require_once 'koneksi.php';

// Set header JSON di awal
header('Content-Type: application/json');

try {
    $db = new database();
    
    if(!isset($_GET['nisn'])) {
        throw new Exception('NISN tidak diberikan');
    }
    
    $nisn = trim($_GET['nisn']);
    if(empty($nisn)) {
        throw new Exception('NISN tidak boleh kosong');
    }
    
    $siswa = $db->get_siswa_by_nisn($nisn);
    
    if($siswa) {
        // Format tanggal lahir jika ada
        if(!empty($siswa['tanggal_lahir'])) {
            $siswa['tanggal_lahir'] = date('Y-m-d', strtotime($siswa['tanggal_lahir']));
        }
        echo json_encode($siswa);
    } else {
        throw new Exception('Siswa tidak ditemukan');
    }
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
    error_log($e->getMessage());
}
?>