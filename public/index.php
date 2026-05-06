<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\View\BookList;
use App\Repository\BookRepository;
use App\View\ReportView;

$db = new Database();
$db->connect();
$conn = $db->getConnection();
$bookRepo = new BookRepository($conn);
$list = new BookList($conn);
$reports = new ReportView($conn);

if (isset($_GET['act'])) {
    if ($_GET['act'] == 'add') {
        $bookRepo->addBook($_POST['title'], $_POST['author'], $_POST['year'], $_POST['genre']);
    } elseif ($_GET['act'] == 'list') {
        $result = $list->listBooks();
    } elseif ($_GET['act'] == 'report') {
        $report = $reports->generateReport();
        echo "<h2>Library Report</h2>";
        echo "<p>Total Books: " . $report['total_books'] . "</p>";
        echo "<p>Borrowed: " . $report['total_borrowed'] . "</p>";
        echo "<p>Returned: " . $report['total_returned'] . "</p>";
        echo "<p>Total Fines Collected: $" . $report['total_fines'] . "</p>";
    }
}
?>



<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<?php 
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Author</th><th>Year</th><th>Genre</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['book_id'] . "</td><td>" . $row['title'] . "</td><td>" . $row['author'] . "</td><td>" . $row['year'] . "</td><td>" . $row['genre'] . "</td></tr>";
    }
    echo "</table>";
?>

</body>
</html>