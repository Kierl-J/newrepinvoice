<?php
require '../db.php';

// Always fetch the first active company
$stmt = $pdo->prepare("SELECT * FROM company_tb WHERE status = 'Active' LIMIT 1");
$stmt->execute();
$company = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($company ?: []);
