<?php
declare(strict_types=1);
namespace LegacyLibrarySystem;


class LibrarySystem{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $name = "library_db";
    private $conn;
    private $fine_rate = 5;


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

// CRUD operations
    public function addBook(string $title, string $author, int $year, string $genre){
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

    public function borrowBook(int $student_id, int $book_id, int $days){
        $due = date('Y-m-d', strtotime('+' . $days . ' days'));
        $sql = "INSERT INTO borrow_records(student_id,book_id,borrow_date,due_date,status) VALUES(?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("iiis", $student_id, $book_id, date('Y-m-d'), $due, 'borrowed');
        $stmt->execute();

        return true;
    }

    public function returnBook(int $recordId){
        $sql = "SELECT * FROM borrow_records WHERE record_id=?";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bind_param("i", $recordId);
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

        $sql2 = "UPDATE borrow_records SET return_date='" . date('Y-m-d') . "', fine_amount=" . $fine . ", status='returned' WHERE record_id=" . $recordId;
        $this->conn->query($sql2);

        return $fine;
    }

    public function listBooks(){
        $sql = "SELECT * FROM books";
        $result = $this->conn->query($sql);
        echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Author</th><th>Year</th><th>Genre</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['book_id'] . "</td><td>" . $row['title'] . "</td><td>" . $row['author'] . "</td><td>" . $row['year'] . "</td><td>" . $row['genre'] . "</td></tr>";
        }
        echo "</table>";
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

    public function getOverdueBooks(){
        $sql = "SELECT br.*, b.title, s.name FROM borrow_records br JOIN books b ON br.book_id=b.book_id JOIN students s ON br.student_id=s.student_id WHERE br.due_date<'" . date('Y-m-d') . "' AND br.status='borrowed'";
        $result = $this->conn->query($sql);
        $list = array();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }

    public function generateReport(){
        $totalBooks = $this->conn->query("SELECT COUNT(*) as c FROM books")->fetch_assoc()['c'];
        $totalBorrowed = $this->conn->query("SELECT COUNT(*) as c FROM borrow_records WHERE status='borrowed'")->fetch_assoc()['c'];
        $totalReturned = $this->conn->query("SELECT COUNT(*) as c FROM borrow_records WHERE status='returned'")->fetch_assoc()['c'];
        $totalFines = $this->conn->query("SELECT SUM(fine_amount) as s FROM borrow_records WHERE fine_amount>0")->fetch_assoc()['s'];
        return [
            'total_books' => $totalBooks,
            'total_borrowed' => $totalBorrowed,
            'total_returned' => $totalReturned,
            'total_fines' => $totalFines
        ];
    }
}


// Usage example
$lib = new LibrarySystem();
$lib->connect();
if (isset($_GET['act'])) {
    if ($_GET['act'] == 'add') {
        $lib->addBook($_POST['title'], $_POST['author'], $_POST['year'], $_POST['genre']);
    } elseif ($_GET['act'] == 'list') {
        $lib->listBooks();
    } elseif ($_GET['act'] == 'report') {
        $report = $lib->generateReport();
        echo "<h2>Library Report</h2>";
        echo "<p>Total Books: " . $report['total_books'] . "</p>";
        echo "<p>Borrowed: " . $report['total_borrowed'] . "</p>";
        echo "<p>Returned: " . $report['total_returned'] . "</p>";
        echo "<p>Total Fines Collected: $" . $report['total_fines'] . "</p>";
    }
}
?>