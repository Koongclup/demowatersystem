<?php
require_once '../controllers/BillController.php';

if (isset($_POST['action'])) {
    $billController = new BillController();
    switch ($_POST['action']) {
        case 'create':
            $billController->create($_POST['user_id'], $_POST['usage_id'], $_POST['billing_date'], $_POST['total_amount'], $_POST['status']);
            break;
        case 'read':
            $billController->read($_POST['id']);
             break;
        case 'update':
            $billController->update($_POST['id'], $_POST['user_id'], $_POST['usage_id'], $_POST['billing_date'], $_POST['total_amount'], $_POST['status']);
            break;
        case 'delete':
            $billController->delete($_POST['id']);
            break;
        case 'fetchSigle':
            $billController->fetchSigle($_POST['id']);
            break;
        case 'fetchAll':
            $billController->fetchAll();
            break;
        case 'fetchBillData':
            $billController->fetchBillData();
            break;
        case 'readUser':
            $billController->fetchBillbyUserID($_POST['user_id']);
            break;
        case 'fetchUser':
            $billController->fetch($_POST['user_id']);
             break;
    }
}
