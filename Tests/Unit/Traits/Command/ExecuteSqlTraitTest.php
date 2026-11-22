<?php

declare(strict_types=1);

/*
 * This file is part of the "t3extension_tools" extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version (GPL-2.0-or-later).
 */

namespace DWenzel\T3extensionTools\Tests\Unit\Traits\Command;

use DWenzel\T3extensionTools\Command\ExampleCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Splitting of SQL into statements by ExecuteSqlTrait::splitSqlStatements().
 */
class ExecuteSqlTraitTest extends TestCase
{
    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function sqlDataProvider(): array
    {
        return [
            'empty input' => ['', []],
            'whitespace only' => ["  \n\t\n", []],
            'single statement' => ['SELECT 1;', ['SELECT 1']],
            'last statement without semicolon' => ["SELECT 1;\nSELECT 2", ['SELECT 1', 'SELECT 2']],
            'multi-line statement keeps line breaks' => [
                "CREATE TABLE t (\n  uid INT\n);",
                ["CREATE TABLE t (\n  uid INT\n)"],
            ],
            'several statements on one line' => ['SELECT 1; SELECT 2;', ['SELECT 1', 'SELECT 2']],
            'empty statements are skipped' => [';; SELECT 1;;', ['SELECT 1']],
            'CRLF line endings' => ["SELECT 1;\r\nSELECT 2;\r\n", ['SELECT 1', 'SELECT 2']],
            'hash comment line' => ["# comment;\nSELECT 1;", ['SELECT 1']],
            'dash comment line' => ["-- comment;\nSELECT 1;", ['SELECT 1']],
            'dash comment after statement' => ["SELECT 1; -- x\nSELECT 2;", ['SELECT 1', 'SELECT 2']],
            'dash comment containing a semicolon after statement' => [
                "SELECT 1; -- x; y\nSELECT 2;",
                ['SELECT 1', 'SELECT 2'],
            ],
            'hash comment after statement' => ["SELECT 1; # x\nSELECT 2;", ['SELECT 1', 'SELECT 2']],
            'dash comment inside a statement' => [
                "SELECT 1, -- first; column\n2;",
                ["SELECT 1, \n2"],
            ],
            'dash comment at end of input' => ['SELECT 1; --', ['SELECT 1']],
            'double dash without whitespace is no comment' => ['SELECT 1--1;', ['SELECT 1--1']],
            'only comments' => ["# nothing to see here ;)\n-- also nothing;\n/* block; */", []],
            'block comment on own lines' => ["/* block\n   comment; */\nSELECT 1;", ['SELECT 1']],
            'block comment end on own line' => ["/* start\n   still comment;\n*/\nSELECT 1;", ['SELECT 1']],
            'block comment in the middle of a line' => ['SELECT /* a; b */ 1;', ['SELECT   1']],
            'block comment between tokens' => ['SELECT/**/1;', ['SELECT 1']],
            'block comment after statement' => ['SELECT 1; /* x; */', ['SELECT 1']],
            'unterminated block comment' => ["SELECT 1;\n/* x;\nSELECT 2;", ['SELECT 1']],
            'MySQL executable comment is kept' => [
                "/*!40101 SET NAMES utf8 */;\nSELECT 1;",
                ['/*!40101 SET NAMES utf8 */', 'SELECT 1'],
            ],
            'MySQL optimizer hint is kept' => [
                'SELECT /*+ NO_INDEX(t) */ uid FROM t;',
                ['SELECT /*+ NO_INDEX(t) */ uid FROM t'],
            ],
            'semicolon at end of line inside a string' => [
                "INSERT INTO t VALUES ('a;\nb');\nSELECT 1;",
                ["INSERT INTO t VALUES ('a;\nb')", 'SELECT 1'],
            ],
            'doubled single quote' => [
                "INSERT INTO t VALUES ('it''s; ok');",
                ["INSERT INTO t VALUES ('it''s; ok')"],
            ],
            'backslash escaped single quote' => [
                "INSERT INTO t VALUES ('a\\'; b');",
                ["INSERT INTO t VALUES ('a\\'; b')"],
            ],
            'comment markers inside a string' => [
                "INSERT INTO t VALUES ('-- x; # y; /* z; */');",
                ["INSERT INTO t VALUES ('-- x; # y; /* z; */')"],
            ],
            'double-quoted string or identifier' => [
                'SELECT "a;b" FROM t;',
                ['SELECT "a;b" FROM t'],
            ],
            'backtick identifier' => [
                'SELECT `a;b` FROM t;',
                ['SELECT `a;b` FROM t'],
            ],
            'backslash does not escape in backtick identifier' => [
                'SELECT `a\\`; SELECT 2;',
                ['SELECT `a\\`', 'SELECT 2'],
            ],
            'unterminated string' => ["SELECT 'a;\nSELECT 2;", ["SELECT 'a;\nSELECT 2;"]],
        ];
    }

    /**
     * @param list<string> $expected
     */
    #[Test]
    #[DataProvider('sqlDataProvider')]
    public function splitSqlStatementsReturnsStatements(string $sql, array $expected): void
    {
        self::assertSame($expected, $this->splitSqlStatements($sql));
    }

    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function sqlWithoutBackslashEscapesDataProvider(): array
    {
        return [
            'backslash at end of string' => [
                "SELECT 'C:\\'; SELECT 2;",
                ["SELECT 'C:\\'", 'SELECT 2'],
            ],
            'doubled single quote' => [
                "INSERT INTO t VALUES ('it''s; ok');",
                ["INSERT INTO t VALUES ('it''s; ok')"],
            ],
            'doubled double quote' => [
                'SELECT 1 AS "a"";b";',
                ['SELECT 1 AS "a"";b"'],
            ],
        ];
    }

    /**
     * @param list<string> $expected
     */
    #[Test]
    #[DataProvider('sqlWithoutBackslashEscapesDataProvider')]
    public function splitSqlStatementsWithoutBackslashEscapesReturnsStatements(string $sql, array $expected): void
    {
        self::assertSame($expected, $this->splitSqlStatements($sql, false));
    }

    /**
     * @return list<string>
     */
    private function splitSqlStatements(string $sql, bool $backslashEscapes = true): array
    {
        $subject = (new \ReflectionClass(ExampleCommand::class))->newInstanceWithoutConstructor();
        $method = new \ReflectionMethod($subject, 'splitSqlStatements');

        /** @var list<string> $statements */
        $statements = $method->invoke($subject, $sql, $backslashEscapes);

        return $statements;
    }
}
