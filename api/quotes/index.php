<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Determine HTTP method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Include necessary files
include_once(__DIR__ . '/../config/database.php');
include_once(__DIR__ . '/../controllers/QuoteController.php');


// Initialize Database Connection
$database = new Database();
$db = $database->getConnection();

// Create a Quote Controller instance
$controller = new QuoteController($db);

// Route request based on HTTP method
$controller->handleRequest($method, $_GET);
?>
