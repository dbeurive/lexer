<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use dbeurive\Lexer\Lexer;
use dbeurive\Lexer\Token;

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
} catch (\Exception $e) {
    print "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

$texts = array(
    '$gConstant1 + sin($lCoef1) / cos($lcoef2) * $gTemp - tan(21)',
    "\$gConstant1 + \n sin(\$lCoef1) / cos(\$lcoef2) * \$gTemp - tan(21)"
);

$n = 1;
foreach ($texts as $_text) {
    try {
        $tokens = $lexer->lex($_text);
    } catch (\Exception $e) {
        print "ERROR: " . $e->getMessage() . "\n";
        exit(1);
    }
    print "[Example $n] $_text:\n";
    dumpToken($tokens);
    print "\n";
    $n++;
}




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