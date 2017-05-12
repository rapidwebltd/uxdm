<?php

namespace RapidWeb\uxdm\Objects\Destinations;

use RapidWeb\uxdm\Interfaces\DestinationInterface;
use RapidWeb\uxdm\Objects\DataRow;
use PDO;
use Exception;

class PDODestination implements DestinationInterface
{
    private $pdo;
    private $tableName;

    public function __construct(PDO $pdo, $tableName) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    private function rowAlreadyExists(array $keyDataItems) {

        $sql = 'select count(*) as c from '.$this->tableName.' where ';

        foreach($keyDataItems as $keyDataItem) {
            $sql .= $keyDataItem->fieldName.' = ? ,';
        }

        $sql = substr($sql, 0, -1);

        $stmt = $this->pdo->prepare($sql);

        $paramNum = 1;
        foreach($keyDataItems as $keyDataItem) {
            $stmt->bindValue($paramNum, $keyDataItem->value);
            $paramNum++;
        }

        $stmt->execute();

        $result = $stmt->fetchObject();

        if ($result->c > 0) {
            return true;
        } else {
            return false;
        }

    }

    private function insertDataRow(DataRow $dataRow) {

        $sql = 'insert into '.$this->tableName.' set ';

        $dataItems = $dataRow->getDataItems();

        foreach($dataItems as $dataItem) {
            $sql .= $dataItem->fieldName.' = ? , ';
        }

        $sql = substr($sql, 0, -2);

        $stmt = $this->pdo->prepare($sql);

        $paramNum = 1;
        foreach($dataItems as $dataItem) {
            $stmt->bindValue($paramNum, $dataItem->value);
            $paramNum++;
        }

        $stmt->execute();
    }

    private function updateDataRow(DataRow $dataRow) {

        $sql = 'update '.$this->tableName.' set ';

        $dataItems = $dataRow->getDataItems();

        foreach($dataItems as $dataItem) {
            $sql .= $dataItem->fieldName.' = ? , ';
        }

        $sql = substr($sql, 0, -2);

        $sql .= 'where ';

        $keyDataItems = $dataRow->getKeyDataItems();

        foreach($keyDataItems as $keyDataItem) {
            $sql .= $keyDataItem->fieldName.' = ? , ';
        }

        $sql = substr($sql, 0, -2);

        $stmt = $this->pdo->prepare($sql);

        $paramNum = 1;
        foreach($dataItems as $dataItem) {
            $stmt->bindValue($paramNum, $dataItem->value);
            $paramNum++;
        }
        foreach($keyDataItems as $keyDataItem) {
            $stmt->bindValue($paramNum, $keyDataItem->value);
            $paramNum++;
        }

        $stmt->execute();
    }

    public function putDataRows(array $dataRows) {

        $dataRowsToInsert = [];
        $dataRowsToUpdate = [];

        foreach ($dataRows as $dataRow) {

            $keyDataItems = $dataRow->getKeyDataItems();            

            if (!$keyDataItems) {
                $dataRowsToInsert[] = $dataRow;
                continue;
            }

            if ($this->rowAlreadyExists($keyDataItems)) {
                $dataRowsToUpdate[] = $dataRow;
            } else {
                $dataRowsToInsert[] = $dataRow;
            }
            
        }

        foreach ($dataRowsToInsert as $dataRow) {
            $this->insertDataRow($dataRow);
        }

        foreach ($dataRowsToUpdate as $dataRow) {
            $this->updateDataRow($dataRow);
        }

    }
}