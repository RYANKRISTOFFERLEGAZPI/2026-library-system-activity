<?php
declare(strict_types=1);
class BorrowRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addBorrowRecord(int $student_id, int $book_id, string $borrow_date, string $return_date) {
        $sql = "INSERT INTO borrow_records(student_id, book_id, borrow_date, return_date) VALUES(?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiis", $student_id, $book_id, $borrow_date, $return_date);
        $stmt->execute();
        return $this->conn->insert_id;
    }
}
?>