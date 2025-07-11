<?php
require 'db.php';
$id = $_GET['id'] ?? 0;

// Fetch header
$stmt = $pdo->prepare("
  SELECT pr.*, po.po_number, po.vendor_code
  FROM purchase_receipt_tb pr
  JOIN purchaseorder_tb po ON po.id = pr.po_id
  WHERE pr.id = ?
");
$stmt->execute([$id]);
$receipt = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch line items
$stmt = $pdo->prepare("
  SELECT pri.*, poi.item, poi.description, poi.um
  FROM purchase_receiptitem_tb pri
  JOIN purchaseorderitem_tb poi ON poi.id = pri.purchaseorderitem_id
  WHERE pri.receipt_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Receipt Details</title>
</head>

<body>
    <h2>Receipt #<?= htmlspecialchars($receipt['receipt_number']) ?></h2>
    <p><strong>PO:</strong> <?= $receipt['po_number'] ?> | <strong>Vendor:</strong> <?= $receipt['vendor_code'] ?></p>
    <p><strong>Date Received:</strong> <?= $receipt['date_received'] ?> | <strong>Received By:</strong> <?= $receipt['received_by'] ?></p>
    <p><strong>Remarks:</strong> <?= nl2br(htmlspecialchars($receipt['remarks'])) ?></p>

    <h3>Line Items</h3>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th>U/M</th>
                <th>Received Qty</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td><?= htmlspecialchars($item['um']) ?></td>
                    <td><?= htmlspecialchars($item['received_qty']) ?></td>
                    <td><?= htmlspecialchars($item['remarks']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br><a href="receipt_list.php">← Back to list</a>
</body>

</html>