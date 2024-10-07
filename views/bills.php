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
        <div><h2>Bills <button class="btn btn-sm shadow btn-primary float-end" onclick="showAddBillForm()"><i class="bi bi-plus"></i>Add Bill</button></h2> </div>
        <!-- Add Bill Button -->
        

        <div class="row">
            <div class="col-md-3">
                <div id="billStatusChart" class="mt-5"></div>
            </div>

            <div class="col-md-3">
                <div id="userPaidBarChart" class="mt-5"></div>
            </div>

            <div class="col-md-6">
                <div id="billAreaChart" class="mt-5"></div>
            </div>

        </div>

        <!-- Offcanvas for Add/Edit Bill -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="billOffcanvas">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasTitle">Add/Edit Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="billForm">
                    <input type="hidden" id="bill_id" name="bill_id">
                    

                    <div class="mb-3">
                        <label for="usage_id" class="form-label">Usage</label>
                        <select class="form-select" id="usage_id" name="usage_id" required>
                            <option value="">Select Username</option>
                            <!-- Options will be dynamically populated here -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="billing_date" class="form-label">Billing Date</label>
                        <input type="date" class="form-control" id="billing_date" name="billing_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Amount</label>
                        <input type="number" class="form-control" id="total_amount" name="total_amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>


        <!-- Bills Table -->
        <table class="table table-striped table-sm " id="billsTable">
            <thead>
                <tr style="background-color:#222; color:#fff;">
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Billing Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="billsBody"></tbody>
        </table>
    </div>
    <!-- Bootstrap Toast Notification -->
   
    <?php include 'layout/footer.php' ?>
    <?php include 'componant/toast.php' ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchBills(); 
           
            $('#billForm').on('submit', function(e) {
                e.preventDefault();
                saveBill();
            });
        });

        
        // Function to show the Bootstrap toast notification
        function showToast(message, type) {
            const toastEl = document.getElementById('liveToast');
            const toastBody = toastEl.querySelector('.toast-body');
            let icon, backgroundColor;
            if (type === 'success') {
                icon = '<i class="bi bi-check-circle mx-2"></i>'; // Bootstrap icon for success
                backgroundColor = '#28a745'; // Green for success
            } else if (type === 'error') {
                icon = '<i class="bi bi-x-circle mx-2"></i>'; // Bootstrap icon for error
                backgroundColor = '#dc3545'; // Red for error
            } else {
                icon = ''; 
                backgroundColor = ''; 
            }
            toastBody.innerHTML = `${icon} ${message}`; 
            toastEl.className = `toast align-items-center text-white border-0`;
            toastEl.style.backgroundColor = backgroundColor; 
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            setTimeout(() => {
                toast.hide();
            }, 2000); 
        }


        // Fetch user data from backend
        function fetchUsers() {
            $.ajax({
                url: '../app/rountes/rountUser.php', 
                type: 'POST',
                data: {
                    action: 'fetchUsers'
                },
                success: function(response) {
                    let users = JSON.parse(response);
                    let userSelect = $('#usage_id'); 
                    userSelect.empty(); 
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

        // Function to initialize DataTable and fetch bills data
        function fetchBills() {
            console.log('Initializing DataTable and fetching bills data...');
            if ($.fn.DataTable.isDataTable('#billsTable')) {
                $('#billsTable').DataTable().destroy(); 
            }
            $('#billsTable').DataTable({
                ajax: {
                    url: '../app/rountes/rountBill.php', 
                    type: 'POST',
                    data: {
                        action: 'fetchAll'
                    }, 
                    dataSrc: '' 
                },
                columns: [{ data: 'id'},
                {data: 'usagename'},
                {data: 'billing_date'},
                {data: 'total_amount' },
                {data: 'status' },
                {data: null,
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-outline-warning btn-sm" onclick="editBill(${row.id})"><i class="bi bi-pen"></i></button>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteBill(${row.id})"><i class="bi bi-trash"></i></button>
                    `;
                        }
                    }
                ],
                paging: true,
                searching: true,
                ordering: true,
                order: [
                    [0, 'asc']
                ],
                drawCallback: function(settings) {
                    console.log('DataTable redrawn with new data.');
                }
            });
            fetchBillingDataForCharts();
        }

        function fetchBillingDataForCharts() {
            $.ajax({
                url: '../app/rountes/rountBill.php',
                type: 'POST',
                data: { action: 'fetchAll' },
                dataType: 'json',
                success: function(response) {
                    let paidCount = 0, pendingCount = 0, billingDates = {}, userPaidStatus = {};
                    response.forEach(bill => {
                        if (bill.status === 'paid') {
                            paidCount++;
                            userPaidStatus[bill.user_id] = userPaidStatus[bill.user_id] ? userPaidStatus[bill.user_id] + 1 : 1;
                        } else if (bill.status === 'pending') {
                            pendingCount++;
                        }
                        const date = bill.billing_date;
                        billingDates[date] = billingDates[date] ? billingDates[date] + bill.total_amount : bill.total_amount;
                    });
                    renderBillAreaChart(billingDates);
                    renderUserPaidBarChart(paidCount, pendingCount);
                    renderBillStatusChart(paidCount, pendingCount);
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Failed to fetch billing data for charts.', 'error');
                }
            });
        }

        function renderBillStatusChart(paidCount, pendingCount) {
            const options = {
                series: [paidCount, pendingCount],
                chart: { type: 'pie', height: 280 },
                labels: ['Paid', 'Pending'],
                legend: { position: 'bottom' }
            };
            const chart = new ApexCharts(document.querySelector('#billStatusChart'), options);
            chart.render();
        }

        function renderUserPaidBarChart(paidCount, pendingCount) {
            const options = {
                series: [{
                    data: [paidCount, pendingCount]
                }],
                chart: { type: 'bar', height: 280 },
                plotOptions: {
                    bar: { horizontal: true }
                },
                xaxis: { categories: ['Paid', 'Pending'] }
            };
            const chart = new ApexCharts(document.querySelector('#userPaidBarChart'), options);
            chart.render();
        }

        function renderBillAreaChart(billingDates) {
            const options = {
                series: [{
                    name: 'Total Amount',
                    data: Object.values(billingDates)
                }],
                chart: { type: 'area', height: 280 },
                xaxis: { categories: Object.keys(billingDates) }
            };
            const chart = new ApexCharts(document.querySelector('#billAreaChart'), options);
            chart.render();
        }

        function showAddBillForm() {
            // Clear the form fields
            $('#bill_id').val(''); // Reset hidden input for bill ID
            //$('#user_id').val(''); // Reset user ID field
            $('#usage_id').val(''); // Reset user ID field
            $('#billing_date').val(''); // Reset billing date field
            $('#total_amount').val(''); // Reset total amount field
            $('#status').val('paid'); // Set default status
            $('#offcanvasTitle').text('Add Bill'); // Set title for add
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('billOffcanvas'));
            offcanvas.show(); // Show the offcanvas
        }

        function editBill(id) {
            $.ajax({
                url: '../app/rountes/rountBill.php',
                type: 'POST',
                data: {
                    action: 'fetchSigle',
                    id: id
                },
                dataType: 'json',
                success: function(bills) {
                    console.log('Bill details fetched successfully:', bills);

                    if (Array.isArray(bills) && bills.length > 0) {
                        const bill = bills[0]; 
                        $('#offcanvasTitle').text(`Edit Bill ${bill.id}`); 
                        $('#bill_id').val(bill.id);
                        //$('#user_id').val(bill.user_id);
                        $('#usage_id').val(bill.usage_id);
                        $('#billing_date').val(bill.billing_date);
                        $('#total_amount').val(bill.total_amount);
                        $('#status').val(bill.status);
                        $('#billOffcanvas').offcanvas('show');
                    } else {
                        console.error('No bills found');
                        Swal.fire('Error', 'No bills found', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch bill details:', error);
                    Swal.fire('Error', 'Failed to fetch bill details', 'error');
                }
            });
        }

        // Function to handle form submission
        $('#billForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            const id = $('#bill_id').val();
            //const user_id = $('#user_id').val();
            const usage_id = $('#usage_id').val(); 
            const billing_date = $('#billing_date').val();
            const total_amount = $('#total_amount').val();
            const status = $('#status').val();
            const action = id ? 'update' : 'create';

            // Prepare data for submission directly in the AJAX request
            $.ajax({
                url: '../app/rountes/rountBill.php',
                type: 'POST',
                data: {
                    action: action,
                    id: id,
                    user_id: usage_id,
                    usage_id: usage_id,
                    billing_date: billing_date,
                    total_amount: total_amount,
                    status: status
                },
                success: function(response) {
                    console.log('Bill saved successfully:', response);
                    showToast('Bill saved successfully!', 'success'); 
                    $('#billOffcanvas').offcanvas('hide');
                    $('#billsTable').DataTable().ajax.reload();
                    fetchBills();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save bill:', error);
                    Swal.fire('Error', 'Failed to save bill', 'error');
                }
            });
        });

        function deleteBill(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this bill!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Deleting bill with ID:', id);
                    $.ajax({
                        url: '../app/rountes/rountBill.php',
                        type: 'POST',
                        data: {
                            id: id,
                            action: 'delete'
                        },
                        success: function() {
                            console.log('Bill deleted successfully.');
                            fetchBills();
                            Swal.fire('Deleted!', 'Bill has been deleted.', 'success');
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to delete bill:', error);
                            Swal.fire('Error', 'Failed to delete bill', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>