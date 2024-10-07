<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
      
    </style>
</head>

<body class="">
    <div class="container">
        <main class="form-signin text-center">
            <form id="loginForm">
                    <img class="mb-4" align="center" src="https://getbootstrap.com/docs/5.0/assets/brand/bootstrap-logo.svg" alt="" width="110" height="80">
                    <h1 class="h3 mb-3 fw-normal" aligin="center">Please sign in</h1>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" required minlength="2" maxlength="20">
                    <label for="floatingInput">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" required minlength="2">
                    <label for="floatingPassword">Password</label>
                </div>

               
                <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
                <button class="btn btn-link my-3" id="switchToRegister">Switch to Register</button>
                <p class="mt-5 mb-3 text-muted">&copy; 2017–2021</p>
            </form>
        </main>
    </div>

    <script>
        $(document).ready(function () {
            // Submit login form
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();
                if (this.checkValidity()) {
                    $.ajax({
                        url: '../app/rountes/rountAunt.php',
                        type: 'POST',
                        data: $(this).serialize() + '&action=login',
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                showToast('success', response.message);
                                setTimeout(() => {
                                    if (response.role === 'admin') {
                                        window.location.href = 'adminDashboard.php'; // Redirect to admin dashboard
                                    } else if (response.role === 'user') {
                                        window.location.href = 'userDashboard.php'; // Redirect to user dashboard
                                    }
                                }, 2000); 
                            } else {
                                showToast('error', response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            showToast('error', 'An error occurred while processing your request.');
                        }
                    });
                } else {
                    showToast('error', 'Please fill in the form correctly.');
                }
            });

            // Switch to register page
            $('#switchToRegister').on('click', function () {
                window.location.href = 'register.php';
            });

            // Function to show Toastr alerts
            function showToast(type, message) {
                toastr.clear(); // Clear previous alerts
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                if (type === 'success') {
                    toastr.success(message, 'Success');
                } else if (type === 'error') {
                    toastr.error(message, 'Error');
                }
            }
        });
    </script>
</body>

</html>
