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
        <h3>Users : <e id='userCount'></e>
        </h3>

        <!-- Add User Button -->
        <button class="btn btn-sm shadow btn-primary" onclick="showAddUserForm()">Add User</button>
        <!-- Users Table -->
        <div class="row">
            <div class="col-md-6">
                <div id="userStatusChart" class="mt-2"></div>
            </div>

            <div class="col-md-6">
                <div id="userPaidBarChart" class="mt-2"></div>
            </div>
        </div>
        <table class="table table-sm table-striped " id="UsersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Pass</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="usersBody">
            </tbody>
        </table>

    </div>

    <!-- Bootstrap Toast Notification -->
    <?php include 'componant/toast.php' ?>
    <!-- Offcanvas for Add/Edit User -->
    <?php include 'componant/offcavas_user.php' ?>
    <!-- Footer -->
    <?php include 'layout/footer.php' ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchUsers();
            fetchUserCount();
            $('#UserForm').on('submit', function(e) {
                e.preventDefault();
                saveUser();
                fetchUseringDataForCharts();
            });
        });

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

        function fetchUsers() {
            console.log('Initializing DataTable and fetching Users data...');
            if ($.fn.DataTable.isDataTable('#UsersTable')) {
                $('#UsersTable').DataTable().destroy();
            }

            $('#UsersTable').DataTable({
                ajax: {
                    url: '../app/rountes/rountUser.php',
                    type: 'POST',
                    data: {
                        action: 'fetchAll'
                    },
                    dataSrc: ''
                },
                "iDisplayLength": 5,
                "lengthMenu": [
                    [5, 10, 25, 50, 100, -1],
                    [5, 10, 25, 50, 100, "All"]
                ],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'password',
                        render: function(data, type, row) {
                            return '******'; // Mask the password for security
                        }
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'role'
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
                        <button class="btn btn-outline-warning btn-sm" onclick="editUser(${row.id})"><i class="bi bi-pen"></i></button>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteUser(${row.id})"><i class="bi bi-trash"></i></button>
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
            fetchUseringDataForCharts();
        }

        // Function to fetch user data for rendering charts
        function fetchUseringDataForCharts() {
            console.log('Fetching Usering data for charts...');
            $.ajax({
                url: '../app/rountes/rountUser.php',
                type: 'POST',
                data: {
                    action: 'fetchAll'
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Users data fetched for charts successfully:', response);

                    if (response && Array.isArray(response)) {
                        let activeCount = 0;
                        let nonActiveCount = 0;
                        let adminCount = 0;
                        let userCount = 0;

                        // Count Active and Non-Active users
                        response.forEach(user => {
                            if (user.status === 'Y') {
                                activeCount++;
                            } else if (user.status === 'N') {
                                nonActiveCount++;
                            }
                        });

                        response.forEach(user => {
                            if (user.role === 'admin') {
                                adminCount++;
                            } else if (user.role === 'user') {
                                userCount++;
                            }
                        });

                        // Render charts using the counted data
                        renderUserStatusChart(activeCount, nonActiveCount);
                        renderUserPaidBarChart(adminCount, userCount);
                    } else {
                        console.error('Invalid response data. Expected an array of users.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch Usering data for charts:', error);
                    Swal.fire('Error', 'Failed to fetch Usering data for charts.', 'error');
                }
            });
        }

        // Function to render a status pie chart with ApexCharts
        function renderUserStatusChart(activeUser, nonActiveUser) {
            var options = {
                chart: {
                    type: 'pie',
                    height: 280
                },
                labels: ['Active', 'Non Active'], // Changed labels to 'Active' and 'Non Active'
                series: [activeUser, nonActiveUser], // Passed active and non-active counts
                colors: ['#00E396', '#FEB019'],
                legend: {
                    position: 'bottom'
                },
                title: {
                    text: 'User Status Overview'
                }
            };

            var chart = new ApexCharts(document.querySelector("#userStatusChart"), options);
            chart.render();
        }

        // Function to render a bar chart for active vs non-active users
        function renderUserPaidBarChart(adminCount, userCount) {
            var options = {
                chart: {
                    type: 'bar',
                    height: 280
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                series: [{
                    name: 'Users',
                    data: [adminCount, userCount] // Passed active and non-active counts
                }],
                xaxis: {
                    categories: ['admin', 'user'] // Changed categories to 'Active' and 'Non Active'
                },
                colors: ['#00E396', '#FEB019'],
                title: {
                    text: 'Type Admin and Users'
                }
            };

            var chart = new ApexCharts(document.querySelector("#userPaidBarChart"), options);
            chart.render();
        }

        function showAddUserForm() {
            // Clear the form fields
            $('#user_id').val('');
            $('#username').val('');
            $('#password').val('');
            $('#email').val('');
            $('#role').val('user');
            $('#status').val('Y');
            $('#offcanvasTitle').text('Add User');
            $('#UserOffcanvas').offcanvas('show');
        }

        function editUser(id) {
            $.ajax({
                url: '../app/rountes/rountUser.php',
                type: 'POST',
                data: {
                    action: 'read',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.length > 0) {
                        const user = response[0];
                        $('#offcanvasTitle').text(`Edit User ${user.id}`);
                        $('#user_id').val(user.id);
                        $('#username').val(user.username);
                        $('#password').val(user.password);
                        $('#email').val(user.email);
                        $('#role').val(user.role);
                        $('#status').val(user.status);

                        // Show the offcanvas
                        $('#UserOffcanvas').offcanvas('show');

                    } else {
                        Swal.fire('Error', 'User not found', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch user details:', error);
                    Swal.fire('Error', 'Failed to fetch user details', 'error');
                }
            });
        }

        // Function to handle form submission
        $('#UserForm').on('submit', function(e) {
            e.preventDefault();
            //const id = $('#id').val();
            const id = $('#user_id').val();
            const username = $('#username').val(); // Ensure this field is present in your form
            const password = $('#password').val();
            const email = $('#email').val();
            const role = $('#role').val();
            const status = $('#status').val();
            const action = id ? 'update' : 'create';

            // Prepare data for submission directly in the AJAX request
            $.ajax({
                url: '../app/rountes/rountUser.php',
                type: 'POST',
                data: {
                    action: action,
                    id: id,
                    username: username,
                    password: password,
                    email: email,
                    role: role,
                    status: status,
                },
                success: function(response) {
                    console.log('User saved successfully:', response);
                    showToast('User saved successfully!', 'success');
                    $('#UserOffcanvas').offcanvas('hide');
                    $('#UsersTable').DataTable().ajax.reload();
                    fetchUserCount()
                    fetchUseringDataForCharts()

                },
                error: function(xhr, status, error) {
                    console.error('Failed to save User:', error);
                    Swal.fire('Error', 'Failed to save User', 'error');
                }
            });
        });

        function deleteUser(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this Users!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Deleting User with ID:', id);
                    $.ajax({
                        url: '../app/rountes/rountUser.php',
                        type: 'POST',
                        data: {
                            id: id,
                            action: 'delete'
                        },
                        success: function() {
                            console.log('User deleted successfully.');
                            fetchUsers();
                            fetchUserCount()
                            Swal.fire('Deleted!', 'User has been deleted.', 'success');
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to delete User:', error);
                            Swal.fire('Error', 'Failed to delete User', 'error');
                        }
                    });
                }
            });
        }

        function showToast(message, type) {
            const toastEl = document.getElementById('liveToast');
            const toastBody = toastEl.querySelector('.toast-body');
            let icon, backgroundColor;
            if (type === 'success') {
                icon = '<i class="bi bi-check-circle mx-2"></i>';
                backgroundColor = '#28a745';
            } else if (type === 'error') {
                icon = '<i class="bi bi-x-circle mx-2"></i>';
                backgroundColor = '#dc3545';
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
            }, 3000);
        }
    </script>
</body>

</html>