<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CORS {
    public function allow() {
        header('Access-Control-Allow-Origin: *'); // Allow all domains (use specific domains in production)
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Allow methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Allow headers

        // If it's a preflight request, just return a 200
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
