<?php

use PHPUnit\Framework\TestCase;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\DataRow;
use RapidWeb\uxdm\Objects\Destinations\AssociativeArrayDestination;

final class AssociativeArrayDestinationTest extends TestCase
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
        $array = [];

        $destination = new AssociativeArrayDestination($array);
        $destination->putDataRows($dataRows);

        $expectedArray = [];
        foreach ($dataRows as $dataRow) {
            $expectedArrayRow = [];
            foreach ($dataRow->getDataItems() as $dataItem) {
                $expectedArrayRow[$dataItem->fieldName] = $dataItem->value;
            }
            $expectedArray[] = $expectedArrayRow;
        }

        $this->assertEquals($expectedArray, $array);
    }
}
