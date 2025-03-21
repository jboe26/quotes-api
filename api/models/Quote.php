<?php
class Quote {
    private $conn;
    private $table = 'quotes';

    public $id;
    public $quote;
    public $author_id;
    public $category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT q.id, q.quote, a.author, c.category
                  FROM quotes q
                  JOIN authors a ON q.author_id = a.id
                  JOIN categories c ON q.category_id = c.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readSingle() {
        $query = "SELECT quotes.id, quotes.quote, authors.author, categories.category
                  FROM quotes 
                  JOIN authors ON quotes.author_id = authors.id
                  JOIN categories ON quotes.category_id = categories.id
                  WHERE quotes.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function readByAuthor() {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM quotes q
                  JOIN authors a ON q.author_id = a.id
                  JOIN categories c ON q.category_id = c.id
                  WHERE q.author_id = :author_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function readByCategory() {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM quotes q
                  JOIN authors a ON q.author_id = a.id
                  JOIN categories c ON q.category_id = c.id
                  WHERE q.category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function readByAuthorAndCategory() {
        $query = "SELECT q.id, q.quote, a.author, c.category 
                  FROM quotes q
                  JOIN authors a ON q.author_id = a.id
                  JOIN categories c ON q.category_id = c.id
                  WHERE q.author_id = :author_id AND q.category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        if (empty($this->quote) || empty($this->author_id) || empty($this->category_id)) {
            return false; // missing parameters
        }
        $query = "INSERT INTO quotes (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update() {
        if (empty($this->id) || empty($this->quote) || empty($this->author_id) || empty($this->category_id)) {
            return false; // missing parameters
        }

        $query = "UPDATE quotes SET quote = :quote, author_id = :author_id, category_id = :category_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        if (empty($this->id)) {
            return false; // missing id
        }
        $query = "DELETE FROM quotes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Additional function to check if a quote exists based on ID
    public function quoteExists() {
        $query = "SELECT id FROM quotes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function authorExists() {
        $query = "SELECT id FROM authors WHERE id = :author_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function categoryExists() {
        $query = "SELECT id FROM categories WHERE id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
