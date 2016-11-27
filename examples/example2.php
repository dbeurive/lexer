<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

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