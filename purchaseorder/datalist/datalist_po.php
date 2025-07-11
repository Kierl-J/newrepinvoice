<?php
require 'db.php'; // ensure $pdo is available

function generateNextPoNumber($pdo)
{
    $stmt = $pdo->query("SELECT po_number FROM purchaseorder_tb ORDER BY id DESC LIMIT 1");
    $row = $stmt->fetch();

    if ($row && isset($row['po_number'])) {
        $latestPo = $row['po_number'];

        // Match text prefix + number at the end
        if (preg_match('/^(.*?)(\d+)$/', $latestPo, $matches)) {
            $prefix = $matches[1];
            $number = $matches[2];
            $nextNumber = str_pad((int)$number + 1, strlen($number), '0', STR_PAD_LEFT);
            return $prefix . $nextNumber;
        }
    }

    // If no PO found or not matching pattern
    return 'PO-0001';
}

$generatedPoNo = generateNextPoNumber($pdo);
