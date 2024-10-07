<?php
require_once '../config/Database.php';
require_once '../models/User.php';

class UserController
{
    private $db;
    private $user;

    public function __construct(){
        try {
            $this->db = (new Database())->getConnection();
            $this->user = new User($this->db);
        } catch (Exception $e) {
            echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
            exit;
        }
    }

    public function fetchAll(){
        echo json_encode($this->user->fetchAll());
    }

    public function fetchCount(){
        echo json_encode($this->user->fetchCount());
    }

    // Create a new user
    public function create($data){
        $this->user->username = $data['username'];
        // Hash the password before saving
        $this->user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $this->user->email = $data['email'];
        $this->user->role = $data['role'];
        $this->user->status = $data['status'];

        if ($this->user->create()) {
            echo json_encode(['message' => 'User created successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to create user.']);
        }
    }

    // Read a single user by ID
    public function read($id) {
        $this->user->id = $id;
        $user = $this->user->read($id);
        echo json_encode($user);
    }

    // Update an existing user
    public function update($data) {
        $this->user->id = $data['id'];
        $this->user->username = $data['username'];
        $this->user->email = $data['email'];
        $this->user->role = $data['role'];
        $this->user->status = $data['status'];

        if ($this->user->update()) {
            echo json_encode(['message' => 'User updated successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to update user.']);
        }
    }

    // Delete a user by ID
    public function delete($id) {
        $this->user->id = $id;
        if ($this->user->delete()) {
            echo json_encode(['message' => 'User deleted successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to delete user.']);
        }
    }
}

