<?php
require 'db.php';
include './datalist/datalist_loader.php';
include './datalist/datalist_po.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat&display=swap" rel="stylesheet">

    <title>Document</title>
    <style>
        .container {
            display: flex;
            gap: 5em;
        }

        body {
            background-color: #f9fafb;
            /* light neutral gray */
            font-family: 'Montserrat', sans-serif;
            color: #1f2937;
            /* dark gray for readability */
            margin: 0;
            padding: 1em;
        }

        .boxA,
        .boxB,
        .boxC,
        .boxD {

            font-family: 'Montserrat', sans-serif;
        }

        button {
            /* background-color: red; */
            /* strong construction navy */
            /* color: white; */
            /* border: none; */
            padding: 0.6em 1.2em;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            font-family: 'Bebas Neue', cursive;
            letter-spacing: 0.5px;
            font-size: 1rem;
        }

        button:hover {
            background-color: gray;
            /* lighter blue on hover */
        }


        table.tablePO {
            table-layout: fixed;
            border-collapse: collapse;
        }

        table.tablePO th,
        table.tablePO td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
        }

        .col-qty {
            width: 15px;
        }

        .col-received {
            width: 25px;
        }

        .col-item {
            width: 150px;
        }

        .col-um {
            width: 50px;
        }

        .col-desc {
            width: 180px;
        }

        .col-gl {
            width: 100px;
        }

        .col-price {
            width: 100px;
        }

        .col-amount {
            width: 100px;
        }

        .col-job {
            width: 140px;
        }

        .col-phase {
            width: 140px;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 2px;
        }

        textarea {
            width: 100%;
            min-height: 24px;
            resize: none;
            overflow: hidden;
            box-sizing: border-box;
        }

        .button-area {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <hr>
    <form action="buttons/submit_purchase_order.php" method="POST" id="poForm">
        <div class="boxA">
            <label>Vendor ID: </label>
            <input required style="width: 200px;" list="vendorid" name="vendor" id="vendor">
            <datalist id="vendorid">
                <!-- should be populated with PHP -->
            </datalist>
        </div>
        <hr>

        <div class="container">
            <div class="boxB">
                <!-- Populate data from company_tb (database table) -->
                <!-- cp stands for company -->

                <label>Remit to:</label><br>
                <input id="remit_name" name="remit_name" placeholder="Name" type="text"><br>
                <input id="remit_address" name="remit_address" placeholder="Address" type="text"><br>
                <input id="remit_city" name="remit_city" placeholder="City" type="text"><br>
                <input id="remit_country" name="remit_country" placeholder="Country" type="text"><br>
                <input id="remit_zip" name="remit_zip" placeholder="Zip" type="text"><br>

            </div>

            <div class="boxC">
                <!-- Populate data from vendor_tb) (database table) -->
                <!-- vd stands for vendor -->
                <label>Ship to:</label><br>
                <input id="shipto_name" name="shipto_name" placeholder="Name" type="text"><br>
                <input id="shipto_address" name="shipto_address" placeholder="Address" type="text"><br>
                <input id="shipto_city" name="shipto_city" placeholder="City" type="text"><br>
                <input id="shipto_country" name="shipto_country" placeholder="Country" type="text"><br>
                <input id="shipto_zip" name="shipto_zip" placeholder="Zip" type="text"><br>

            </div>

            <div style="margin-top: 25px;" class="boxD">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <label for="date_po" style="width: 100px;">Date:</label>
                    <input id="date_po" name="date_po" type="date" style="width: 150px;">
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                    <label for="goodthru_po" style="width: 100px;">Good Thru:</label>
                    <input id="goodthru_po" name="goodthru_po" type="date" style="width: 150px;">
                </div>
                <div style="display: flex; align-items: center;">
                    <label for="poNO_po" style="width: 100px;">PO No.</label>
                    <input id="poNO_po" name="poNO_po" type="text" style="width: 150px;" value="<?= htmlspecialchars($generatedPoNo) ?>">

                </div>
            </div>

        </div>
        <hr>


        <div class="tableContainer" style="width: 1820px; overflow-x: auto;">
            <form>
                <table class="tablePO" id="poTable">
                    <thead>
                        <tr>
                            <th class="col-qty">Quantity</th>
                            <th class="col-received">Received</th>
                            <th class="col-item">Item</th>
                            <th class="col-um">U/M</th>
                            <th class="col-desc">Description</th>
                            <th class="col-gl">GL Account</th>
                            <th class="col-price">Unit Price</th>
                            <th class="col-amount">Amount</th>
                            <th class="col-job">Job</th>
                            <th class="col-phase">Phase</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="poTableBody" oninput="handleAutoCalc(event)">
                        <!-- Initial row -->
                        <tr>
                            <td><input id="qty_po" type="text" name="items[0][qty]"></td>
                            <td><input id="received_po" type="text" name="items[0][received]" readonly></td>
                            <td><input id="item_po" list="items" name="items[0][item]"></td>
                            <td><input id="um_po" type="text" name="items[0][um]"></td>
                            <td><textarea required id="description_po" name="items[0][description]" oninput="autoResize(this)"></textarea></td>
                            <td><input id="glaccount_po" list="gl_accounts" name="items[0][gl_account]"></td>
                            <td><input id="unitprice_po" type="text" name="items[0][unit_price]" value="0.00"></td>
                            <td><input id="amount+[p" type="text" name="items[0][amount]"></td>
                            <td><input id="job_po" id="" list="jobs" name="items[0][job]"></td>
                            <td><input id="phase_po" list="phases" name="items[0][phase]"></td>
                            <td><button type="button" onclick="deleteRow(this)">Delete</button></td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <div style="font-weight: bold; padding-top: 1em;">
                        Total Amount: ₱<span id="totalAmount">0.00</span>
                    </div>
                </div>
            </form>



            <div class="button-area">
                <button type="button" onclick="addRow()">Add Row</button>
            </div>
        </div>
        <div class="button-area">
            <button type="submit" form="poForm">Submit Purchase Order</button>
        </div>
    </form>



    <script>
        function addRow() {
            const tableBody = document.getElementById("poTableBody");
            const rowCount = tableBody.rows.length;
            const row = tableBody.rows[0].cloneNode(true);

            const inputs = row.querySelectorAll("input, textarea, select");
            inputs.forEach(input => {
                if (input.name) {
                    const field = input.name.match(/\[([a-z_]+)\]$/)[1];
                    input.name = `items[${rowCount}][${field}]`;

                    // Clear values
                    input.value = (field === "unitprice_po") ? "0.00" : "";

                    // Reset readonly only for 'received'
                    input.readOnly = (field === "received_po");

                    // Reset textarea height if needed
                    if (input.tagName === "TEXTAREA") {
                        input.style.height = '24px';
                    }
                }
            });

            tableBody.appendChild(row);
        }




        function deleteRow(button) {
            const row = button.closest("tr");
            const tableBody = document.getElementById("poTableBody");
            if (tableBody.rows.length > 1) {
                row.remove();
                renumberRows();
            } else {
                alert("At least one row is required.");
            }
        }


        updateTotalAmount();


        function renumberRows() {
            const rows = document.querySelectorAll("#poTableBody tr");
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll("input");
                inputs.forEach(input => {
                    if (input.name) {
                        const field = input.name.match(/\[([a-z_]+)\]$/)[1];
                        input.name = `items[${index}][${field}]`;
                    }
                });
            });
        }

        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date_po').value = today;

        function handleAutoCalc(e) {
            const target = e.target;
            const row = target.closest("tr");
            if (!row) return;

            const qty = parseFloat(row.querySelector('[name$="[qty]"]')?.value || 0);
            const price = parseFloat(row.querySelector('[name$="[unit_price]"]')?.value || 0);
            const tax = parseFloat(row.querySelector('[name$="[tax]"]')?.value || 0);
            const amountInput = row.querySelector('[name$="[amount]"]');

            if (amountInput) {
                const amount = (qty * price) + tax;
                amountInput.value = amount.toFixed(2);
            }
        }


        function updateTotalAmount() {
            let total = 0;
            const rows = document.querySelectorAll("#poTableBody tr");

            rows.forEach(row => {
                const amountInput = row.querySelector('input[name*="[amount]"]');
                const val = parseFloat(amountInput?.value) || 0;
                total += val;
            });

            document.getElementById("totalAmount").textContent = total.toFixed(2);
        }

        document.getElementById("poTableBody").addEventListener("input", (e) => {
            if (
                e.target.name?.includes("[qty]") ||
                e.target.name?.includes("[unit_price]") ||
                e.target.name?.includes("[tax]")
            ) {
                const row = e.target.closest("tr");
                const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
                const tax = parseFloat(row.querySelector('input[name*="[tax]"]')?.value) || 0;

                const amount = (qty * price) + tax;
                const amountInput = row.querySelector('input[name*="[amount]"]');
                if (amountInput) amountInput.value = amount.toFixed(2);

                updateTotalAmount();
            }
        });

        document.getElementById('vendor').addEventListener('change', function() {
            const vendorCode = this.value;
            if (!vendorCode) return;

            fetch(`./logic/get_vendor_info.php?code=${encodeURIComponent(vendorCode)}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('shipto_name').value = data.vendor_name || '';
                        document.getElementById('shipto_address').value = data.address || '';
                        document.getElementById('shipto_city').value = data.city || '';
                        document.getElementById('shipto_country').value = data.country || '';
                        document.getElementById('shipto_zip').value = data.zip || '';
                    }
                })
                .catch(err => console.error("Vendor fetch error:", err));
        });
        window.addEventListener('DOMContentLoaded', function() {
            fetch('./logic/get_company_info.php')
                .then(res => res.json())
                .then(data => {
                    if (data) {
                        document.getElementById('remit_name').value = data.name || '';
                        document.getElementById('remit_address').value = data.address || '';
                        document.getElementById('remit_city').value = data.city || '';
                        document.getElementById('remit_country').value = data.country || '';
                        document.getElementById('remit_zip').value = data.zip || '';
                    }
                })
                .catch(err => console.error('Company fetch error:', err));
        });
    </script>

    </script>

</body>

</html>