<?php

namespace Amp\File\Test;

use Amp\File;
class EioDriverTest extends DriverTest
{
    protected function setUp() : void
    {
        parent::setUp();
        if (!\extension_loaded("eio")) {
            $this->markTestSkipped("eio extension not loaded");
        }
        File\filesystem(new File\EioDriver());
    }
}