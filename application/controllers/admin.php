<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
        $this->load->helper('url');
        $this->load->library('form_validation');
    }

    public function login()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        // header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit; // Exit after responding to preflight request
        }

        // Retrieve raw JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true); // Decode the JSON input to an associative array

        // Extract username and password from the decoded data
        $username = isset($data['username']) ? $data['username'] : false;
        $password = isset($data['password']) ? $data['password'] : false;

        // echo json_encode(['username' => $username, 'password' => $password]);

        // Validate input
        if (empty($username) || empty($password)) {
            // Return error response with 400 Bad Request
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Username and password are required.']);
            return;
        }

        // Check the credentials using the model
        $this->load->model('admin_model');
        $admin = $this->admin_model->validate_login($username, $password);

        if ($admin) {
            // If the credentials are valid, generate a token and return it
            // $token = $this->generate_token($admin->id); // Implement your token generation logic
            // $this->session->set_userdata('is_logged_in', true);
            echo json_encode(['status' => 'success']);
        } else {
            // Invalid credentials
            // Return error response with 401 Unauthorized
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }
    }

    // private function isLoggedIn()
    // {
    //     if (!$this->session->userdata('is_logged_in')) {
    //         redirect('api/admin/login');
    //     }
    // }

}