<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include the Category model so the controller can use it
include_once __DIR__ . "/../models/Category.php";

class CategoryController {
    private $db;
    private $category;

    public function __construct($db) {
        $this->db = $db;
        $this->category = new Category($db);
    }

    public function handleRequest($method, $params) {
        switch ($method) {
            case 'GET':
                if (isset($params['id'])) {
                    $this->getCategoryById($params['id']);
                } else {
                    $this->getAllCategories();
                }
                break;
            case 'POST':
                $this->createCategory();
                break;
            case 'PUT':
                $this->updateCategory();
                break;
            case 'DELETE':
                $this->deleteCategory();
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Method Not Allowed"]);
        }
    }

    private function getAllCategories() {
        $stmt = $this->category->read();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categories ?: ["message" => "No Categories Found"]);
    }

    private function getCategoryById($id) {
        $this->category->id = $id;
        $stmt = $this->category->readSingle();
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: ["message" => "category_id Not Found"]);
    }

    private function createCategory() {
        $data = json_decode(file_get_contents("php://input"));

        // Check for invalid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            http_response_code(400);
            return;
        }

        if (!empty($data->category)) {
            $this->category->category = $data->category;

            $new_category_id = $this->category->create();

            if ($new_category_id) {
                $new_category = $this->category->getCategoryById($new_category_id);
                echo json_encode([
                    "id" => $new_category['id'],
                    "category" => $new_category['category']
                ]);
                http_response_code(201); // Created
            } else {
                echo json_encode(["message" => "Database Error"]);
                http_response_code(500); // Internal Server Error
            }
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
            http_response_code(400); // Bad Request
        }
    }

    private function updateCategory() {
        $data = json_decode(file_get_contents("php://input"));

        // Check for invalid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            http_response_code(400);
            return;
        }

        if (!empty($data->id) && !empty($data->category)) {
            $this->category->id = $data->id;
            $this->category->category = $data->category;

            echo json_encode($this->category->update() ? ["message" => "Category Updated"] : ["message" => "Failed to Update Category"]);
            http_response_code(200); // OK
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
            http_response_code(400); // Bad Request
        }
    }

    private function deleteCategory() {
        $data = json_decode(file_get_contents("php://input"));

        // Check for invalid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            http_response_code(400);
            return;
        }

        if (!empty($data->id)) {
            $this->category->id = $data->id;

            if (!$this->category->categoryExists()) {
                echo json_encode(["message" => "Category Not Found"]);
                http_response_code(404); // Not Found
                return;
            }

            $deleted_category = $this->category->delete();
            echo json_encode($deleted_category ? ["message" => "Category Deleted", "id" => $data->id] : ["message" => "Failed to delete category"]);
            http_response_code(200); // OK
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
            http_response_code(400); // Bad Request
        }
    }
}
?>
