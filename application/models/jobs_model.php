<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jobs_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // Load the database
        $this->load->library('form_validation'); // Load form validation library
    }

    // Method to retrieve jobs
    public function get_jobs()
    {
        $this->db->select('jobs.*, categories.name AS category');
        $this->db->from('jobs');
        $this->db->join('categories', 'jobs.category_id = categories.id', 'left');
        $query = $this->db->get(); // Get all jobs
        return $query->result(); // Return as an array of objects
    }

    public function get_all_categories()
    {
        $this->db->select('name');
        $this->db->from('categories');
        $query = $this->db->get();
        $result = $query->result_array();

        // Extract only the 'name' field into a flat array
        $categories = array_column($result, 'name');

        return $categories;
    }

    // Method to insert a new job
    public function insert_job($data)
    {
        $this->db->insert('jobs', $data);
        if ($this->db->affected_rows() > 0) {
            // Return the ID of the newly inserted job
            return $this->db->insert_id();
        } else {
            // Return false if insertion failed
            return false;
        }
    }

    // Get category ID by category name
    public function get_category_id_by_name($category_name)
    {
        $this->db->select('id');
        $this->db->from('categories');
        $this->db->where('name', $category_name);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return false; // Return false if category not found
    }

    public function get_job_by_id($id)
    {
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
