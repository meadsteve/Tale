<?php

namespace MeadSteve\Tale\Tests;

use Gamez\Psr\Log\TestLogger;
use MeadSteve\Tale\Tests\Steps\Mocks\MockStep;
use MeadSteve\Tale\Transaction;
use PHPUnit\Framework\TestCase;

class LoggingTest extends TestCase
{
    public function testProvidedLoggerGetsSomeDebugLogs()
    {
        $mockStep = new MockStep();
        $mockLogger = new TestLogger();
        $transaction = (new Transaction($mockLogger))->add($mockStep);
        $transaction->run("starting_state");

        $this->assertTrue($mockLogger->log->countRecordsWithLevel("debug") > 0);
    }
}
