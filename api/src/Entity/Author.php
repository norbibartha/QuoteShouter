<?php

namespace App\Entity;

class Author
{
    public function __construct(private int $id, private string $name, private array $quotes = [])
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getQuotes(): array
    {
        return $this->quotes;
    }

    /**
     * @param array $quotes
     */
    public function setQuotes(array $quotes): void
    {
        $this->quotes = $quotes;
    }
}