<?php

use PHPUnit\Framework\TestCase;
use RapidWeb\uxdm\Objects\DestinationContainer;
use RapidWeb\uxdm\Objects\Destinations\DebugOutputDestination;

final class DestinationContainerTest extends TestCase
{
    public function testCreation()
    {
        $destination = new DebugOutputDestination();
        $fields = ['name'];

        $destinationContainer = new DestinationContainer($destination, $fields);

        $this->assertEquals($destination, $destinationContainer->destination);
        $this->assertEquals($fields, $destinationContainer->fields);
    }
}
