<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RapidWeb\uxdm\Objects\DataItem;
use RapidWeb\uxdm\Objects\Destinations\PDODestination;
use RapidWeb\uxdm\Objects\Migrator;
use RapidWeb\uxdm\Objects\Sources\PDOSource;

$pdoSource = new PDOSource(new PDO('mysql:dbname=laravel-test;host=127.0.0.1', 'root', getenv('UXDM_EXAMPLE_PASSWORD')), 'users');

$pdoDestination = new PDODestination(new PDO('mysql:dbname=new-test;host=127.0.0.1', 'root', getenv('UXDM_EXAMPLE_PASSWORD')), 'new_users');

if (!class_exists('Cache\Adapter\Filesystem\FilesystemCachePool')) {
    die('This example requires the `FilesystemCachePool` class. Install it with `composer require cache/filesystem-adapter`.'.PHP_EOL);
}

$filesystemAdapter = new Local(__DIR__.'/');
$filesystem = new Filesystem($filesystemAdapter);
$sourceCachePool = new FilesystemCachePool($filesystem);

$migrator = new Migrator();
$migrator->setSource($pdoSource)
         ->setSourceCache($sourceCachePool, 'uxdm-pdo-to-pdo-cache-key', 60 * 60 * 24)
         ->setDestination($pdoDestination)
         ->setFieldsToMigrate(['id', 'email', 'name'])
         ->setKeyFields(['id'])
         ->setDataItemManipulator(function ($dataItem) {
             if ($dataItem->fieldName == 'name') {
                 $dataItem->value = strtoupper($dataItem->value);
             }
         })
         ->setDataRowManipulator(function ($dataRow) {
             $dataRow->addDataItem(new DataItem('random_number', rand(1, 1000)));
         })
         ->setSkipIfTrueCheck(function ($dataRow) {
             $dataItems = $dataRow->getDataItems();
             foreach ($dataItems as $dataItem) {
                 if ($dataItem->fieldName == 'name' && $dataItem->value == 'TEST') {
                     return true;
                 }
             }
         })
         ->withProgressBar()
         ->migrate();
