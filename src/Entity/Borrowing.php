<?php

namespace App\Entity;

use App\Repository\BorrowingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowingRepository::class)]
class Borrowing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'student')]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'book')]
    private ?Book $book = null;

    #[ORM\Column]
    private ?\DateTime $date_borrowed = null;

    #[ORM\Column]
    private ?\DateTime $datereturned = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getDateBorrowed(): ?\DateTime
    {
        return $this->date_borrowed;
    }

    public function setDateBorrowed(\DateTime $date_borrowed): static
    {
        $this->date_borrowed = $date_borrowed;

        return $this;
    }

    public function getDatereturned(): ?\DateTime
    {
        return $this->datereturned;
    }

    public function setDatereturned(\DateTime $datereturned): static
    {
        $this->datereturned = $datereturned;

        return $this;
    }
}
