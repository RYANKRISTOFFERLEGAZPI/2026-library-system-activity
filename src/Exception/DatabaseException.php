<?php
declare(strict_types=1);
namespace App\Exception;
use Exception;

class DatabaseException extends Exception {
    
    public function __construct($message = "Database error occurred", $code = 0) {
        parent::__construct($message, $code);
    }
}


?>