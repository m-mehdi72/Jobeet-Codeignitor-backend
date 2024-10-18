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
    // Method to handle job posting including validation
    public function create_job()
    {
        // At the start of the create_job function
        // $response = array('step' => 'Received data', 'post_data' => $_POST, 'files' => $_FILES);
        // echo json_encode($response);
        // flush(); // Send the response immediately
        
        // echo json_encode('update => Beginning Validation Step');
        // flush();
        // Set validation rules for required fields
        $this->form_validation->set_rules('company', 'Company', 'required');
        $this->form_validation->set_rules('position', 'Position', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[full-time,part-time,freelance]');
        $this->form_validation->set_rules('location', 'Location', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('how_to_apply', 'How to Apply', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('expires_on', 'Expires On', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');

        // echo json_encode('Form Validation step run');
        // flush();

        // Check if validation fails
        if ($this->form_validation->run() === FALSE) {
            // Return validation errors if they exist
            $response = array('error' => strip_tags(validation_errors())); // Strip tags to avoid HTML output
            echo json_encode($response);
            return;
        }

        // Retrieve the category ID by category name
        $category_name = $this->input->post('category');
        $category_id = $this->jobs_model->get_category_id_by_name($category_name);
        if (!$category_id) {
            // If category is not found, return an error
            $response = array('error' => 'Invalid category selected.');
            echo json_encode($response);
            return;
        }

        // Send response after getting category ID
        // $response = array('step' => 'Category ID retrieved', 'category_id' => $category_id);
        // echo json_encode($response);
        // flush(); // Ensure the response is sent immediately

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
            'company' => $this->input->post('company'),
            'type' => $this->input->post('type'),
            'position' => $this->input->post('position'),
            'location' => $this->input->post('location'),
            'description' => $this->input->post('description'),
            'how_to_apply' => $this->input->post('how_to_apply'),
            'public' => $this->input->post('public') ? 1 : 0, // Boolean public field
            'email' => $this->input->post('email'),
            'token' => bin2hex(random_bytes(32)), // Generate a unique token
            'expires_on' => $this->input->post('expires_on'),
            'status' => $this->input->post('status'),
            'logo' => $logo_url, // Optional field
            'url' => $this->input->post('url') // Optional field
        );

        // Send response after preparing job data
        // $response = array('step' => 'Job data prepared', 'job_data' => $job_data);
        // echo json_encode($response);
        // flush(); // Ensure the response is sent immediately

        // echo json_encode('Beginning database insertion');
        // flush();
        // Insert job data into the database
        $inserted = $this->jobs_model->insert_job($job_data);
        // echo json_encode( $inserted);
        // flush();

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

    public function get_categories(){
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
