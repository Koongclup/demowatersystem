<?php

class WaterUsage {
    private $conn;
    private $table = 'water_usage';

    public $id;
    public $user_id;
    public $usage_date;
    public $amount;

    public function __construct($db){
        $this->conn = $db;
    }

    // Create a new water usage record
    public function create() {
        $query = "INSERT INTO " . $this->table . " (user_id, usage_date, amount) VALUES (:user_id, :usage_date, :amount)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':usage_date', $this->usage_date);
        $stmt->bindParam(':amount', $this->amount);

        if ($stmt->execute()) {
            $updateQuery = "UPDATE water_usage wb
                            LEFT JOIN users u ON wb.user_id = u.id
                            SET wb.usagename = u.username";
            $updateStmt = $this->conn->prepare($updateQuery);
            return $updateStmt->execute();
        } else {
            return false;
        }
    }

    public function readByUserId($user_id){
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY usage_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch water usage records by user ID
    public function fetchSingle($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id ORDER BY usage_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Update a water usage record
    public function update(){
        $query = "UPDATE " . $this->table . " SET user_id=:user_id, usage_date=:usage_date, amount=:amount WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':usage_date', $this->usage_date);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            $updateQuery = "UPDATE water_usage wb
                            LEFT JOIN users u ON wb.user_id = u.id
                            SET wb.usagename = u.username";
            $updateStmt = $this->conn->prepare($updateQuery);
            return $updateStmt->execute();
        } else {
            return false;
        }
    }

    // Delete a water usage record
    public function delete(){
        $query = "DELETE FROM " . $this->table . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT * FROM water_usage";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchUsageData() {
        // Sample query to fetch usage data for chart
        $query = "SELECT usage_date, SUM(amount) as total_usage FROM water_usage GROUP BY usage_date";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
