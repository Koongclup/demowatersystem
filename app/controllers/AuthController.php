<?php
// app/controllers/AuthController.php

require_once '../config/Database.php';
require_once '../models/User.php';

session_start();

class AuthController {
    private $conn;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->user = new User($this->conn);
    }

    // Handle user registration
    public function register($username, $password, $email, $role) {
        $this->user->username = $username;
        $this->user->password = $password;
        $this->user->email = $email;
        $this->user->role = $role;

        if ($this->user->register()) {
            echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
        }
    }

    // Handle user login
    public function login($username, $password) {
        $this->user->username = $username;
        $this->user->password = $password;

        $user = $this->user->login();
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            echo json_encode(['status' => 'success', 'message' => 'Login successful', 'role' => $user['role']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }
    }

    // Logout
    public function logout() {
        session_destroy();
        header("Location: ../login.php");
    }
}

