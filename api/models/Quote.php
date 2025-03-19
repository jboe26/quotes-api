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
    
    // Read a single quote by ID
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
      

    // Create a new quote
    public function create() {
        $query = "INSERT INTO quotes (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);

        return $stmt->execute();
    }

    // Update a quote
    public function update() {
        $query = "UPDATE quotes SET quote = :quote, author_id = :author_id, category_id = :category_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Delete a quote
    public function delete() {
        $query = "DELETE FROM quotes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

}
?>