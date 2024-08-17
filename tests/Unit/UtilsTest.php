<?php

declare(strict_types = 1);

namespace CodeLts\CliTools\Tests\Unit;

use CodeLts\CliTools\Utils;
use CodeLts\CliTools\Tests\AbstractTestCase;

class UtilsTest extends AbstractTestCase
{

    public function testIsCiDetected(): void
    {
        $this->assertIsBool(Utils::isCiDetected());
    }

}
