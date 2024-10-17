<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        header("Content-Type: application/json");
    }

    public function test_data()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Define some dummy data
        $data = array(
            'status' => 'success',
            'message' => 'API is working!',
            'data' => array(
                'name' => 'CodeIgniter',
                'version' => '2.2.6',
                'framework' => 'CodeIgniter 2',
                'purpose' => 'Testing API'
            )
        );

        // Output the data as JSON
        echo json_encode($data);
    }
}
