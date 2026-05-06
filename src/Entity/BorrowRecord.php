<?php
declare(strict_types=1);
class BorrowRecord {
    private $id;
    private $student_id;
    private $book_id;
    private $borrow_date;
    private $return_date;

    public function __construct(int $id, int $student_id, int $book_id, string $borrow_date, string $return_date) {
        $this->id = $id;
        $this->student_id = $student_id;
        $this->book_id = $book_id;
        $this->borrow_date = $borrow_date;
        $this->return_date = $return_date;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getStudentId() {
        return $this->student_id;
    }

    public function getBookId() {
        return $this->book_id;
    }

    public function getBorrowDate() {
        return $this->borrow_date;
    }

    public function getReturnDate() {
        return $this->return_date;
    }
}
?>