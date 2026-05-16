<?php
class Database {
    private $host = "localhost";
    private $db_name = "monitoring_system";
    private $username = "root";
    private $password = "";

    public function getConnection() {
        try {
            return new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
        } catch (PDOException $e) {
            die("DB Error: " . $e->getMessage());
        }
    }
}