<?php

namespace UnitTests\Entity;

use App\Entity\Author;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    /**
     * @dataProvider getIdDataProvider
     *
     * @param int $id
     * @param string $authorName
     * @param int $expectedResult
     *
     * @return void
     */
    public function testGetId(int $id, string $authorName, int $expectedResult): void
    {
        $author = new Author($id, $authorName);
        $this->assertSame($expectedResult, $author->getId());
    }

    /**
     * @return array
     */
    public function getIdDataProvider(): array
    {
        return [
          [
              'id' => 1,
              'authorName' => 'Steve Jobs',
              'expectedResult' => 1,
          ]
        ];
    }

    /**
     * @dataProvider setIdDataProvider
     *
     * @param int $id
     * @param string $authorName
     * @param int $newId
     * @param int $expectedResult
     *
     * @return void
     */
    public function testSetId(int $id, string $authorName, int $newId, int $expectedResult): void
    {
        $author = new Author($id, $authorName);
        $author->setId($newId);
        $this->assertSame($expectedResult, $author->getId());
    }

    /**
     * @return array
     */
    public function setIdDataProvider(): array
    {
        return [
            [
                'id' => 2,
                'authorName' => 'Chinese Proverb',
                'newId' => 3,
                'expectedResult' => 3,
            ]
        ];
    }

    /**
     * @dataProvider getNameDataProvider
     *
     * @param int $id
     * @param string $authorName
     * @param string $expectedResult
     *
     * @return void
     */
    public function testGetName(int $id, string $authorName, string $expectedResult): void
    {
        $author = new Author($id, $authorName);
        $this->assertSame($expectedResult, $author->getName());
    }

    /**
     * @return array
     */
    public function getNameDataProvider(): array
    {
        return [
            [
                'id' => 1,
                'authorName' => 'Steve Jobs',
                'expectedResult' => 'Steve Jobs',
            ]
        ];
    }

    /**
     * @dataProvider setNameDataProvider
     *
     * @param int $id
     * @param string $authorName
     * @param string $newName
     * @param string $expectedResult
     *
     * @return void
     */
    public function testSetName(int $id, string $authorName, string $newName, string $expectedResult): void
    {
        $author = new Author($id, $authorName);
        $author->setName($newName);
        $this->assertSame($expectedResult, $author->getName());
    }

    /**
     * @return array
     */
    public function setNameDataProvider(): array
    {
        return [
            [
                'id' => 2,
                'authorName' => 'Chinese Proverb',
                'newName' => 'Marie Curie',
                'expectedResult' => 'Marie Curie',
            ]
        ];
    }

    /**
     * @dataProvider getQuotesDataProvider
     *
     * @param int $id
     * @param string $authorName
     * @param array $quotes
     * @param array $expectedResults
     *
     * @return void
     */
    public function testGetQuotes(int $id, string $authorName, array $quotes, array $expectedResults): void
    {
        $author = new Author($id, $authorName, $quotes);
        $this->assertSame($expectedResults, $author->getQuotes());
    }

    /**
     * @return array
     */
    public function getQuotesDataProvider(): array
    {
        return [
            [
                'id' => 1,
                'authorName' => 'Steve Jobs',
                'quotes' => ['Test quote.'],
                'expectedResult' => ['Test quote.'],
            ]
        ];
    }

    /**
     * @dataProvider setQuotesDataProvider
     *
     * @param int $id
     * @param string $authorName
     * @param array $quotes
     * @param array $expectedResults
     *
     * @return void
     */
    public function testQuotes(int $id, string $authorName, array $quotes, array $expectedResults): void
    {
        $author = new Author($id, $authorName);
        $author->setQuotes($quotes);
        $this->assertSame($expectedResults, $author->getQuotes());
    }

    /**
     * @return array
     */
    public function setQuotesDataProvider(): array
    {
        return [
            [
                'id' => 2,
                'authorName' => 'Chinese Proverb',
                'quotes' => ['Test new quote.', 'Test quote.'],
                'expectedResults' => ['Test new quote.', 'Test quote.'],
            ]
        ];
    }
}