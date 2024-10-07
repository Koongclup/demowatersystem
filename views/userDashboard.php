<?php
session_start();
if (($_SESSION['role']!="user" ) ) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
    <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
<?php require 'layout/userHeader.php'; ?>
    <div class="container mt-5">
        <h3>User  <?php echo $_SESSION['user_id'].' : ' .$_SESSION['username']  ; ?></h3>
  
        <button class="btn btn-secondary" id="viewBillsBtn">View Bills</button>

        <div id="waterUsageForm" style="display:none;">
            <h3 class="mt-4">Add Water Usage</h3>
            <form id="waterUsageForm">
                <div class="mb-3">
                    <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <label for="usage_date" class="form-label">Usage Date</label>
                    <input type="date" class="form-control" id="usage_date" name="usage_date" required>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (liters)</label>
                    <input type="number" class="form-control" id="amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <div id="billsTable" >
            <h3 class="mt-4">Bills</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Billing Date</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody id="billsBody"></tbody>
            </table>
        </div>
    </div>
    <?php include 'layout/footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#addWaterUsageBtn').on('click', function() {
                $('#waterUsageForm').toggle();
                $('#billsTable').hide();
            });

            $('#viewBillsBtn').on('click', function() {
                $('#billsTable').toggle();
                $('#waterUsageForm').hide();
                fetchBills();
            });

            $('#waterUsageForm').on('submit', function(e) {
                e.preventDefault();

                // Get values from the form fields
                const user_id = $('#user_id').val();
                const usage_date = $('#usage_date').val();
                const amount = $('#amount').val();

                // Create data object
                const data = {
                    user_id: user_id,
                    usage_date: usage_date,
                    amount: amount,
                    action: 'create'
                };

                $.ajax({
                    url: '../app/rountes/rountWaterUsage.php',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire(response.message);
                        if (response.status === 'success') {
                            $('#waterUsageForm')[0].reset();
                        }
                    }
                });
            });

            var currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;

            function fetchBills() {
                $.ajax({
                    url: '../app/rountes/rountBill.php',
                    type: 'POST',
                    data: {
                        action: 'fetchUser',
                        user_id: currentUserId // Sending user_id as part of the request
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            let rows = '';
                            response.data.forEach(bill => {
                                rows += `<tr>
                                            <td>${bill.id}</td>
                                            <td>${bill.billing_date}</td>
                                            <td>${bill.total_amount}</td>
                                        </tr>`;
                                });
                            $('#billsBody').html(rows);
                        } else {
                            // Handle case where no bills are found
                            $('#billsBody').html('<tr><td colspan="3">No bills found</td></tr>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching bills:', textStatus, errorThrown);
                        $('#billsBody').html('<tr><td colspan="3">Error fetching bills</td></tr>');
                    }
                });
            }

            $(document).ready(function() {
                fetchBills(); // Fetch bills when the document is ready
            });

            // Logout functionality
            $('#logoutBtn').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You will be logged out!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, logout!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to logout.php
                        window.location.href = 'logout.php';
                    }
                });
            });
        });
    </script>
</body>

</html>