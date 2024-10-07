<?php
session_start();
require_once '../config/Database.php';
require_once '../models/Bill.php';

class BillController {
    private $conn;
    private $bill;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->bill = new Bill($this->conn);
    }

    // Create a new bill
    public function create($user_id, $usage_id, $billing_date, $total_amount, $status) {
        $this->bill->user_id = $user_id;
        $this->bill->usage_id = $usage_id;
        $this->bill->billing_date = $billing_date;
        $this->bill->total_amount = $total_amount;
        $this->bill->status = $status;

        if ($this->bill->create()) {
            echo json_encode(['status' => 'success', 'message' => 'Bill created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create bill']);
        }
    }

    public function read($id) {
        $data = $this->bill->fetchSigle($id);
        echo json_encode($data);
    }

    public function fetchSigle($id) {
        $data = $this->bill->fetchSigle($id);
        echo json_encode($data);
    }

    // Read all bills for a specific user
    public function fetchBillbyUserID($user_id) {
        $data = $this->bill->readByUserId($user_id);
        echo json_encode($data);
    }

    // Update a bill
    public function update($id, $user_id, $usage_id, $billing_date, $total_amount, $status) {
        $this->bill->id = $id;
        $this->bill->user_id = $user_id;
        $this->bill->usage_id = $usage_id;
        $this->bill->billing_date = $billing_date;
        $this->bill->total_amount = $total_amount;
        $this->bill->status = $status;

        if ($this->bill->update()) {
            echo json_encode(['status' => 'success', 'message' => 'Bill updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update bill']);
        }
    }

    // Delete a bill
    public function delete($id) {
        $this->bill->id = $id;

        if ($this->bill->delete()) {
            echo json_encode(['status' => 'success', 'message' => 'Bill deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete bill']);
        }
    }

    // Fetch bills for the logged-in user
    public function fetch($user_id) {
        if (isset($user_id)) { 
            $data = $this->bill->readByUserId($user_id);

            if ($data) {
                echo json_encode(['status' => 'success', 'data' => $data]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No bills found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        }
    }

    public function fetchAll() {
        echo json_encode($this->bill->readAll());
    }

    public function fetchBillData() {
        echo json_encode($this->bill->fetchBillData());
    }
}

