<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Traits\Command;

use DWenzel\T3extensionTools\Command\ExampleCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExecuteSqlTraitTest extends TestCase
{
    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function sqlDataProvider(): array
    {
        return [
            'empty' => ['', []],
            'only comments' => ["# nothing to see here ;)\n-- also nothing;\n/* block; */", []],
            'one statement per line' => [
                "DELETE FROM a;\nDELETE FROM b;",
                ['DELETE FROM a', 'DELETE FROM b'],
            ],
            'statement over several lines' => [
                "CREATE TABLE a (\n  uid INT\n);",
                ['CREATE TABLE a ( uid INT )'],
            ],
            'multi-line block comment' => [
                "/* start\n   still comment;\n*/\nDELETE FROM a;",
                ['DELETE FROM a'],
            ],
            'windows line endings' => [
                "DELETE FROM a;\r\nDELETE FROM b;\r\n",
                ['DELETE FROM a', 'DELETE FROM b'],
            ],
            'last statement without semicolon' => [
                "DELETE FROM a;\nDELETE FROM b",
                ['DELETE FROM a', 'DELETE FROM b'],
            ],
            'semicolon inside a line does not split' => [
                "INSERT INTO a VALUES ('x;y');",
                ["INSERT INTO a VALUES ('x;y')"],
            ],
        ];
    }

    /**
     * @param list<string> $expected
     */
    #[Test]
    #[DataProvider('sqlDataProvider')]
    public function splitSqlStatementsReturnsSingleStatements(string $sql, array $expected): void
    {
        $command = (new \ReflectionClass(ExampleCommand::class))->newInstanceWithoutConstructor();
        $method = new \ReflectionMethod($command, 'splitSqlStatements');

        self::assertSame($expected, $method->invoke($command, $sql));
    }
}
