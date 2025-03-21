<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include the Quote model so the controller can use it
include_once __DIR__ . "/../models/Quote.php";

class QuoteController {
    private $db;
    private $quote;

    public function __construct($db) {
        $this->db = $db;
        $this->quote = new Quote($db);
    }

    public function handleRequest($method, $params) {
        switch ($method) {
            case 'GET':
                if (isset($params['id'])) {
                    $this->getQuoteById($params['id']);
                } elseif (isset($params['author_id']) && isset($params['category_id'])) {
                    $this->getQuotesByAuthorAndCategory($params['author_id'], $params['category_id']);
                } elseif (isset($params['author_id'])) {
                    $this->getQuotesByAuthor($params['author_id']);
                } elseif (isset($params['category_id'])) {
                    $this->getQuotesByCategory($params['category_id']);
                } else {
                    $this->getAllQuotes();
                }
                break;
            case 'POST':
                $this->createQuote();
                break;
            case 'PUT':
                $this->updateQuote();
                break;
            case 'DELETE':
                $this->deleteQuote();
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Method Not Allowed"]);
        }
    }

    private function getAllQuotes() {
        $stmt = $this->quote->read();
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes ?: ["message" => "No Quotes Found"]);
    }

    private function getQuoteById($id) {
        $this->quote->id = $id;
        $stmt = $this->quote->readSingle();
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: ["message" => "No Quotes Found"]);
    }

    private function createQuote() {
        $data = json_decode(file_get_contents("php://input"));

        // Check for invalid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            http_response_code(400);
            return;
        }

        if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $this->quote->quote = $data->quote;
            $this->quote->author_id = $data->author_id;
            $this->quote->category_id = $data->category_id;

            // Validate if author_id exists
            if (!$this->quote->authorExists()) {
                echo json_encode(["message" => "author_id Not Found"]);
                http_response_code(404);
                return;
            }

            // Validate if category_id exists
            if (!$this->quote->categoryExists()) {
                echo json_encode(["message" => "category_id Not Found"]);
                http_response_code(404);
                return;
            }

            $new_quote_id = $this->quote->create();

            if ($new_quote_id) {
                $new_quote = $this->quote->getQuoteById($new_quote_id);
                echo json_encode([
                    "id" => $new_quote['id'],
                    "quote" => $new_quote['quote'],
                    "author_id" => $new_quote['author_id'],
                    "category_id" => $new_quote['category_id']
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
}
?>
