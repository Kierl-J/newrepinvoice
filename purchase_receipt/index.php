<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="POST" action="submit_receipt.php">
        <label>PO Number:</label>
        <select name="po_id" id="poDropdown" required>
            <option value="">-- Select PO --</option>
            <?php
            require 'db.php';
            $stmt = $pdo->query("SELECT id, po_number FROM purchaseorder_tb ORDER BY po_number ASC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['po_number']}</option>";
            }
            ?>
        </select>

        <label>Receipt Number:</label>
        <input type="text" name="receipt_number" required>

        <label>Date Received:</label>
        <input type="date" name="date_received" required>

        <label>Received By:</label>
        <input type="text" name="received_by">

        <table id="itemTable">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Ordered</th>
                    <th>Already Received</th>
                    <th>New Received Qty</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" style="text-align:center;">Please select a PO number above.</td>
                </tr>
            </tbody>
        </table>

        <button type="submit">Save Receipt</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const poDropdown = document.getElementById('poDropdown');
            const tbody = document.querySelector('#itemTable tbody');

            poDropdown.addEventListener('change', function() {
                const po_id = this.value;
                if (!po_id) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Please select a PO number.</td></tr>';
                    return;
                }

                fetch('get_po_items.php?po_id=' + po_id)
                    .then(res => {
                        if (!res.ok) throw new Error("HTTP error " + res.status);
                        return res.json();
                    })
                    .then(items => {
                        console.log("Loaded Items:", items); // DEBUG

                        tbody.innerHTML = '';
                        if (!items.length) {
                            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No items found for this PO.</td></tr>';
                            return;
                        }

                        items.forEach(item => {
                            const maxQty = parseFloat(item.quantity) - parseFloat(item.received);
                            tbody.innerHTML += `
            <tr>
              <td>${item.item} <input type="hidden" name="item_id[]" value="${item.id}"></td>
              <td>${item.quantity}</td>
              <td>${item.received}</td>
              <td><input type="number" name="received_qty[]" step="0.01" min="0" max="${maxQty}" value="0" required></td>
              <td><input type="text" name="remarks[]"></td>
            </tr>
          `;
                        });
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Error loading items.</td></tr>';
                    });
            });
        });
    </script>

</body>

</html>