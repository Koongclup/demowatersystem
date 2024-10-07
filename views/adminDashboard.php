<?php
session_start();
if (($_SESSION['role']!="admin" ) ) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
    <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php require 'layout/header.php'; ?>
    <?php require 'layout/loading.php'; ?>
    <div class="container mt-3 mb-5">
        <h3>หน้าหลัก</h3>
        <div class="row">
            <div class="col-md-5 my-3">
                <div class="">
                    <div class="card-body">
                        <div id="billChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-7 my-3">
                <div class=" ">
                    <div class="card-body">
                        <div id="waterUsageChart" height="100"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 my-3">
                <div class="">
                    <div class="card-body">
                        <h4>All Users  :  <e id="userCount"></e></h4>
                        <table class="table table-sm table-striped " id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody id="usersBody">
                                <!-- Rows will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 my-3">
                <div class="">
                    <div class="card-body">
                        <h4>All Water Usage</h4>
                        <table class="table table-sm table-striped " id="waterUsageTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Usage Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="waterUsageBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="cok-md-12">
            <div class="">
                <div class="card-body">
                    <h4>All Bills</h4>
                    <table class="table table-sm table-striped " id="billsTable">
                        <thead class="bg-info">
                            <tr>
                                <th>ID</th>
                                <th>User ID</th>
                                <th>Billing Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="billsBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layout/footer.php' ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchUsers();
            fetchWaterUsage();
            fetchBills();
            fetchUserCount();
            fetchWaterUsageData();
            fetchBillData();
        });

        function fetchUsers() {
            $.ajax({
                url: '../app/rountes/rountUser.php',
                type: 'POST',
                data: {
                    action: 'fetchAll'
                },

                dataType: 'json',
                success: function(response) {
                    let rows = '';
                    if (response) {
                        response.forEach(user => {
                            rows += `<tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                    </tr>`;
                        });
                        $('#usersBody').html(rows);
                        // Initialize DataTable after populating users
                        $('#usersTable').DataTable({
                            "iDisplayLength": 5,
                            "lengthMenu": [
                                [5, 10, 25, 50, 100, -1],
                                [5, 10, 25, 50, 100, "All"]
                            ],
                        });
                    }
                }
            });
        }

        function fetchWaterUsage() {
            $.ajax({
                url: '../app/rountes/rountWaterUsage.php',
                type: 'POST',
                data: {
                    action: 'fetchAll'
                },
                dataType: 'json',
                success: function(response) {
                    let rows = '';
                    if (response) {
                        response.forEach(usage => {
                            rows += `<tr>
                        <td>${usage.id}</td>
                        <td>${usage.user_id}</td>
                        <td>${usage.usage_date}</td>
                        <td>${usage.amount}</td>
                    </tr>`;
                        });
                        $('#waterUsageBody').html(rows);
                        // Initialize DataTable after populating water usage
                        $('#waterUsageTable').DataTable({
                            "iDisplayLength": 5,
                            "lengthMenu": [
                                [5, 10, 25, 50, 100, -1],
                                [5, 10, 25, 50, 100, "All"]
                            ],
                        });
                    }
                }
            });
        }

        function fetchBills() {
            $.ajax({
                url: '../app/rountes/rountBill.php',
                type: 'POST',
                data: {
                    action: 'fetchAll'
                },
                dataType: 'json',
                success: function(response) {
                    let rows = '';
                    if (response) {
                        response.forEach(bill => {
                            rows += `<tr>
                        <td>${bill.id}</td>
                        <td>${bill.user_id}</td>
                        <td>${bill.billing_date}</td>
                        <td>${bill.total_amount}</td>
                        <td>${bill.status}</td>
                    </tr>`;
                        });
                        $('#billsBody').html(rows);
                        $('#billsTable').DataTable({
                            "iDisplayLength": 5,
                            "lengthMenu": [
                                [5, 10, 25, 50, 100, -1],
                                [5, 10, 25, 50, 100, "All"]
                            ],
                        });
                    }
                }
            });
        }

        function fetchUserCount() {
            $.ajax({
                url: '../app/rountes/rountUser.php',
                type: 'POST',
                data: {
                    action: 'fetchCount'
                },
                dataType: 'json',
                success: function(response) {
                    $('#userCount').html(`Total Users: ${response.count}`);
                }
            });
        }

        function fetchWaterUsageData() {
            $.ajax({
                url: '../app/rountes/rountWaterUsage.php',
                type: 'POST',
                data: {
                    action: 'fetchUsageData'
                },
                dataType: 'json',
                success: function(response) {
                    const dates = response.map(item => item.usage_date);
                    const totalUsage = response.map(item => item.total_usage);
                    renderWaterUsageChart(dates, totalUsage);
                }
            });
        }

        function fetchBillData() {
            $.ajax({
                url: '../app/rountes/rountBill.php',
                type: 'POST',
                data: {
                    action: 'fetchBillData'
                },
                dataType: 'json',
                success: function(response) {
                    const billingDates = response.map(item => item.billing_date);
                    const totalBilled = response.map(item => item.total_billed);
                    renderBillChart(billingDates, totalBilled);
                }
            });
        }

        function renderWaterUsageChart(dates, totalUsage) {
            const options = {
                chart: {
                    type: 'area',
                    height: 300
                },
                series: [{
                    name: 'Water Usage',
                    data: totalUsage
                }],
                xaxis: {
                    categories: dates
                }
            };
            const chart = new ApexCharts(document.querySelector("#waterUsageChart"), options);
            chart.render();
        }

        function renderBillChart(billingDates, totalBilled) {
            const options = {
                chart: {
                    type: 'bar',
                    height: 300
                },
                series: [{
                    name: 'Total Billed',
                    data: totalBilled
                }],
                xaxis: {
                    categories: billingDates
                }
            };
            const chart = new ApexCharts(document.querySelector("#billChart"), options);
            chart.render();
        }
    </script>
</body>

</html>