<?php
require 'db.php';

$po_id = $_GET['po_id'] ?? 0;
if (!$po_id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing PO ID"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT poi.id, poi.item, poi.quantity, poi.received
    FROM purchaseorderitem_tb poi
    WHERE poi.purchaseorder_id = ?
");
$stmt->execute([$po_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($items);
