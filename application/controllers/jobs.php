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

    public function create_job_test()
    {
        // Dummy data for testing

        // error_reporting(E_ALL);
        // ini_set('display_errors', 1);
        $_POST = [
            'company' => 'Data Insights Co.',
            'position' => 'Janitor',
            'type' => 'Full-T fdime',
            'location' => 'Los Angeles, CA',
            'description' => 'ewqeq',
            'how_to_apply' => 'ewew',
            'email' => 'jobs@jobeet.com', // This should fail validation
            'expires_on' => '2024-11-20T11:10:05.647Z',
            'category' => 'Accounting',
        ];


        // Normalize input (e.g., make 'type' lowercase)
        $_POST['type'] = strtolower(trim($_POST['type']));

        // Load the form validation library
        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('company', 'Company', 'required');
        $this->form_validation->set_rules('position', 'Position', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[full-time,part-time,freelance]');
        $this->form_validation->set_rules('location', 'Location', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('how_to_apply', 'How to Apply', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('expires_on', 'Expires On', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');

        // Run validation
        if ($this->form_validation->run() === FALSE) {
            // Capture validation errors
            $validation_errors = validation_errors();
            $response = array('error' => strip_tags($validation_errors));
            echo json_encode($response);
        } else {
            echo json_encode(['success' => 'Validation passed!']);
        }
    }

    // Method to post a new job
    // Method to handle job posting including validation
    public function create_job()
    {
        error_reporting(0);  // Turn off all error reporting
        ini_set('display_errors', 0);  // Don't display errors in the browser
        // Get the raw JSON input
        $json_data = file_get_contents('php://input');
        // Decode the JSON into a PHP array
        $data = json_decode($json_data, true);
        $data['status'] = 1;
        // Check if JSON data is received
        if (!$data) {
            echo json_encode(['error' => 'No data received']);
            return;
        }
        $this->load->library('form_validation');
        // if (isset($this->form_validation)) {
        //     echo "Form validation library loaded successfully!";
        // } else {
        //     echo "Failed to load form validation library.";
        // }
        // echo json_encode(['data_received' => $data]);
        // Trim all keys and values in the $data array to avoid extra spaces
        $data = array_map('trim', $data);

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // error_log(print_r($data, true)); // Log received data for debugging

        // // Set validation rules for required fields
        // echo "data passed";

        $_POST = array(
            'category' => $data['category'], // Category ID from DB
            'company' => $data['company'],
            'type' => $data['type'],
            'position' => $data['position'],
            'location' => $data['location'],
            'description' => $data['description'],
            'how_to_apply' => $data['how_to_apply'],
            'public' => isset($data['public']) && $data['public'] ? 1 : 0, // Boolean public field
            'email' => $data['email'],
            'token' => bin2hex(random_bytes(32)), // Generate a unique token
            'expires_on' => $data['expires_on'],
            'status' => $data['status'],
            'url' => $data['url'] // Optional field
        );

        $this->form_validation->set_rules('company', 'Company', 'required');
        $this->form_validation->set_rules('position', 'Position', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[full-time,part-time,freelance]');
        $this->form_validation->set_rules('location', 'Location', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('how_to_apply', 'How to Apply', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('expires_on', 'Expires On', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');
        // echo "Validation done";
        // Check if validation fails
        if ($this->form_validation->run() === FALSE) {
            // Return validation errors if they exist
            $response = array('error' => strip_tags(validation_errors())); // Strip tags to avoid HTML output
            echo json_encode($response);
            return;
        }

        // Retrieve the category ID by category name
        $category_name = $data['category'];
        $category_id = $this->jobs_model->get_category_id_by_name($category_name);
        if (!$category_id || $category_id == NULL) {
            // If category is not found, return an error
            $response = array('error' => 'Invalid category selected.');
            echo json_encode($response);
            return;
        }

        // Handle file upload (optional fields: logo, url)
        $config['upload_path'] = './uploads/logos/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB max
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $logo_url = NULL; // Only upload if the file is present

        if (!empty($_FILES['logo']['name'])) {
            if (!$this->upload->do_upload('logo')) {
                // Return error if the image upload fails
                $response = array('error' => strip_tags($this->upload->display_errors())); // Strip tags for clean output
                echo json_encode($response);
                return;
            } else {
                // Get uploaded file data
                $upload_data = $this->upload->data();
                $logo_url = base_url() . 'uploads/logos/' . $upload_data['file_name'];
            }
        }

        // Prepare job data
        $job_data = array(
            'category_id' => $category_id, // Category ID from DB
            'company' => $data['company'],
            'type' => $data['type'],
            'position' => $data['position'],
            'location' => $data['location'],
            'description' => $data['description'],
            'how_to_apply' => $data['how_to_apply'],
            'public' => isset($data['public']) && $data['public'] ? 1 : 0, // Boolean public field
            'email' => $data['email'],
            'token' => bin2hex(random_bytes(32)), // Generate a unique token
            'expires_on' => $data['expires_on'],
            'status' => $data['status'],
            'logo' => $logo_url, // Optional field
            'url' => $data['url'] // Optional field
        );

        // Insert job data into the database
        $inserted = $this->jobs_model->insert_job($job_data);

        // Check if insertion was successful
        if ($inserted) {
            // Send success response
            $response = array('success' => 'Job posted successfully', 'job_id' => $inserted, 'token' => $job_data['token']); // Include inserted job ID
            echo json_encode($response);
        } else {
            // Capture the error and last query for debugging
            $db_error = $this->db->error();
            $last_query = $this->db->last_query();

            // Send error response with detailed information
            $response = array(
                'error' => 'Failed to insert job.',
                'db_error' => $db_error['message'], // Database error message
                'last_query' => $last_query, // The last executed query
                'job_data' => $job_data // Include job data that failed
            );
            echo json_encode($response);
        }
    }


    public function get_categories()
    {
        $categories = $this->jobs_model->get_all_categories();
        echo json_encode(array($categories));
    }

    public function get_job($id)
    {

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
