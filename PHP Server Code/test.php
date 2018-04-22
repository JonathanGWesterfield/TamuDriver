<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 5:05 PM
 *
 * This code is for error reporting and "compile" errors. They really help because they
 * actually tell me whats wrong with the code. Otherwise, i'll often just see a blank
 * page load and its really annoying and hard to debug.
 */

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

ini_set('error_log', './script_errors.log');  // change here
ini_set('log_errors', 'On');

include_once "CommonInterface.php";
include_once "DBInterface.php";


echo("Attempting to start<br>");

$thisCommon = new Common(true);

echo("Started the Common<br>");
echo("Starting the API<br>");

$test = new DBAPI($thisCommon);

echo("Created the DBAPI object<br>");

$test->printEntireDB();

/** Testing getTotalNumWalkers - should be 17 */
if($test->getTotalNumWalkers() != 17)
{
    echo("<br><br>getTotalNumWalkers() failed! Didn't equal 17 (the number of rows in the DB<br><br>");
}

/** Testing getNumWalkersToday - Should be 0 */
if($test->getNumWalkersToday() != 0)
{
    echo("<br><br>getNumWalkersToday() failed! There are supposed to be 0 walkers today! <br><br>");
}

/** Testing getNumWalkersThisWeek - Should be 0 */
if($test->getNumWalkersThisWeek() != 8)
{
    echo("<br><br>getNumWalkersThisWeek() failed! There are supposed to be 8 walkers this week! <br><br>");
}

/** @var $monthArray
 * Testing getCurrentYearTraffic - Should be 12 months returned */
$monthArray = $test->getCurrentYearTraffic();
if(count($monthArray) != 12)
{
    echo("<br><br>Error in getCurrentYearTraffic()!!<br>");
    echo("Did not return 12 months in the year!<br><br>");
}

/** @var $dayArray
 * Testing getCurrentMonthTraffic - should have the correct number of days in the current month
 */
$dayArray = $test->getCurrentMonthTraffic();
if(count($dayArray) != cal_days_in_month(CAL_GREGORIAN,2,2018))
{
    echo("Number of days in the month<br>" . cal_days_in_month(CAL_GREGORIAN,2,2018));

    echo("<br><br>ERROR!! getCurrentMonthTraffic Failed! Wrong number of days returned!<br><br>");
}

/** @var $hourArray
 * Testing getCurrentDayTraffic - should return length 24 for a 24 hour day*/
$hourArray = $test->getCurrentDayTraffic();
if(count($hourArray) != 24)
{
    echo("<br><br>ERROR! getCurrentDayTraffic Failed! Did not return 24 hours!<br><br>");
}

/** @var $secondMonthArray
 * Testing getTrafficByYear by putting in the current year - should return 12 months*/
$secondMonthArray = $test->getTrafficByYear(2018);
if(count($secondMonthArray) != 12)
{
    echo("<br><br>ERROR!! getTrafficByYear Failed!! Does not return 12 months!<br><br>");
}

/** @var $secondDayArray
 * Testing getTrafficByMonth testing February 2020 - should return correct amount of days*/
$secondDayArray = $test->getTrafficByMonth(2020, 2);
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
if(count($secondHourArray) != 24)
{
    echo("<br><br>ERROR! getTrafficByDay Failed! Did not return 24 hours!<br><br>");
}

$numRange = $test->getTrafficTimeRange(2018, 2, 12, 2018, 2, 16);
echo("NUMBER RANGE FROM TEST: " . $numRange . "<br>");
if($numRange != 8)
{
    echo("ERROR! getTrafficTimeRange did not return the correct number of walkers!<br><br>");
}


?>