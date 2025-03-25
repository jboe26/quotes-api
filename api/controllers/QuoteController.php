<?php

set_exception_handler(function ($e) {
    echo json_encode(["message" => "An error occurred", "error" => $e->getMessage()]);
    http_response_code(500); // Internal Server Error
});


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

    private function getQuotesByAuthor($author_id) {
        $this->quote->author_id = $author_id;
        $stmt = $this->quote->readByAuthor();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC) ?: ["message" => "No Quotes Found"]);
    }

    private function getQuotesByCategory($category_id) {
        $this->quote->category_id = $category_id;
        $stmt = $this->quote->readByCategory();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC) ?: ["message" => "No Quotes Found"]);
    }

    private function getQuotesByAuthorAndCategory($author_id, $category_id) {
        $this->quote->author_id = $author_id;
        $this->quote->category_id = $category_id;
        $stmt = $this->quote->readByAuthorAndCategory();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC) ?: ["message" => "No Quotes Found"]);
    }

    private function createQuote() {
        $data = json_decode(file_get_contents("php://input"));
    
        // Check if the JSON was valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }
    
        // Check for missing parameters
        if (empty($data->quote) || empty($data->author_id) || empty($data->category_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
    
        // Check if the author and category exist
        $this->quote->quote = $data->quote;
        $this->quote->author_id = $data->author_id;
        $this->quote->category_id = $data->category_id;
    
        if (!$this->quote->authorExists()) {
            echo json_encode(["message" => "author_id Not Found"]);
            return;
        }
    
        if (!$this->quote->categoryExists()) {
            echo json_encode(["message" => "category_id Not Found"]);
            return;
        }
    
        // Create the new quote
        $new_quote_id = $this->quote->create();
    
        if ($new_quote_id) {
            // Fetch the created quote details
            $new_quote = $this->quote->getQuoteById($new_quote_id);
            
            // Return the created quote with id, quote, author_id, and category_id
            echo json_encode([
                "id" => $new_quote['id'],
                "quote" => $new_quote['quote'],
                "author_id" => $new_quote['author_id'],
                "category_id" => $new_quote['category_id']
            ]);
        } else {
            echo json_encode(["message" => "Database Error"]);
        }
    }
    
    

    private function updateQuote() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }
    
        if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id) && !empty($data->id)) {
            $this->quote->quote = $data->quote;
            $this->quote->author_id = $data->author_id;
            $this->quote->category_id = $data->category_id;
            $this->quote->id = $data->id;
    
            if (!$this->quote->authorExists()) {
                echo json_encode(["message" => "author_id Not Found"]);
                return;
            }
    
            if (!$this->quote->categoryExists()) {
                echo json_encode(["message" => "category_id Not Found"]);
                return;
            }
    
            $updated_quote = $this->quote->update();
    
            if ($updated_quote && is_array($updated_quote)) {
                echo json_encode([
                    "id" => $updated_quote['id'],
                    "quote" => $updated_quote['quote'],
                    "author_id" => $updated_quote['author_id'],
                    "category_id" => $updated_quote['category_id']
                ]);
            } else {
                echo json_encode(["message" => "Failed to update quote"]);
            }
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }
    

    private function deleteQuote() {
        $data = json_decode(file_get_contents("php://input"));
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }
    
        if (!empty($data->id)) {
            $this->quote->id = $data->id;
    
            if (!$this->quote->quoteExists()) {
                echo json_encode(["message" => "No Quotes Found"]);
                return;
            }
    
            $deleted_quote = $this->quote->delete();
    
            if ($deleted_quote) {
                echo json_encode(["message" => "Quote Deleted", "id" => $data->id]);
            } else {
                echo json_encode(["message" => "Failed to delete quote"]);
            }
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }
    
}

?>
