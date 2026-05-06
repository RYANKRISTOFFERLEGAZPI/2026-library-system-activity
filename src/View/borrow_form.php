<?php
declare(strict_types=1);
class BorrowList {
    private $conn;
    private $fine_rate = 5;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function borrowBook(int $student_id, int $book_id, int $days){
        $due = date('Y-m-d', strtotime('+' . $days . ' days'));
        $sql = "INSERT INTO borrow_records(student_id,book_id,borrow_date,due_date,status) VALUES(?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("iiis", $student_id, $book_id, date('Y-m-d'), $due, 'borrowed');
        $stmt->execute();

        return true;
    }

    public function returnBook(int $returnId){
        $sql = "SELECT * FROM borrow_records WHERE record_id=?";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bind_param("i", $returnId);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $due = strtotime($r['due_date']);
        $today = strtotime(date('Y-m-d'));
        $secondsPerDay = 60 * 60 * 24;
        $diff = ($today - $due) / $secondsPerDay;
        $fine = 0;


        if ($diff > 0) {
            $fine = $diff * $this->fine_rate;
        }

        $sql2 = "UPDATE borrow_records SET return_date='" . date('Y-m-d') . "', fine_amount=" . $fine . ", status='returned' WHERE record_id=" . $returnId;
        $this->conn->query($sql2);

        return $fine;
    }

    public function getOverdueBooks(){
        $sql = "SELECT br.*, books.title, students.name FROM borrow_records br JOIN books b ON borrowed.book_id=books.book_id JOIN students s ON borrowed.student_id=students.student_id WHERE borrowed.due_date<'" . date('Y-m-d') . "' AND br.status='borrowed'";
        $result = $this->conn->query($sql);
        $list = array();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }
}

?>