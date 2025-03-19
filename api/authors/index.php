<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../controllers/AuthorController.php";

// Initialize Database Connection
$database = new Database();
$db = $database->getConnection();

// Create an Author Controller instance
$controller = new AuthorController($db);

// Route request based on HTTP method
$controller->handleRequest($method, $_GET);
?>
