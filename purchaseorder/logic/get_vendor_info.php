<?php
require '../db.php';

$vendorCode = $_GET['code'] ?? '';

if ($vendorCode) {
    $stmt = $pdo->prepare("SELECT * FROM vendor_tb WHERE vendor_code = ? AND status = 'Active'");
    $stmt->execute([$vendorCode]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($vendor ?: []);
}
