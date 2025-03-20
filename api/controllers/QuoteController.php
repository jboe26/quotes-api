<?php
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
    
        // Check if required fields are provided
        if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $this->quote->quote = $data->quote;
            $this->quote->author_id = $data->author_id;
            $this->quote->category_id = $data->category_id;
    
            // Validate if author_id exists
            if (!$this->quote->authorExists()) {
                echo json_encode(["message" => "author_id Not Found"]);
                return;
            }
    
            // Validate if category_id exists
            if (!$this->quote->categoryExists()) {
                echo json_encode(["message" => "category_id Not Found"]);
                return;
            }
    
            // Create the quote and get the new quote ID
            $new_quote_id = $this->quote->create();
    
            if ($new_quote_id) {
                // Retrieve the newly created quote
                $new_quote = $this->quote->getQuoteById($new_quote_id);
    
                echo json_encode($new_quote);
            } else {
                echo json_encode(["message" => "Database Error"]);
            }
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }
    

    private function updateQuote() {
        $data = json_decode(file_get_contents("php://input"));
    
        // Check if required fields are provided
        if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
            $this->quote->quote = $data->quote;
            $this->quote->author_id = $data->author_id;
            $this->quote->category_id = $data->category_id;
    
            // Validate if author_id exists
            if (!$this->quote->authorExists()) {
                echo json_encode(["message" => "author_id Not Found"]);
                return;
            }
    
            // Validate if category_id exists
            if (!$this->quote->categoryExists()) {
                echo json_encode(["message" => "category_id Not Found"]);
                return;
            }
    
            // Update the quote and get the updated quote data
            $updated_quote = $this->quote->update();
    
            if ($updated_quote) {
                echo json_encode($updated_quote); // Return the updated quote
            } else {
                echo json_encode(["message" => "Failed to update quote"]);
            }
        } else {
            echo json_encode(["message" => "Missing Required Parameters"]);
        }
    }

    private function deleteQuote() {
        $data = json_decode(file_get_contents("php://input"));
    
        // Check if required field 'id' is provided
        if (!empty($data->id)) {
            $this->quote->id = $data->id;
    
            // Validate if the quote exists
            if (!$this->quote->quoteExists()) {
                echo json_encode(["message" => "Quote Not Found"]);
                return;
            }
    
            // Delete the quote
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
