<?php
<?php
require_once 'koneksi.php';
$db = new database();

if(isset($_GET['nisn'])) {
    $nisn = $_GET['nisn'];
    $exists = $db->check_nisn_exists($nisn);
    
    header('Content-Type: application/json');
    echo json_encode(['exists' => $exists]);
    exit;
}