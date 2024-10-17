<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function validate_login($username, $password) {
        // Hash the password input to compare with the hashed password in the database
        $hashed_password = hash('sha256', $password); // Use the same hashing technique
    
        // Query to find the admin by username and password
        $this->db->where('username', $username);
        $this->db->where('password', $hashed_password); // Compare with the hashed password
        $query = $this->db->get('admin'); // Use your admin table name
    
        // Return the admin row if found, otherwise return false
        if ($query->num_rows() > 0) {
            return $query->row(); // Return the first matching row
        } else {
            return false; // No matching admin found
        }
    }
    
    
}

