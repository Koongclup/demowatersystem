<?php
class Bill
{
    private $conn;
    private $table_name = 'water_bills';

    public $id;
    public $user_id;
    public $usage_id;
    public $billing_date;
    public $total_amount;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

     // Read all bills
     public function readAll(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new bill
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, usage_id=:usage_id, billing_date=:billing_date, total_amount=:total_amount, status=:status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':usage_id', $this->usage_id);
        $stmt->bindParam(':billing_date', $this->billing_date);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':status', $this->status);
        if ($stmt->execute()) {
            $updateQuery = "UPDATE water_bills wb
                            LEFT JOIN users u ON wb.usage_id = u.id
                            SET wb.usagename = u.username
                            WHERE wb.usage_id = :usage_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':usage_id', $this->usage_id);
            return $updateStmt->execute();
        } else {
            return false;
        }
    }
    
    // Read bills by usage ID
    public function fetchSigle($id){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update a bill record
    public function update(){
        $query = "UPDATE " . $this->table_name . " SET user_id=:usage_id, usage_id=:usage_id, billing_date=:billing_date, total_amount=:total_amount, status=:status WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':usage_id', $this->usage_id);
        $stmt->bindParam(':billing_date', $this->billing_date);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
         // Execute the insert query
         if ($stmt->execute()) {
            // After successful insert, update the usagename field
            $updateQuery = "UPDATE water_bills wb
                            LEFT JOIN users u ON wb.usage_id = u.id
                            SET wb.usagename = u.username
                            WHERE wb.usage_id = :usage_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':usage_id', $this->usage_id);
            return $updateStmt->execute();
        } else {
            return false;
        }
    
    }

    // Delete a bill record
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function fetchBillData() {
        // Sample query to fetch bill data for chart
        $query = "SELECT billing_date, SUM(total_amount) as total_billed FROM water_bills GROUP BY billing_date";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     // Read bills by user ID
    public function readByUserId($user_id){
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
