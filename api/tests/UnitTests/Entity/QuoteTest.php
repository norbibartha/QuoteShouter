<?php

namespace UnitTests\Entity;

use App\Entity\Quote;
use PHPUnit\Framework\TestCase;

class QuoteTest extends TestCase
{
    /**
     * @dataProvider getIdDataProvider
     *
     * @param int $id
     * @param string $text
     * @param int $expectedResult
     *
     * @return void
     */
    public function testGetId(int $id, string $text, int $expectedResult): void
    {
        $quote = new Quote($id, $text);
        $this->assertSame($expectedResult, $quote->getId());
    }

    /**
     * @return array
     */
    public function getIdDataProvider(): array
    {
        return [
            [
                'id' => 1,
                'text' => 'Test quote.',
                'expectedResult' => 1,
            ]
        ];
    }

    /**
     * @dataProvider setIdDataProvider
     *
     * @param int $id
     * @param string $text
     * @param int $newId
     * @param int $expectedResult
     *
     * @return void
     */
    public function testSetId(int $id, string $text, int $newId, int $expectedResult): void
    {
        $quote = new Quote($id, $text);
        $quote->setId($newId);
        $this->assertSame($expectedResult, $quote->getId());
    }

    /**
     * @return array
     */
    public function setIdDataProvider(): array
    {
        return [
            [
                'id' => 1,
                'text' => 'Test quote.',
                'newId' => 2,
                'expectedResult' => 2,
            ]
        ];
    }

    /**
     * @dataProvider getTextDataProvider
     *
     * @param int $id
     * @param string $text
     * @param string $expectedResult
     *
     * @return void
     */
    public function testGetText(int $id, string $text, string $expectedResult): void
    {
        $quote = new Quote($id, $text);
        $this->assertSame($expectedResult, $quote->getText());
    }

    /**
     * @return array
     */
    public function getTextDataProvider(): array
    {
        return [
            [
                'id' => 1,
                'text' => 'Test quote.',
                'expectedResult' => 'Test quote.',
            ]
        ];
    }

    /**
     * @dataProvider setTextDataProvider
     *
     * @param int $id
     * @param string $text
     * @param string $newText
     * @param string $expectedResult
     *
     * @return void
     */
    public function testSetText(int $id, string $text, string $newText, string $expectedResult): void
    {
        $quote = new Quote($id, $text);
        $quote->setText($newText);
        $this->assertSame($expectedResult, $quote->getText());
    }

    /**
     * @return array
     */
    public function setTextDataProvider(): array
    {
        return [
            [
                'id' => 1,
                'text' => 'Test quote.',
                'newText' => 'Test quote2.',
                'expectedResult' => 'Test quote2.',
            ]
        ];
    }
}