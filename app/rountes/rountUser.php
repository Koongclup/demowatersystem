<?php
require_once '../controllers/UserController.php';

if (isset($_POST['action'])) {
    $userController = new UserController();

    switch ($_POST['action']) {

        case 'create':
            $userController->create($_POST);
            break;

        case 'read':
            if (isset($_POST['id'])) {
                $userController->read($_POST['id']);
            } else {
                echo json_encode(['message' => 'User ID is required.']);
            }
            break;

        case 'update':
            if (isset($_POST['id'])) {
                $userController->update($_POST);
            } else {
                echo json_encode(['message' => 'User ID is required for updating.']);
            }
            break;

        case 'delete':
            if (isset($_POST['id'])) {
                $userController->delete($_POST['id']);
            } else {
                echo json_encode(['message' => 'User ID is required for deletion.']);
            }
            break;

        case 'fetchAll':
            $userController->fetchAll();

            break;

        case 'fetchCount':
            $userController->fetchCount();
            break;

        case 'fetchUsers':
            $userController->fetchAll();
            break;


        default:
            echo json_encode(['message' => 'Invalid action specified.']);
            break;
    }
} else {
    echo json_encode(['message' => 'No action specified.']);
}
