<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 4/22/18
 * Time: 2:58 PM
 */

include_once "PHPtoSQL.php";

interface PHPtoSQLInterface
{
    /**
     * DBAPI constructor.
     * @param $db
     *
     * Mostly sets up the dates in this object. Also sets the timezone to our timezone.
     */
    public function __construct($db);

    // class Destructor
    public function __destruct();
    public function GetMinimumCountInHour($countByHour, $dateByHour);
    public function GetMedianCountInHour($countByHour, $dateByHour);
    public function GetMaximumCountInHour($countByHour, $dateByHour);
    public function GetAverageCountInHour($countByHour);
    public function getNumCarsThisWeek($locationChoice);
    public function getTrafficByYear($year, $locationChoice);
    public function getTrafficByMonth($year, $month, $locationChoice);
    public function getTrafficByDay($year, $month, $day, $locationChoice);
    public function getTrafficTimeRange($year1, $month1, $day1, $year2, $month2, $day2, $locationChoice);




}