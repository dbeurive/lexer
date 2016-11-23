<?php

//    Copyright (c) 2016 Denis BEURIVE
//
//    This work is licensed under the Creative Commons Attribution 3.0
//    Unported License.
//
//    A summary of the license is given below, followed by the full legal
//    text.
//
//    --------------------------------------------------------------------
//
//    You are free:
//
//    * to Share - to copy, distribute and transmit the work
//    * to Remix - to adapt the work
//
//    Under the following conditions:
//
//    Attribution. You must attribute the work in the manner specified by
//    the author or licensor (but not in any way that suggests that they
//    endorse you or your use of the work).
//
//        * For any reuse or distribution, you must make clear to others
//          the license terms of this work.
//
//        * Any of the above conditions can be waived if you get
//          permission from the copyright holder.
//
//        * Nothing in this license impairs or restricts the author's moral
//          rights.
//
//    Your fair dealing and other rights are in no way affected by the
//    above.

/**
 * This file implements a token.
 */

namespace dbeurive\Lexer;

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
}