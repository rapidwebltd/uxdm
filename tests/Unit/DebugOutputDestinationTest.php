<?php

use PHPUnit\Framework\TestCase;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\Destinations\DebugOutputDestination;

final class DebugOutputDestinationTest extends TestCase
{
    private function createDataRows()
    {
        $faker = Faker\Factory::create();

        $dataRows = [];

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRows[] = $dataRow;

        return $dataRows;
    }

    public function testPutDataRows()
    {
        $dataRows = $this->createDataRows();

        $destination = new DebugOutputDestination();
        ob_start();
        $destination->putDataRows($dataRows);
        $output = $this->stripFirstLine(ob_get_clean());

        ob_start();
        var_dump($dataRows);
        $expectedOutput = $this->stripFirstLine(ob_get_clean());

        $this->assertEquals($expectedOutput, $output);
    }

    private function stripFirstLine($text)
    {
        return substr($text, strpos($text, "\n") + 1);
    }
}
