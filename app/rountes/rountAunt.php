<?php 
require_once '../controllers/AuthController.php';

if (isset($_POST['action'])) {
    $auth = new AuthController();
    switch ($_POST['action']) {
        case 'register':
            $auth->register($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role']);
            break;
        case 'login':
            $auth->login($_POST['username'], $_POST['password']);
            break;
        case 'logout':
            $auth->logout();
            break;
    }
}
