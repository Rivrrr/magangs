<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dosen.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$id_dosen = $_POST['id_dosen'] ?? '';

if (!empty($id_dosen)) {
    try {
        $stmt = $conn->prepare("DELETE FROM dosen WHERE id_dosen = :id");
        $stmt->bindParam(':id', $id_dosen);
        $stmt->execute();
        
    } catch(PDOException $e) {
        error_log("Gagal menghapus dosen: " . $e->getMessage());
    }
}

header("Location: dosen.php");
exit;
?>