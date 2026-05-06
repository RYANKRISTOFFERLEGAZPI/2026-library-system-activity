<?php
declare(strict_types=1);
namespace App\View;

class BookList {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listBooks(){
        $sql = "SELECT * FROM books";
        $result = $this->conn->query($sql);
        $books = array();
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        return $books;
    }

    public function searchBooks(string $keyword){
        $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $keyword = "%$keyword%";
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        $books = array();
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        return $books;
    }

    
}
// @author — Ryan Kristoffer E. Legaspi
// @since — 2026-05-06
?>