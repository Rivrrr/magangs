<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mahasiswa.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$id_mahasiswa = $_POST['id_mahasiswa'] ?? '';

if (!empty($id_mahasiswa)) {
    try {

        $stmt = $conn->prepare("DELETE FROM mahasiswa WHERE id_mahasiswa = :id");
        $stmt->bindParam(':id', $id_mahasiswa);
        $stmt->execute();
        
    } catch(PDOException $e) {
        error_log("Error menghapus data: " . $e->getMessage());
    }
}

header("Location: mahasiswa.php");
exit;
?>