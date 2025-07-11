<?php
require 'db.php';

$po_id = $_GET['po_id'] ?? 0;

$stmt = $pdo->prepare("
  SELECT poi.id, poi.item, poi.quantity, poi.received
  FROM purchaseorderitem_tb poi
  WHERE poi.purchaseorder_id = ?
");
$stmt->execute([$po_id]);

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;
