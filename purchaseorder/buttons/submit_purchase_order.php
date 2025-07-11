<?php
require '../db.php';

// 1. Sanitize input
$vendor_code = $_POST['vendor'] ?? '';
$po_number = $_POST['poNO_po'] ?? '';
$date_po = $_POST['date_po'] ?? '';
$good_thru = $_POST['goodthru_po'] ?? '';

//Remit to:
$remit_name = $_POST['remit_name'] ?? '';
$remit_address = $_POST['remit_address'] ?? '';
$remit_city = $_POST['remit_city'] ?? '';
$remit_country = $_POST['remit_country'] ?? '';
$remit_zip = $_POST['remit_zip'] ?? '';

//Ship to:
$shipto_name = $_POST['shipto_name'] ?? '';
$shipto_address = $_POST['shipto_address'] ?? '';
$shipto_city = $_POST['shipto_city'] ?? '';
$shipto_country = $_POST['shipto_country'] ?? '';
$shipto_zip = $_POST['shipto_zip'] ?? '';

$items = $_POST['items'] ?? [];

try {
    $pdo->beginTransaction();

    // ✅ Calculate total amount
    $total_amount = 0;
    foreach ($items as $item) {
        $qty = floatval($item['qty'] ?? 0);
        $unit_price = floatval($item['unit_price'] ?? 0);
        $total_amount += $qty * $unit_price;
    }

    // 2. Insert into purchaseorder_tb (now includes total_amount)
    $stmt = $pdo->prepare("
        INSERT INTO purchaseorder_tb (
            vendor_code, remit_name, remit_address, remit_city, remit_country, remit_zip,
            shipto_name, shipto_address, shipto_city, shipto_country, shipto_zip,
            po_number, date_po, good_thru, total_amount
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $vendor_code,
        $remit_name,
        $remit_address,
        $remit_city,
        $remit_country,
        $remit_zip,
        $shipto_name,
        $shipto_address,
        $shipto_city,
        $shipto_country,
        $shipto_zip,
        $po_number,
        $date_po,
        $good_thru,
        $total_amount
    ]);

    $po_id = $pdo->lastInsertId();

    // 3. Insert items into purchaseorderitem_tb
    $stmt_item = $pdo->prepare("
        INSERT INTO purchaseorderitem_tb (
            purchaseorder_id, quantity, received, item, um, description,
            gl_account, unit_price, job, phase
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $stmt_item->execute([
            $po_id,
            $item['qty'] ?? 0,
            $item['received'] ?? 0,
            $item['item'] ?? '',
            $item['um'] ?? '',
            $item['description'] ?? '',
            $item['gl_account'] ?? '',
            $item['unit_price'] ?? 0,
            $item['job'] ?? '',
            $item['phase'] ?? ''
        ]);
    }

    $pdo->commit();
    echo "Purchase Order successfully saved!";
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
