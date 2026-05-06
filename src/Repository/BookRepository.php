<?php
declare(strict_types=1);
namespace App\Repository;

class BookRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addBook(string $title, string $author, int $year, string $genre) {
        $sql = "INSERT INTO books(title,author,year,genre) VALUES(?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssis", $title, $author, $year, $genre);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function getBook(int $id){
        $sql = "SELECT * FROM books WHERE book_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

}
?>