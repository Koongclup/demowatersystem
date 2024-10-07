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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>
    <?php require 'layout/header.php'; ?>
    <?php require 'layout/loading.php'; ?>
    <div class="container mt-3 mb-5">
        <h2>waters</h2>
        <button class="btn btn-sm shadow btn-primary" onclick="showAddwaterForm()">Add water</button>
        <br><br>
        <table class="table table-sm table-striped" id="waterUsageTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>UserID</th>
                <th>Usage Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="usageBody"></tbody>
    </table>
    </div>

    <!-- Bootstrap Toast Notification -->
  <?php require_once 'componant/toast.php' ?>

    <!-- Offcanvas for Add/Edit water -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="waterOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasTitle">Add/Edit water</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="waterForm">
                <input type="hidden" id="id" name="id">
                <div class="mb-3">
                        <label for="user_id" class="form-label">Usage</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Select Username</option>
                            <!-- Options will be dynamically populated here -->
                        </select>
                    </div>

                <div class="mb-3">
                    <label for="usage_date" class="form-label">Email</label>
                    <input type="date" class="form-control" id="usage_date" name="usage_date" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <input type="text" class="form-control" id="status" name="status" >
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="text" class="form-control" id="amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

    <?php include 'layout/footer.php' ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchWater(); 

            // Submit event for the add/edit water form
            $('#waterForm').on('submit', function(e) {
                e.preventDefault();
                savewater();
            });
        });

         // Fetch user data from backend
         function fetchUsers() {
            $.ajax({
                url: '../app/rountes/rountUser.php', // Replace with the actual path to your PHP file
                type: 'POST',
                data: {
                    action: 'fetchUsers'
                },
                success: function(response) {
                    let users = JSON.parse(response);
                    let userSelect = $('#user_id'); // Dropdown select element

                    userSelect.empty(); // Clear any existing options
                    userSelect.append('<option value="">Select Username</option>');

                    users.forEach(function(user) {
                        userSelect.append('<option value="' + user.id + '">' + user.username + '</option>');
                    });
                },
                error: function() {
                    alert('Failed to fetch users');
                }
            });
        }
        fetchUsers();
        function fetchWater() {
            console.log('Initializing DataTable and fetching waters data...');
            if ($.fn.DataTable.isDataTable('#waterUsageTable')) {
                $('#waterUsageTable').DataTable().destroy(); // Destroy previous instance if exists
            }
            $('#waterUsageTable').DataTable({
                ajax: {
                    url: '../app/rountes/rountWaterUsage.php',
                    type: 'POST',
                    data: {
                        action: 'fetchAll'
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'usagename'
                    },
                    {
                        data: 'usage_date',
                       
                    },
                    {
                        data: 'amount'
                    },
                    
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            return data === 'Y' ? 'Active' : 'No Active';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-outline-warning btn-sm" onclick="editwater(${row.id})"><i class="bi bi-pen"></i></button>
                        <button class="btn btn-outline-danger btn-sm" onclick="deletewater(${row.id})"><i class="bi bi-trash"></i></button>
                    `;
                        }
                    }
                ],

                paging: true,
                searching: true,
                ordering: true,
                order: [
                    [0, 'asc']
                ], // Default sorting
                // Callbacks to handle the draw event if needed
                drawCallback: function(settings) {
                    console.log('DataTable redrawn with new data.');
                }
            });
        }

        // Function to fetch watering data for rendering charts
        function fetchwateringDataForCharts() {
            console.log('Fetching watering data for charts...');
            $.ajax({
                url: '../app/rountes/rountWaterUsage.php',
                type: 'POST',
                data: {
                    action: 'fetchAll' // Change this as per your logic
                },
                dataType: 'json',
                success: function(response) {
                    console.log('waters data fetched for charts successfully:', response);
                    if (response && Array.isArray(response)) {
                        let paidCount = 0;
                        let pendingCount = 0;
                        let wateringDates = {};
                        let waterPaidStatus = {};

                        response.forEach(water => {
                            // Count statuses
                            if (water.status === 'paid') {
                                paidCount++;
                                waterPaidStatus[water.water_id] = waterPaidStatus[water.water_id] ? waterPaidStatus[water.water_id] + 1 : 1;
                            } else if (water.status === 'pending') {
                                pendingCount++;
                            }

                            // Group waters by date
                            const date = water.watering_date;
                            wateringDates[date] = wateringDates[date] ? wateringDates[date] + water.total_amount : water.total_amount;
                        });

                        // Render charts
                        if (Object.keys(wateringDates).length > 0) {
                            renderwaterAreaChart(wateringDates);
                        } else {
                            console.warn('No watering dates data available for the area chart.');
                        }

                        if (paidCount > 0 || pendingCount > 0) {
                            renderwaterPaidBarChart(paidCount, pendingCount);
                            renderwaterStatusChart(paidCount, pendingCount);
                        } else {
                            console.warn('No water status data available for the status chart.');
                        }

                        if (Object.keys(waterPaidStatus).length > 0) {
                            // renderwaterPaidBarChart(waterPaidStatus);
                        } else {
                            console.warn('No water paid status data available for the bar chart.');
                        }
                    } else {
                        console.error('Invalid response data. Expected an array of waters.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch waters for charts:', error);
                    Swal.fire('Error', 'Failed to fetch watering data for charts.', 'error');
                }
            });
        }

        function showAddwaterForm() {
            // Clear the form fields
            $('#id').val('');
            $('#user_id').val('');
            $('#usage_date').val('');
            $('#status').val('');
            $('#amount').val('');
            $('#offcanvasTitle').text('Add water');
            $('#waterOffcanvas').offcanvas('show');
        }

        function editwater(id) {
            $.ajax({
                url: '../app/rountes/rountWaterUsage.php',
                type: 'POST',
                data: {
                    action: 'fetchSingle',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.length > 0) {
                        const water = response[0];
                        $('#offcanvasTitle').text(`Edit water ${water.id}`);
                        $('#id').val(water.id);
                        $('#user_id').val(water.user_id);
                        $('#usage_date').val(water.usage_date);
                        $('#status').val(water.status);
                        $('#amount').val(water.amount);
                        // Show the offcanvas
                        $('#waterOffcanvas').offcanvas('show');

                    } else {
                        Swal.fire('Error', 'water not found', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch water details:', error);
                    Swal.fire('Error', 'Failed to fetch water details', 'error');
                }
            });
        }

        // Function to handle form submission
        $('#waterForm').on('submit', function(e) {
            e.preventDefault(); 
            const id = $('#id').val();
            const user_id = $('#user_id').val();
            const usage_date = $('#usage_date').val(); // Ensure this field is present in your form
            const status= $('#status').val();
            const amount= $('#amount').val();

            // Determine if we're adding or editing based on water_id
            const action = id ? 'update' : 'create';

            // Prepare data for submission directly in the AJAX request
            $.ajax({
                url: '../app/rountes/rountWaterUsage.php',
                type: 'POST',
                data: {
                    action: action,
                    id: id,
                    user_id:  user_id,
                    usage_date: usage_date,
                    status: status,
                    amount: amount,
                },
                success: function(response) {
                    console.log('water saved successfully:', response);
                    showToast('water saved successfully!', 'success');
                    $('#waterOffcanvas').offcanvas('hide');
                    $('#waterUsageTable').DataTable().ajax.reload();

                },
                error: function(xhr, status, error) {
                    console.error('Failed to save water:', error);
                    Swal.fire('Error', 'Failed to save water', 'error');
                }
            });
        });

        function deletewater(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this waters!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Deleting water with ID:', id);
                    $.ajax({
                        url: '../app/rountes/rountWaterUsage.php',
                        type: 'POST',
                        data: {
                            id: id,
                            action: 'delete'
                        },
                        success: function() {
                            console.log('water deleted successfully.');
                            fetchwaters();
                            Swal.fire('Deleted!', 'water has been deleted.', 'success');
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to delete water:', error);
                            Swal.fire('Error', 'Failed to delete water', 'error');
                        }
                    });
                }
            });
        }

        // Function to show the Bootstrap toast notification
        function showToast(message, type) {
            const toastEl = document.getElementById('liveToast');
            const toastBody = toastEl.querySelector('.toast-body');
            let icon, backgroundColor;

            // Determine the icon and color based on the type
            if (type === 'success') {
                icon = '<i class="bi bi-check-circle mx-2"></i>'; // Bootstrap icon for success
                backgroundColor = '#28a745'; // Green for success
            } else if (type === 'error') {
                icon = '<i class="bi bi-x-circle mx-2"></i>'; // Bootstrap icon for error
                backgroundColor = '#dc3545'; // Red for error
            } else {
                icon = ''; // No icon for default case
                backgroundColor = ''; // Default color
            }

            // Set the toast message and icon
            toastBody.innerHTML = `${icon} ${message}`; // Use innerHTML to include icon

            // Change the toast background color class based on type
            toastEl.className = `toast align-items-center text-white border-0`;
            toastEl.style.backgroundColor = backgroundColor; // Set the dynamic background color

            // Create a new toast instance and show it
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Optional: Auto-hide the toast after a few seconds
            setTimeout(() => {
                toast.hide();
            }, 2000); // Adjust the time as needed
        }

        function renderwaterStatusChart(paidCount, pendingCount) {
            const chartOptions = {
                chart: {
                    type: 'pie',
                    height: 300
                },
                dataLabels: {
                    enabled: true
                },
                series: [paidCount, pendingCount],
                labels: ['Paid', 'Pending'],
                colors: ['#28a745', '#e0e0e0'], // Green for Paid, Red for Pending
                legend: {
                    position: 'bottom'
                },
                title: {
                    text: 'water Status Distribution',
                    align: 'center'
                }
            };

            const chart = new ApexCharts(document.querySelector("#waterStatusChart"), chartOptions);
            chart.render();
        }

        function renderwaterAreaChart(wateringDates) {
            const dates = Object.keys(wateringDates);
            const amounts = Object.values(wateringDates);

            const chartOptions = {
                chart: {
                    type: 'area',
                    height: 300
                },
                series: [{
                    name: 'Total watered',
                    data: amounts
                }],
                xaxis: {
                    categories: dates
                },
                title: {
                    text: 'waters Grouped by Date',
                    align: 'center'
                },
                colors: ['#3498db'], // Blue for the area chart
            };

            const chart = new ApexCharts(document.querySelector("#waterAreaChart"), chartOptions);
            chart.render();
        }

        function renderwaterPaidBarChart(paidCount, pendingCount) {
            const chartOptions = {
                chart: {
                    type: 'bar',
                    height: 300,
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return val + " - " + opts.w.globals.seriesNames[opts.seriesIndex];
                    },
                },
                series: [{
                        name: 'Paid',
                        data: [paidCount],
                    },
                    {
                        name: 'Pending',
                        data: [pendingCount],
                    },
                ],
                xaxis: {
                    categories: ['water Status'], // Add this line for x-axis categories
                },
                colors: ['#28a745', '#343'], // Green for Paid, Grey for Pending
                legend: {
                    position: 'bottom',
                },
                title: {
                    text: 'water Status Distribution',
                    align: 'center',
                },
            };

            const chart = new ApexCharts(document.querySelector("#waterPaidBarChart"), chartOptions);
            chart.render();
        }
    </script>
</body>

</html>