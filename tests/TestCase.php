<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private int $outputBufferLevel = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->outputBufferLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        // Some third-party packages (and occasionally Blade rendering edge cases)
        // may leave output buffers open, which PHPUnit reports as "risky".
        while (ob_get_level() > $this->outputBufferLevel) {
            ob_end_clean();
        }

        parent::tearDown();
    }
}
