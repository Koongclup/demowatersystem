<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4 mx-auto my-4">
                <h2 class="mb-4 text-center">Register</h2>
                <form id="registerForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required minlength="4" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required minlength="6" maxlength="20">
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary mx-2">Register</button>
                    <button class="btn btn-outline-secondary" id="switchToLogin">Switch to Login</button>
                </div>
                </form>
               
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                if (this.checkValidity()) {
                    const password = $('#password').val();
                    const confirmPassword = $('#confirmPassword').val();

                    if (password !== confirmPassword) {
                        Swal.fire('Passwords do not match.');
                        return;
                    }

                    $.ajax({
                        url: '../app/rountes/rountAunt.php',
                        type: 'POST',
                        data: $(this).serialize() + '&action=register',
                        dataType: 'json',
                        success: function(response) {
                            Swal.fire(response.message);
                            if (response.status === 'success') {
                                window.location.href = 'index.php?page=login';
                            }
                        }
                    });
                } else {
                    Swal.fire('Please fill in the form correctly.');
                }
            });

            $('#switchToLogin').on('click', function() {
                window.location.href = '../index.php';
            });
        });
    </script>
</body>

</html>
