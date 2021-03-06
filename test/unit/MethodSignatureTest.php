<?php

namespace michaelbutler\php5to7\test\unit;

use michaelbutler\php5to7\MethodSignature;

class MethodSignatureTest extends \PHPUnit_Framework_TestCase
{
    private function getSampleTokens()
    {
        $tokens = [
            64 =>
                array(
                    0 => T_WHITESPACE,
                    1 => '

    ',
                    2 => 27,
                ),
            65 =>
                array(
                    0 => T_DOC_COMMENT,
                    1 => '/**
     * @param string $param1 This does some stuff.
     *
     * @param string $param2 This does some more stuff.
     * @param array $param3 This does some more stuff.
     *
     * @return string
     */',
                    2 => 29,
                ),
            66 =>
                array(
                    0 => T_WHITESPACE,
                    1 => '
    ',
                    2 => 35,
                ),
            67 =>
                array(
                    0 => T_PUBLIC,
                    1 => 'public',
                    2 => 36,
                ),
            68 =>
                array(
                    0 => T_WHITESPACE,
                    1 => ' ',
                    2 => 36,
                ),
            69 =>
                array(
                    0 => T_FUNCTION,
                    1 => 'function',
                    2 => 36,
                ),
            70 =>
                array(
                    0 => T_WHITESPACE,
                    1 => ' ',
                    2 => 36,
                ),
            71 =>
                array(
                    0 => T_STRING,
                    1 => 'myFunction2',
                    2 => 36,
                ),
            72 => '(',
            73 =>
                array(
                    0 => T_VARIABLE,
                    1 => '$param1',
                    2 => 36,
                ),
            74 => ',',
            75 =>
                array(
                    0 => T_WHITESPACE,
                    1 => ' ',
                    2 => 36,
                ),
            76 =>
                array(
                    0 => T_VARIABLE,
                    1 => '$param2',
                    2 => 36,
                ),
            77 => ')',
            78 =>
                array(
                    0 => T_WHITESPACE,
                    1 => '
    ',
                    2 => 36,
                ),
            79 => '{',
        ];
        return $tokens;
    }

    public function testCreateFromTokensWillReturnNull()
    {
        $tokens = $this->getSampleTokens();
        $result = MethodSignature::createFromTokens($tokens, 4);

        static::assertNull($result);
    }

    public function testCreateFromTokensWithDanglingPhpDocWillReturnNull()
    {
        $tokens = [
            64 =>
                array(
                    0 => T_WHITESPACE,
                    1 => '

    ',
                    2 => 27,
                ),
            65 =>
                array(
                    0 => T_DOC_COMMENT,
                    1 => '/**
     * @param string $param1 This does some stuff.
     *
     * @param string $param2 This does some more stuff.
     * @param array $param3 This does some more stuff.
     *
     * @return string
     */',
                    2 => 29,
                ),
            66 => 'z',
            67 =>
                array(
                    0 => T_DOC_COMMENT,
                    1 => '/**
     * @param string $param1 This does some stuff.
     *
     * @param string $param2 This does some more stuff.
     * @param array $param3 This does some more stuff.
     *
     * @return string
     */',
                    2 => 29,
                ),
            68 =>
                array(
                    0 => T_WHITESPACE,
                    1 => '
    ',
                    2 => 35,
                ),
        ];

        $result = MethodSignature::createFromTokens($tokens, 65);
        static::assertNull($result);
    }

    public function testCreateFromTokensStartingOnNonDocCommentWillReturnNull()
    {
        $tokens = $this->getSampleTokens();
        $result = MethodSignature::createFromTokens($tokens, 64);
        static::assertNull($result);
    }

    public function testGetSizeMethodReturnsTokenCount()
    {
        $tokens = $this->getSampleTokens();
        $result = MethodSignature::createFromTokens($tokens, 65);
        static::assertSame(14, $result->getSize());
    }

    public function testCreateFromTokensWillReturnMethodSignature()
    {
        $tokens = $this->getSampleTokens();
        $result = MethodSignature::createFromTokens($tokens, 65);

        $expected = [
            '$param1' => [
                'name' => '$param1',
                'type' => 'string',
            ],
            '$param2' => [
                'name' => '$param2',
                'type' => 'string',
            ],
            '$param3' => [
                'name' => '$param3',
                'type' => 'array',
            ],
        ];

        // Call once to "cache" the results
        $result->getVariableTypeMap();

        static::assertNotEmpty($result);
        static::assertSame($expected, $result->getVariableTypeMap());
    }
}
