<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Load the database
    }

    // Method to retrieve jobs
    public function get_jobs() {
        $this->db->select('jobs.*, categories.name AS category');
        $this->db->from('jobs');
        $this->db->join('categories', 'jobs.category_id = categories.id', 'left');
        $query = $this->db->get(); // Get all jobs
        return $query->result(); // Return as an array of objects
    }

    // Method to insert a new job
    public function insert_job($data) {
        return $this->db->insert('jobs', $data); // Insert job data into the database
    }

    public function get_job_by_id($id) {
        $this->db->select('jobs.*, categories.name AS category');
        $this->db->from('jobs');
        $this->db->join('categories', 'jobs.category_id = categories.id', 'left');
        $this->db->where('jobs.id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row_array(); // Return single job as an associative array
        }
        return false; // No job found
    }
    

}
