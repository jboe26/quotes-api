<?php
// Set headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Output API homepage
echo json_encode([
    "message" => "Welcome to the Quotes REST API!",
    "endpoints" => [
        "Quotes" => [
            "GET /api/quotes/" => "Retrieve all quotes",
            "GET /api/quotes/?id={quote_id}" => "Retrieve a quote by ID",
            "GET /api/quotes/?author_id={author_id}" => "Retrieve quotes by author ID",
            "GET /api/quotes/?category_id={category_id}" => "Retrieve quotes by category ID",
            "GET /api/quotes/?random=true" => "Retrieve a random quote",
            "POST /api/quotes/" => "Create a new quote",
            "PUT /api/quotes/" => "Update an existing quote",
            "DELETE /api/quotes/?id={quote_id}" => "Delete a quote by ID"
        ],
        "Authors" => [
            "GET /api/authors/" => "Retrieve all authors",
            "POST /api/authors/" => "Create a new author",
            "PUT /api/authors/" => "Update an author",
            "DELETE /api/authors/?id={author_id}" => "Delete an author by ID"
        ],
        "Categories" => [
            "GET /api/categories/" => "Retrieve all categories",
            "POST /api/categories/" => "Create a new category",
            "PUT /api/categories/" => "Update a category",
            "DELETE /api/categories/?id={category_id}" => "Delete a category by ID"
        ]
    ]
], JSON_PRETTY_PRINT);
?>