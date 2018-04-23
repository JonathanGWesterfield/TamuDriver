<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 5:05 PM
 *
 * */


/**
 * This code is for error reporting and "compile" errors. They really help because they
 * actually tell me whats wrong with the code. Otherwise, i'll often just see a blank
 * page load and its really annoying and hard to debug.
 */
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

ini_set('error_log', './script_errors.log');  // change here
ini_set('log_errors', 'On');

include_once "CommonInterface.php";
include_once "PHPtoSQLInterface.php";


echo("Attempting to start<br>");

$thisCommon = new Common(true);

echo("Started the Common<br>");
echo("Starting the API<br>");

$test = new PHPtoSQL($thisCommon);

echo("Created the DBAPI object<br>");

/** @var $secondMonthArray
 * Testing getTrafficByYear by putting in the current year - should return 12 months*/
$secondMonthArray = $test->getTrafficByYear(2018, true);
if(count($secondMonthArray) != 12)
{
    echo("<br><br>ERROR!! getTrafficByYear Failed!! Does not return 12 months!<br><br>");
}

/** @var $secondDayArray
 * Testing getTrafficByMonth testing February 2020 - should return correct amount of days*/
$secondDayArray = $test->getTrafficByMonth(2020, 2, true);
if(count($secondDayArray) !=cal_days_in_month(CAL_GREGORIAN,2,2020))
{
    echo("<br><br>ERROR!! getTrafficByMonth Failed!! Does not return correct # of days for Feb 2020!<br><br>");
}

/** Should return 0 walkers for Feb 2020 since no records in the future */
if(array_sum($secondDayArray) != 0)
{
    echo("<br><br>ERROR!! getTrafficByMonth Failed!! Does not return correct # of walkers for Feb 2020!<br><br>");
}

/** @var $secondHourArray
 * Testing getTrafficByDay - should return 24 hours */
$secondHourArray = $test->getTrafficByDay(2018, 2, 15);

$numRange = $test->getTrafficTimeRange(2018, 2, 12, 2018, 2, 16);
echo("NUMBER RANGE FROM TEST: " . $numRange . "<br>");



?>