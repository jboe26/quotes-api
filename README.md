### ### Quotes API
### ### Joshua Boepple INF653 Back End Web Development - Midterm

### ### This RESTful API manages quotes, authors, and categories using PHP and PostgreSQL.

### ### Deployed Project: [\[My Deployed Project Link\]](https://inf653-midterm-project-5bc6.onrender.com/api)

### ### API Overview

### Quotes
GET /quotes - Fetch all quotes.

GET /quotes/{id} - Fetch a specific quote by ID.

POST /quotes - Add a new quote.

PUT /quotes - Update an existing quote.

DELETE /quotes - Remove a quote.

### Authors
GET /authors - Fetch all authors.

GET /authors/{id} - Fetch a specific author by ID.

POST /authors - Add a new author.

PUT /authors - Update an existing author.

DELETE /authors - Remove an author.

### Categories
GET /categories - Fetch all categories.

GET /categories/{id} - Fetch a specific category by ID.

POST /categories - Add a new category.

PUT /categories - Update an existing category.

DELETE /categories - Remove a category.

### Database
Powered by PostgreSQL and hosted on Render.

### Error Handling
API responses include clear HTTP status codes and JSON error messages for invalid inputs or requests.

### Why This Works
Organized endpoints under each resource (Quotes, Authors, Categories) make the structure clearer.

Grouping usage and error handling under Usage keeps it concise.

Consistent formatting ensures readability.
