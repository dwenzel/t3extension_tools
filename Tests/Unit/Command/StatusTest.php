<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command;

use DWenzel\T3extensionTools\Command\Status;
use DWenzel\T3extensionTools\Command\StatusAwareInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class StatusTest extends TestCase
{
    /**
     * @test
     */
    public function implementsStatusAwareInterface(): void
    {
        $this->assertInstanceOf(
            StatusAwareInterface::class,
            new Status()
        );
    }
    
    /**
     * @test
     */
    public function successReturnsCorrectStatusCode(): void
    {
        $expectedStatusCode = defined(Command::class . 'Command::class' . '::SUCCESS') 
            ? Command::SUCCESS 
            : StatusAwareInterface::SUCCESS;
            
        $this->assertEquals(
            $expectedStatusCode,
            Status::success()
        );
    }
    
    /**
     * @test
     */
    public function failureReturnsCorrectStatusCode(): void
    {
        $expectedStatusCode = defined(Command::class . 'Command::class' . '::FAILURE') 
            ? Command::FAILURE 
            : StatusAwareInterface::FAILURE;
            
        $this->assertEquals(
            $expectedStatusCode,
            Status::failure()
        );
    }
    
    /**
     * @test
     */
    public function invalidReturnsCorrectStatusCode(): void
    {
        $expectedStatusCode = defined(Command::class . 'Command::class' . '::INVALID') 
            ? Command::INVALID 
            : StatusAwareInterface::INVALID;
            
        $this->assertEquals(
            $expectedStatusCode,
            Status::invalid()
        );
    }
}