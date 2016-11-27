# Introduction

This repository contains the implementation of a basic lexer.

A lexer explodes a given string into a list of tokens.

# Installation

From the command line:

    composer require dbeurive\lexer

If you want to include this package to your project, then edit your file `composer.json` and add the following entry:

    "require": {
        "dbeurive/lexer": "*"
    }

# Synopsis

```php
    $varProcessor = function(array $inMatches) {
        $name = strtolower($inMatches[2]);
        switch (strtoupper($inMatches[1])) {
            case 'L': return 'LOCAL_'  . $name;
            case 'G': return 'GLOBAL_' . $name;
        }
        throw new \Exception("Impossible error!");
    };
   
    $tokens = array(
        array('/[0-9]+/',                'numeric'),
        array('/\\$([lg])([a-z0-9]+)/i', 'variable', $varProcessor),
        array('/[a-z]{2,}/i',            'function'),
        array('/(\\+|\\-|\\*|\\/)/',     'operator'),
        array('/\\(/',                   'open_bracket'),
        array('/\\)/',                   'close_bracket'),
        array('/(\\s+|\\r?\\n)/',        'blank', function(array $m) { return null; })
    );
    
    try {
        $lexer = new Lexer($tokens);
        $text = '$gConstant1 + sin($lCoef1) / cos($lcoef2) * $gTemp - tan(21)';
        $tokens = $lexer->lex($_text);
    } catch (\Exception $e) {
        print "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    /** @var Token $_token */
    foreach ($tokens as $_token) {
        printf("%s %s\n", $_token->type, $_token->value);
    }
```

# Specifications

## Description

The lexer is configured by a list of tokens specifications:

    array(
        <token specification>,
        <token specification>,
        ...
    )

Each token specification is an array that contains 2 or 3 elements.

    <token specification> = array(<regexp>, <type>, [<transformer callback>])

* The first element is a regular expression that describes the token.
* The second element is a name that identifies the type of the token.
* The optional third element is a function that is applied to the token's value before it is returned.

> **WARNING**
>
> Make sure to double all characters "`\`" within the regular expressions that define the tokens.
> That is: `'/\s/'` becomes `'/\\s/'.`

The signature of the optional third element (`<transformer callback>`) must be:

    mixed|null function(array $inMatches)

The array (`$inMatches`) passed to the function comes from the processing of the regular expression that describes the token.

* The first element of the array (`$inMatches[0]`) contains the text that matches the full pattern.
* The second element of the array (`$inMatches[1]`) contains the text that matched the first captured parenthesized subpattern.
* The third element of the array (`$inMatches[2]`) contains the text that matched the second captured parenthesized subpattern.
* ... and so on.

> See the description for the PHP function `preg_match()`.

* If the function returns the value `null`, then the detected token is "ignored".
  That is: it will not be inserted into the list of extracted tokens.
* If the function returns a non-null value, then the token is inserted in the list of detected tokens.
  The value of the inserted token will be the value returned by the function (`<transformer callback>`).

## Very important note

Be aware that the order of declarations of the tokens is important.

The [example 2](examples/example2.php) illustrates this point.

```php
    use dbeurive\Lexer\Lexer;
    use dbeurive\Lexer\Token;
    
    $text = 'AAAA AA';
    
    // ---------------------------------------------------------
    // TEST 1
    // ---------------------------------------------------------
    
    $specifications = array(
        array('/AA/',                    'type A2'),
        array('/A/',                     'type A1'),
        array('/(\\s+|\\r?\\n)/',        'blank', function(array $m) { return null; })
    );
    
    try {
        $lexer = new Lexer($specifications);
        $tokens = $lexer->lex($text);
    } catch (\Exception $e) {
        print "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    print "Test1: $text\n\n";
    dumpToken($tokens);
    print "\n";
    
    // ---------------------------------------------------------
    // TEST 2
    // ---------------------------------------------------------
    
    $specifications = array(
        array('/A/',                     'type A1'),
        array('/AA/',                    'type A2'),
        array('/(\\s+|\\r?\\n)/',        'blank', function(array $m) { return null; })
    );
    
    try {
        $lexer = new Lexer($specifications);
        $tokens = $lexer->lex($text);
    } catch (\Exception $e) {
        print "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    print "Test2: $text\n\n";
    dumpToken($tokens);
    
    exit(0);
    
    function dumpToken(array $inTokens) {
        $max = 0;
    
        /** @var Token $_token */
        foreach ($inTokens as $_token) {
            $max = strlen($_token->type) > $max ? strlen($_token->type) : $max;
        }
    
        /** @var Token $_token */
        foreach ($inTokens as $_token) {
            printf("%${max}s %s\n", $_token->type, $_token->value);
        }
    }
```

The result is:

    Test1: AAAA AA
    
    type A2 AA
    type A2 AA
    type A2 AA
    
    Test2: AAAA AA
    
    type A1 A
    type A1 A
    type A1 A
    type A1 A
    type A1 A
    type A1 A

# API

## Constructor

```php
    /**
     * Lexer constructor.
     * @param array $inSpecifications This array represents the tokens specifications.
     *        Each element of this array is an array that specifies a token.
     *        It contains 2 or 3 elements.
     *        - First element: a regular expression that describes the token.
     *        - Second element: the name of the token.
     *        - Third element: an optional callback function.
     *          The signature of this function must be:
     *          null|string function(array $inMatches)
     * @throws \Exception
     */
    public function __construct(array $inSpecifications)
```

Please see the section "specifications" for a detailed description of the parameter.

## lex()

```php
    /**
     * Explode a given string into a list of tokens.
     * @param string $inString The string to explode into tokens.
     * @return array The method returns a list of tokens.
     *         Each element of the returned list is an instance of the class Token.
     * @throws \Exception
     * @see Token
     */
    public function lex($inString) 
```
   
This method "parses" a given text and returns a list of detected tokens.

The returned array contains the list of detected tokens.

Each element of the returned array is an instance of the class `\dbeurive\Lexer\Token`.

```php
    /**
     * Class Token
     *
     * This class implements a token.
     *
     * @package dbeurive\Lexer
    */
    class Token
    {
        /** @var null|mixed Token's value. */
        public $value = null;
        /** @var null|string Token's type. */
        public $type = null;
    
        /**
         * Token constructor.
         * @param string $inOptValue The token's value.
         * @param string $inOptType The token's type.
         */
        public function __construct($inOptValue=null, $inOptType=null)
        {
            $this->value = $inOptValue;
            $this->type  = $inOptType;
        }
    }
```

