<?php

use dbeurive\Lexer\Lexer;
use dbeurive\Lexer\Token;

class LexerTest extends PHPUnit_Framework_TestCase
{
    const TYPE_SPECIAL       = 'SPECIAL';
    const TYPE_GLOBAL        = 'GLOBAL';
    const TYPE_LOCAL         = 'LOCAL';
    const TYPE_FUNCTION      = 'FUNCTION';
    const TYPE_OPERAOR_PLUS  = 'PLUS';
    const TYPE_OPERAOR_MINUS = 'MINUS';
    const TYPE_BRACKET       = 'BRACKET';
    const TYPE_WHITE         = 'WHITE';

    /** @var null|Lexer  */
    private $__lexer = null;

    public function setUp() {
        $tokens = array(
            array('/VG\\d+S/',       self::TYPE_SPECIAL),
            array('/VG\\d+/',        self::TYPE_GLOBAL),
            array('/VL\\d+/',        self::TYPE_LOCAL),
            array('/[a-z]{2,}/',     self::TYPE_FUNCTION),
            array('/\\+/',           self::TYPE_OPERAOR_PLUS),
            array('/\\-/',           self::TYPE_OPERAOR_MINUS),
            array('/(\\(|\\))/',     self::TYPE_BRACKET),
            array('/\\s+/',          self::TYPE_WHITE, function(array $m) { return null; })
        );
        $this->__lexer = new Lexer($tokens);
    }

    public function testOK() {

        $text = 'VG12VG12SVG13 VL12+(sin(VL10)) - VL10';
        $expexted = array(
            new Token('VG12',  self::TYPE_GLOBAL),
            new Token('VG12S', self::TYPE_SPECIAL),
            new Token('VG13',  self::TYPE_GLOBAL),
            new Token('VL12',  self::TYPE_LOCAL),
            new Token('+',     self::TYPE_OPERAOR_PLUS),
            new Token('(',     self::TYPE_BRACKET),
            new Token('sin',   self::TYPE_FUNCTION),
            new Token('(',     self::TYPE_BRACKET),
            new Token('VL10',  self::TYPE_LOCAL),
            new Token(')',     self::TYPE_BRACKET),
            new Token(')',     self::TYPE_BRACKET),
            new Token('-',     self::TYPE_OPERAOR_MINUS),
            new Token('VL10',  self::TYPE_LOCAL)
        );
        $tokens = $this->__lexer->lex($text);
        $this->assertEquals($expexted, $tokens);

        $text = '';
        $tokens = $this->__lexer->lex($text);
        $expexted = array();
        $this->assertEquals($expexted, $tokens);
    }

    public function testError() {

        // The specifications contain a regexp that matches an empty string.
        $this->expectException('\Exception');
        $specifications = array(
            array('/A+/',  'A'),
            array('/\s*/', 'BLANK')
        );
        $lexer = new Lexer($specifications);
        $lexer->lex('');
    }
}