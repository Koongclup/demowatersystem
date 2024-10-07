<?php
session_start();
require_once '../config/Database.php';
require_once '../models/WaterUsage.php';

class WaterUsageController {
    private $conn;
    private $waterUsage;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->waterUsage = new WaterUsage($this->conn);
    }

    // Create a new water usage record
    public function create($user_id, $usage_date, $amount) {
        $this->waterUsage->user_id = $user_id;
        $this->waterUsage->usage_date = $usage_date;
        $this->waterUsage->amount = $amount;

        if ($this->waterUsage->create()) {
            echo json_encode(['status' => 'success', 'message' => 'Water usage record created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create water usage record']);
        }
    }

    // Read all water usage records for a specific user
    public function read($user_id) {
        $waters = $this->waterUsage->readByUserId($user_id);
        echo json_encode($waters);
    }

    // fetch data only billid by id 
    public function fetchSingle($id) {
        $waters = $this->waterUsage->fetchSingle($id);
        echo json_encode($waters);
    }
    // Update a water usage record
    public function update($id, $user_id, $usage_date, $amount) {
        $this->waterUsage->id = $id;
        $this->waterUsage->user_id = $user_id;
        $this->waterUsage->usage_date = $usage_date;
        $this->waterUsage->amount = $amount;

        if ($this->waterUsage->update()) {
            echo json_encode(['status' => 'success', 'message' => 'Water usage record updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update water usage record']);
        }
    }

    // Delete a water usage record
    public function delete($id) {
        $this->waterUsage->id = $id;

        if ($this->waterUsage->delete()) {
            echo json_encode(['status' => 'success', 'message' => 'Water usage record deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete water usage record']);
        }
    }

    public function fetchAll() {
        echo json_encode($this->waterUsage->readAll());
    }

    public function fetchUsageData() {
        echo json_encode($this->waterUsage->fetchUsageData());
    }
}


