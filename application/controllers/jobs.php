<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jobs extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('jobs_model'); // Load the jobs model
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        header('Content-Type: application/json'); // Set header for JSON responses
    }

    // Method to retrieve jobs
    public function index()
    {
        $jobs = $this->jobs_model->get_jobs(); // Get jobs from the model
        echo json_encode($jobs); // Return jobs as JSON
    }

    // Method to post a new job
    public function post()
    {
        $data = json_decode(file_get_contents('php://input'), true); // Get JSON input

        // Validate input data (you can enhance this with more checks)
        if (isset($data['position']) && isset($data['company'])) {
            $result = $this->jobs_model->insert_job($data);
            echo json_encode(['success' => true, 'message' => 'Job posted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
        }
    }

    public function get_job($id) {
        
        // Get job details
        $job = $this->jobs_model->get_job_by_id($id);
    
        // Check if job exists
        if ($job) {
            echo json_encode(array('status' => 'success', 'data' => $job));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Job not found'));
        }
    }

}
