<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Usage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Water Usage</h2>
    <table class="table table-striped" id="waterUsageTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Usage Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody id="usageBody"></tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    fetchWaterUsage();

    function fetchWaterUsage() {
        $.ajax({
            url: '../controllers/WaterUsageController.php',
            type: 'POST',
            data: { action: 'fetch' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    let rows = '';
                    response.data.forEach(usage => {
                        rows += `<tr>
                                    <td>${usage.id}</td>
                                    <td>${usage.user_id}</td>
                                    <td>${usage.usage_date}</td>
                                    <td>${usage.amount}</td>
                                  </tr>`;
                    });
                    $('#usageBody').html(rows);
                }
            }
        });
    }
});
</script>
</body>
</html>
