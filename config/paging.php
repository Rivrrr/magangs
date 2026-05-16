<?php
$page_active = $_GET['page'] ?? 1;
$limit = $limit ?? 5;
$offset = ($page_active - 1) * $limit;

$stmtTotal = $conn->prepare($count_query);
$stmtTotal->execute($count_params ?? []);

$total_data = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
$total_page = ceil($total_data / $limit);

$start_data = ($page_active - 1) * $limit + 1;
$end_data = min($start_data + $limit - 1, $total_data);
?>