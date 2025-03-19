<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // First, check if DATABASE_URL exists (for Render)
            $url = getenv('DATABASE_URL');

            if ($url) {
                // Parse the DATABASE_URL
                $db = parse_url($url);
                $host = $db['host'];
                $port = $db['port'];
                $db_name = ltrim($db['path'], '/');
                $username = $db['user'];
                $password = $db['pass'];
            } else {
                // Fallback for Local Testing (XAMPP)
                $host = "localhost"; // XAMPP MySQL or PostgreSQL Server
                $port = "5432"; // PostgreSQL default port
                $db_name = "quotesdb"; // Your local database name
                $username = "postgres"; // Your local DB username
                $password = "postgres"; // Your local DB password
            }

            // Establish PostgreSQL connection
            $this->conn = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db_name",
                $username,
                $password
            );

            // Set the PDO attributes
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
        } catch (Exception $exception) {
            error_log("General error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
