<?php
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
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: ["message" => "Category Not Found"]);
    }

    private function createCategory() {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->category)) {
            $this->category->category = $data->category;

            echo json_encode($this->category->create() ? ["message" => "Category Created"] : ["message" => "Failed to Create Category"]);
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }

    private function updateCategory() {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->category)) {
            $this->category->id = $data->id;
            $this->category->category = $data->category;

            echo json_encode($this->category->update() ? ["message" => "Category Updated"] : ["message" => "Failed to Update Category"]);
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }

    private function deleteCategory() {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $this->category->id = $data->id;
            echo json_encode($this->category->delete() ? ["message" => "Category Deleted"] : ["message" => "Category Not Found"]);
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }
}
?>
