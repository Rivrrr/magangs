<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$id = $_POST['id_dosen'];
$nama = $_POST['nama'];
$status = $_POST['status'];

$stmt = $conn->prepare("
    UPDATE dosen
    SET nama = ?,
        status = ?
    WHERE id_dosen = ?
");

$stmt->execute([$nama, $status, $id]);

header("Location: dosen.php");
exit;