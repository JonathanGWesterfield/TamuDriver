<?php
include_once "CommonInterface.php";
include_once "PHPtoSQLInterface.php";

$thisCommon = new Common(true);
$db = new PHPtoSQL($thisCommon);

// Display all locations in the database.
$printedFirst = false;
foreach($db->getListOfLocations() as $item) {
    if($printedFirst)
        echo "\n";
    echo $item;
    $printedFirst = true;
}