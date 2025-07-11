<?php
require 'db.php'; // make sure $pdo is available

// Fetch Jobs
$job_stmt = $pdo->query("SELECT job_code, job_name FROM job_tb WHERE status = 'Active'");
$jobs = $job_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Phases
$phase_stmt = $pdo->query("SELECT DISTINCT phase_code, phase_name FROM phase_tb WHERE status = 'Active'");
$phases = $phase_stmt->fetchAll(PDO::FETCH_ASSOC);

// Load vendors
$vendor_stmt = $pdo->query("SELECT vendor_code, vendor_name FROM vendor_tb WHERE status = 'Active'");
$vendors = $vendor_stmt->fetchAll();

// Load gl_accounts
$gl_stmt = $pdo->query("SELECT gl_code, gl_name FROM gl_account_tb WHERE status = 'Active'");
$gl_accounts = $gl_stmt->fetchAll(PDO::FETCH_ASSOC);

// Load item_po

$item_stmt = $pdo->query("SELECT item_code, item_name FROM item_tb WHERE status = 'Active'");
$items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Job Datalist -->
<datalist id="jobs">
    <?php foreach ($jobs as $job): ?>
        <option value="<?= htmlspecialchars($job['job_code']) ?>"><?= htmlspecialchars($job['job_name']) ?></option>
    <?php endforeach; ?>
</datalist>

<!-- Phase Datalist -->
<datalist id="phases">
    <?php foreach ($phases as $phase): ?>
        <option value="<?= htmlspecialchars($phase['phase_code']) ?>"><?= htmlspecialchars($phase['phase_name']) ?></option>
    <?php endforeach; ?>
</datalist>

<datalist id="vendorid">
    <?php foreach ($vendors as $v): ?>
        <option value="<?= htmlspecialchars($v['vendor_code']) ?>">
            <?= htmlspecialchars($v['vendor_code']) ?> - <?= htmlspecialchars($v['vendor_name']) ?>
        </option>
    <?php endforeach; ?>
</datalist>

<!-- GL Account Datalist -->
<datalist id="gl_accounts">
    <?php foreach ($gl_accounts as $gl): ?>
        <option value="<?= htmlspecialchars($gl['gl_code']) ?>">
            <?= htmlspecialchars($gl['gl_code']) ?> - <?= htmlspecialchars($gl['gl_name']) ?>
        </option>
    <?php endforeach; ?>
</datalist>

<!-- Items Datalist -->
<datalist id="items">
    <?php foreach ($items as $item): ?>
        <option value="<?= htmlspecialchars($item['item_code']) ?>">
            <?= htmlspecialchars($item['item_code']) ?> - <?= htmlspecialchars($item['item_name']) ?>
        </option>
    <?php endforeach; ?>
</datalist>