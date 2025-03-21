<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include the Author model so the controller can use it
include_once __DIR__ . "/../models/Author.php";

class AuthorController {
    private $db;
    private $author;

    public function __construct($db) {
        $this->db = $db;
        $this->author = new Author($db);
    }

    public function handleRequest($method, $params) {
        switch ($method) {
            case 'GET':
                if (isset($params['id'])) {
                    $this->getAuthorById($params['id']);
                } else {
                    $this->getAllAuthors();
                }
                break;
            case 'POST':
                $this->createAuthor();
                break;
            case 'PUT':
                $this->updateAuthor();
                break;
            case 'DELETE':
                $this->deleteAuthor();
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Method Not Allowed"]);
        }
    }

    private function getAllAuthors() {
        $stmt = $this->author->read();
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($authors ?: ["message" => "No Authors Found"]);
    }

    private function getAuthorById($id) {
        $this->author->id = $id;
        $stmt = $this->author->readSingle();
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: ["message" => "author_id Not Found"]);
    }

    private function createAuthor() {
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }

        if (!empty($data->author)) {
            $this->author->author = $data->author;

            echo json_encode($this->author->create() ? ["message" => "Author Created"] : ["message" => "Failed to Create Author"]);
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }

    private function updateAuthor() {
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }

        if (!empty($data->id) && !empty($data->author)) {
            $this->author->id = $data->id;
            $this->author->author = $data->author;

            echo json_encode($this->author->update() ? ["message" => "Author Updated"] : ["message" => "Failed to Update Author"]);
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }

    private function deleteAuthor() {
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }

        if (!empty($data->id)) {
            $this->author->id = $data->id;

            // Check if the author exists
            if (!$this->author->authorExists()) {
                echo json_encode(["message" => "Author Not Found"]);
                return;
            }

            $deleted_author = $this->author->delete();

            echo json_encode($deleted_author ? ["message" => "Author Deleted", "id" => $data->id] : ["message" => "Failed to delete author"]);
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }
}
?>
