<?php
class Author {
    private $conn;
    private $table = 'authors';

    public $id;
    public $author;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all authors
    public function read() {
        $query = "SELECT * FROM authors";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read a single author by ID
    public function readSingle() {
        $query = "SELECT * FROM authors WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Create a new author
    public function create() {
        $query = "INSERT INTO authors (author) VALUES (:author)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author', $this->author);

        if ($stmt->execute()) {
            return true;  // Return success flag
        }
        return false;  // Return failure flag
    }

    // Update an author
    public function update() {
        $query = "UPDATE authors SET author = :author WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Delete an author
    public function delete() {
        $query = "DELETE FROM authors WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Check if the author exists
    public function authorExists() {
        $query = "SELECT id FROM authors WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->rowCount() > 0; 
    }
}
?>
