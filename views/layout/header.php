<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm no-print">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?page=หน้าหลักรายการ"> Water Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-md-auto gap-2">
                <li class="nav-item rounded">
                    <a class="nav-link active" aria-current="page" href="index.php?page=หน้าหลักรายการ"><i class="bi bi-house-fill me-2"></i>หน้าหลัก</a>
                </li>
                <li class="nav-item dropdown rounded">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-list-ul me-2"></i> management</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="bills.php?page=bill"> <i class="bi bi-receipt-cutoff"></i> bill management</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="usermanagement.php?page=usermanagement"> <i class="bi bi-person"></i> users management</a></li>
                        <li><a class="dropdown-item" href="waterUsage.php?page=water"><i class="bi bi-droplet-half"></i> water management</a></li>


                    </ul>
                </li>
                <li class="nav-item rounded">
                    <a class="nav-link" href="#"><i class="bi bi-chat-square-dots me-2"></i>คู่มือ</a>
                </li>
                <li class="nav-item dropdown rounded">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-fill me-2"></i>Profile</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="listuserall.php?page=Account">Account</a></li>
                        <li><a class="dropdown-item" href="setting.php?page=แก้ไขระบบ">Setting</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" id="logout" href="#">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#logout').on('click', function() {
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
</script>