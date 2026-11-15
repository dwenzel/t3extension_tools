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
     * @return array<string, array{int, int}>
     */
    public static function statusDataProvider(): array
    {
        return [
            'success' => [Status::success(), Command::SUCCESS],
            'failure' => [Status::failure(), Command::FAILURE],
            'invalid' => [Status::invalid(), Command::INVALID],
        ];
    }

    #[Test]
    #[DataProvider('statusDataProvider')]
    public function statusMethodReturnsSymfonyStatusCode(int $status, int $expected): void
    {
        self::assertSame($expected, $status);
    }
}
