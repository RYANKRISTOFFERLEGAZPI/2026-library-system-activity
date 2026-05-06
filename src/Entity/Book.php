<?php
declare(strict_types=1);
class Book {
    private $id;
    private $title;
    private $author;
    private $year;
    private $genre;

    public function __construct(int $id, string $title, string $author, int $year, string $genre) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->genre = $genre;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getYear() {
        return $this->year;
    }

    public function getGenre() {
        return $this->genre;
    }
}
// @author — Ryan Kristoffer E. Legaspi
// @since — 2026-05-06
?>
