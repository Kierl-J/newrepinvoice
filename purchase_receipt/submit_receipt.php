<?php
require 'db.php';

$po_id = $_POST['po_id'];
$receipt_number = $_POST['receipt_number'];
$date_received = $_POST['date_received'];
$received_by = $_POST['received_by'] ?? '';
$remarks_header = $_POST['remarks_header'] ?? '';

// Insert into receipt header
$stmt = $pdo->prepare("INSERT INTO purchase_receipt_tb (po_id, receipt_number, date_received, received_by, remarks) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$po_id, $receipt_number, $date_received, $received_by, $remarks_header]);
$receipt_id = $pdo->lastInsertId();

// Insert each item
$item_ids = $_POST['item_id'];
$received_qtys = $_POST['received_qty'];
$remarks = $_POST['remarks'];

foreach ($item_ids as $i => $item_id) {
    $qty = floatval($received_qtys[$i]);
    $remark = $remarks[$i];

    // Insert into receipt item table
    $stmt = $pdo->prepare("INSERT INTO purchase_receiptitem_tb (receipt_id, purchaseorderitem_id, received_qty, remarks) VALUES (?, ?, ?, ?)");
    $stmt->execute([$receipt_id, $item_id, $qty, $remark]);

    // Update received quantity in PO item
    $pdo->prepare("UPDATE purchaseorderitem_tb SET received = received + ? WHERE id = ?")
        ->execute([$qty, $item_id]);
}

header("Location: receipt_success.php");
