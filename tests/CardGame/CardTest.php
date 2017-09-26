<?php

namespace MauMau\CardGame;

use PHPUnit\Framework\TestCase;
use Mockery;

class CardTest extends TestCase
{
    protected $card;
    protected $rules;

    public function setUp()
    {
        $this->rules = $this->createMock(AbstractRules::class);
    }

    public function testValidCard()
    {
        $this->rules->method('validateCard')->willReturn(true);
        $this->rules->method('cardColor')->willReturn('red');
        $this->rules->method('cardValue')->willReturn(11);

        $this->card = new Card('hearts', 'J', $this->rules);

        $this->assertEquals('hearts', $this->card->getSuit());
        $this->assertEquals('J', $this->card->getName());
        $this->assertEquals('red', $this->card->getColor());
        $this->assertEquals(11, $this->card->getValue());
        $this->assertEquals(false, $this->card->isJoker());
    }

    public function testInvalidCard()
    {
        $this->rules->method('validateCard')->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid card');
        $this->card = new Card('error', 'error', $this->rules);
    }

    public function testJoker()
    {
        $this->rules->method('validateCard')->willReturn(true);
        $this->rules->method('cardColor')->willReturn('any');
        $this->rules->method('cardValue')->willReturn(0);

        $this->card = new Card('', 'Joker', $this->rules);

        $this->assertEquals('Joker', $this->card->getName());
        $this->assertEquals('any', $this->card->getColor());
        $this->assertEquals(0, $this->card->getValue());
        $this->assertEquals(true, $this->card->isJoker());
    }

    public function cardProvider()
    {
        return [
            ['', 'Joker', '', 0, 'Joker'],
            ['hearts', 'J', 'red', 11, "\u{2665}J"],
            ['clubs', '3', 'black', 3, "\u{2663}3"],
            ['spades', 'Q', 'black', 12, "\u{2660}Q"],
            ['diamonds', 'A', 'red', 13, "\u{2666}A"],
            ['error', 'A', 'red', 13, "invalid card"]
        ];
    }

    /**
     * @param string $suit
     * @param string $name
     * @param string $color
     * @param int $value
     * @param string $expected
     * @dataProvider cardProvider
     */
    public function testToString(string $suit, string $name, string $color, int $value, string $expected)
    {
        $this->rules->method('validateCard')->willReturn(true);
        $this->rules->method('cardColor')->willReturn($color);
        $this->rules->method('cardValue')->willReturn($value);

        $this->card = new Card($suit, $name, $this->rules);

        $this->assertEquals($expected, $this->card);
    }
}
