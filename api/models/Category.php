<?php
class Category {
    private $conn;
    private $table = 'categories';

    public $id;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all categories
    public function read() {
        $query = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read a single category by ID
    public function readSingle() {
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Create a new category
    public function create() {
        $query = "INSERT INTO categories (category) VALUES (:category)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $this->category);
        return $stmt->execute();
    }

    // Update a category
    public function update() {
        $query = "UPDATE categories SET category = :category WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Delete a category
    public function delete() {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
