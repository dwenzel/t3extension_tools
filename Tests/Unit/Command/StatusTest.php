<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Command;

use DWenzel\T3extensionTools\Command\Status;
use DWenzel\T3extensionTools\Command\StatusAwareInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class StatusTest extends TestCase
{
    #[Test]
    public function implementsStatusAwareInterface(): void
    {
        self::assertInstanceOf(StatusAwareInterface::class, new Status());
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function statusDataProvider(): array
    {
        return [
            'success' => ['success', Command::SUCCESS],
            'failure' => ['failure', Command::FAILURE],
            'invalid' => ['invalid', Command::INVALID],
        ];
    }

    #[Test]
    #[DataProvider('statusDataProvider')]
    public function statusMethodReturnsSymfonyStatusCodeAndIsDeprecated(string $method, int $expected): void
    {
        $this->expectUserDeprecationMessage(
            Status::class . '::' . $method . '() is deprecated since t3extension_tools 7.0 and will be removed in 8.0.'
            . ' Use ' . Command::class . '::' . strtoupper($method) . ' instead.'
        );

        self::assertSame($expected, Status::$method());
    }
}
