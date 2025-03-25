<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

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
                $this->handleGetRequest($params);
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

    private function handleGetRequest($params) {
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
    }

    private function getAllQuotes() {
        $stmt = $this->quote->read();
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes ?: ["message" => "No Quotes Found"]);
    }

    private function getQuoteById($id) {
        $this->quote->id = $id;
        $stmt = $this->quote->readSingle();
        $quote = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($quote ?: ["message" => "No Quotes Found"]);
    }

    private function getQuotesByAuthor($author_id) {
        $this->quote->author_id = $author_id;
        $stmt = $this->quote->readByAuthor();
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes ?: ["message" => "No Quotes Found"]);
    }

    private function getQuotesByCategory($category_id) {
        $this->quote->category_id = $category_id;
        $stmt = $this->quote->readByCategory();
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes ?: ["message" => "No Quotes Found"]);
    }

    private function getQuotesByAuthorAndCategory($author_id, $category_id) {
        $this->quote->author_id = $author_id;
        $this->quote->category_id = $category_id;
        $stmt = $this->quote->readByAuthorAndCategory();
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes ?: ["message" => "No Quotes Found"]);
    }

    private function createQuote() {
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }

        if (empty($data->quote) || empty($data->author_id) || empty($data->category_id)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

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

        $new_quote_id = $this->quote->create();

        if ($new_quote_id) {
            $this->quote->id = $new_quote_id;
            $new_quote = $this->quote->readSingle();
            $new_quote = $new_quote->fetch(PDO::FETCH_ASSOC);

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

        if (empty($data->quote) || empty($data->author_id) || empty($data->category_id) || empty($data->id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

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

        if ($updated_quote) {
            $this->quote->id = $data->id;
            $updated_quote = $this->quote->readSingle();
            $updated_quote = $updated_quote->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "id" => $updated_quote['id'],
                "quote" => $updated_quote['quote'],
                "author_id" => $updated_quote['author_id'],
                "category_id" => $updated_quote['category_id']
            ]);
        } else {
            echo json_encode(["message" => "Failed to update quote"]);
        }
    }

    private function deleteQuote() {
        $data = json_decode(file_get_contents("php://input"));

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(["message" => "Invalid JSON data"]);
            return;
        }

        if (empty($data->id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $this->quote->id = $data->id;

        if (!$this->quote->quoteExists()) {
            echo json_encode(["message" => "No Quotes Found"]);
            return;
        }

        $deleted_quote = $this->quote->delete();

        if ($deleted_quote) {
            echo json_encode(["id" => $data->id]);
        } else {
            echo json_encode(["message" => "Failed to delete quote"]);
        }
    }
}
?>