<?php
require 'db.php';

$stmt = $pdo->query("
    SELECT pr.id, pr.receipt_number, pr.date_received, pr.received_by,
           po.po_number, po.vendor_code,
           COUNT(pri.id) AS total_items
    FROM purchase_receipt_tb pr
    JOIN purchaseorder_tb po ON po.id = pr.po_id
    LEFT JOIN purchase_receiptitem_tb pri ON pri.receipt_id = pr.id
    GROUP BY pr.id
    ORDER BY pr.date_received DESC
");
$receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Purchase Receipt List</title>
  <style>
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <h2>Purchase Receipts</h2>
  <table>
    <thead>
      <tr>
        <th>Receipt #</th>
        <th>Date Received</th>
        <th>PO Number</th>
        <th>Vendor</th>
        <th>Received By</th>
        <th>Total Items</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($receipts as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['receipt_number']) ?></td>
          <td><?= htmlspecialchars($r['date_received']) ?></td>
          <td><?= htmlspecialchars($r['po_number']) ?></td>
          <td><?= htmlspecialchars($r['vendor_code']) ?></td>
          <td><?= htmlspecialchars($r['received_by']) ?></td>
          <td><?= htmlspecialchars($r['total_items']) ?></td>
          <td><a href="view_receipt.php?id=<?= $r['id'] ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
