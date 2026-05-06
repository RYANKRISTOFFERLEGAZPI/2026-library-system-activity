<?php
declare(strict_types=1);
namespace App\Config;
use RuntimeException;

class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $name = "library_db";
    private $conn;

// Connection handling
    public function connect(){
        try{
            $this->conn = new \mysqli($this->host, $this->user, $this->pass, $this->name);
            if ($this->conn->connect_error) {
            throw new RuntimeException('Database connection failed: '.$this->conn->connect_error);
            }
        } catch (\Exception $e) {
            die("db error: " . $e->getMessage());
        }
    }

    private function __clone() {}
    
    public function __wakeup() {
        throw new RuntimeException("Cannot unserialize singleton");
    }

    public function getConnection() {
        return $this->conn;
    }

}
// @author — Ryan Kristoffer E. Legaspi
// @since — 2026-05-06
?>