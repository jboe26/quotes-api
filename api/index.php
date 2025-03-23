<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if (!empty($request_uri[1])) {
    switch ($request_uri[1]) {
        case 'quotes':
            include_once 'quotes/index.php';
            break;
        case 'authors':
            include_once 'authors/index.php';
            break;
        case 'categories':
            include_once 'categories/index.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(["message" => "Endpoint not found"]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "No endpoint specified."]);
}
?>
