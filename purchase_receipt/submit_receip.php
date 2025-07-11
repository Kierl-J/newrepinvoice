<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $po_id = $_POST['po_id'];
    $receipt_number = $_POST['receipt_number'];
    $date_received = $_POST['date_received'];
    $received_by = $_POST['received_by'];

    $item_ids = $_POST['item_id'] ?? [];
    $received_qtys = $_POST['received_qty'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    // Insert into purchase_receipt_tb (header)
    $stmt = $pdo->prepare("
        INSERT INTO purchase_receipt_tb (po_id, receipt_number, date_received, received_by)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$po_id, $receipt_number, $date_received, $received_by]);
    $receipt_id = $pdo->lastInsertId();

    // Loop through each item
    for ($i = 0; $i < count($item_ids); $i++) {
        $item_id = $item_ids[$i];
        $qty_received = floatval($received_qtys[$i]);
        $item_remarks = $remarks[$i];

        // Skip empty or zero quantity
        if ($qty_received <= 0) continue;

        // Insert into purchase_receiptitem_tb (line items)
        $stmt = $pdo->prepare("
            INSERT INTO purchase_receiptitem_tb (receipt_id, purchaseorderitem_id, received_qty, remarks)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$receipt_id, $item_id, $qty_received, $item_remarks]);

        // Update purchaseorderitem_tb (add to received)
        $stmt = $pdo->prepare("
            UPDATE purchaseorderitem_tb
            SET received = received + ?
            WHERE id = ?
        ");
        $stmt->execute([$qty_received, $item_id]);
    }

    // Redirect or show success
    header("Location: receipt_success.php"); // or return a success message
    exit;
}
