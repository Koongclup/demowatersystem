<?php 
require_once '../controllers/WaterUsageController.php';

if (isset($_POST['action'])) {
    $waterUsageController = new WaterUsageController();
    switch ($_POST['action']) {
        case 'create':
            $waterUsageController->create($_POST['user_id'], $_POST['usage_date'], $_POST['amount']);
            break;
        case 'read':
            $waterUsageController->read($_POST['user_id']);
            break;
        case 'update':
            $waterUsageController->update($_POST['id'], $_POST['user_id'], $_POST['usage_date'], $_POST['amount']);
            break;
        case 'delete':
            $waterUsageController->delete($_POST['id']);
            break;
        case 'fetchAll':
            $waterUsageController->fetchAll();
            break;
        case 'fetchUsageData':
            $waterUsageController->fetchUsageData();
        break;
        case 'fetchSingle':
            if (isset($_POST['id'])) {
                $waterUsageController->fetchSingle($_POST['id']);
            } else {
                echo json_encode(['message' => 'water ID is required.']);
            }
        break;
        
    }
}