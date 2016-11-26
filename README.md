# Introduction

This repository contains the implementation of a basic lexer.

A lexer explodes a given string into a list of tokens.

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
        array('/[0-9]+/',               'numeric'),
        array('/\$([lg])([a-z0-9]+)/i', 'variable', $varProcessor),
        array('/[a-z]{2,}/i',           'function'),
        array('/(\+|\-|\*|\/)/',        'operator'),
        array('/\(/',                   'open_bracket'),
        array('/\)/',                   'close_bracket'),
        array('/(\s+|\r?\n)/',          'blank', function(array $m) { return null; })
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

The signature of the optional third element (`<transformer callback>`) must be:

    mixed|null function(array $inMatches)

The array passed to the function comes from the processing of the regular expression that describes the token.

* The first element of the array contains the text that matches the full pattern.
* The second element of the array contains the text that matched the first captured parenthesized subpattern.
* The third element of the array contains the text that matched the second captured parenthesized subpattern.
* ... and so on.

> See the description for the PHP function `preg_match()`.

* If the function returns the value `null`, then the detected token is "ignored".
  That is: it will not be inserted into the list of extracted tokens.
* If the function returns a non-null value, then the token is inserted in the list of detected tokens.
  The value of the inserted token will be the value returned by the function.

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

