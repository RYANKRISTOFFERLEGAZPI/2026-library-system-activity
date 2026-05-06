<?php
declare(strict_types=1);
namespace App\Service;
use App\Entity\Book;
use App\Entity\Student;

class LibraryService {
    private $conn;
    private $DailyFinerate;

    public function __construct( $db) {
        $this->conn = $db->connect();
        $this->DailyFinerate = 5;
    }

    public function returnBook($recordId){
        $sql = "SELECT * FROM borrow_records WHERE record_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $recordId);
        $stmt->execute();
        $record = $stmt->get_result()->fetch_assoc();
        $due = strtotime($record['due_date']);
        $today = strtotime(date('Y-m-d'));
        $secondsPerDay = 60 * 60 * 24;
        $diff = ($today - $due) / $secondsPerDay;
        $fine = 0;
        if ($diff > 0) {
            $fine = $diff * $this->DailyFinerate;
        }
        $sql2 = "UPDATE borrow_records SET return_date='" . date('Y-m-d') . "', fine_amount=" . $fine . ", status='returned' WHERE record_id=" . $recordId;
        $this->conn->query($sql2);
        return $fine;
    }
}
?>