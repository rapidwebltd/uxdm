<?php

use PHPUnit\Framework\TestCase;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\Destinations\NullDestination;

final class NullDestinationTest extends TestCase
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

        $destination = new NullDestination();
        ob_start();
        $destination->putDataRows($dataRows);
        $output = ob_get_clean();

        $expectedOutput = '';

        $this->assertEquals($expectedOutput, $output);
    }
}
