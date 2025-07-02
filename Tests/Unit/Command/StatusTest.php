<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command;

use DWenzel\T3extensionTools\Command\Status;
use DWenzel\T3extensionTools\Command\StatusAwareInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class StatusTest extends TestCase
{
    #[Test] public function implementsStatusAwareInterface(): void
    {
        self::assertInstanceOf(
            StatusAwareInterface::class,
            new Status()
        );
    }

    #[Test] public function successReturnsCorrectStatusCode(): void
    {
        $expectedStatusCode = defined(Command::class . 'Command::class' . '::SUCCESS')
            ? Command::SUCCESS
            : StatusAwareInterface::SUCCESS;

        self::assertEquals(
            $expectedStatusCode,
            Status::success()
        );
    }

    #[Test] public function failureReturnsCorrectStatusCode(): void
    {
        $expectedStatusCode = defined(Command::class . 'Command::class' . '::FAILURE')
            ? Command::FAILURE
            : StatusAwareInterface::FAILURE;

        self::assertEquals(
            $expectedStatusCode,
            Status::failure()
        );
    }

    #[Test] public function invalidReturnsCorrectStatusCode(): void
    {
        $expectedStatusCode = defined(Command::class . 'Command::class' . '::INVALID')
            ? Command::INVALID
            : StatusAwareInterface::INVALID;

        self::assertEquals(
            $expectedStatusCode,
            Status::invalid()
        );
    }
}
