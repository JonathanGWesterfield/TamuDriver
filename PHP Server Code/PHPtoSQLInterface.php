<?php
/**
 * Created by PhpStorm.
 * User: Jabroni
 * Date: 4/22/18
 * Time: 2:58 PM
 */

include_once "PHPtoSQL.php";

// TRUE = Lot 35, FALSE = Lot 54

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
    /**
     * @return int
     *
     * Gets the number of cars in the last week starting from today
     */
    public function getNumCarsThisWeek($locationChoice);

    /**
     * @param $year
     * @return array - a length 12 array where each element is the number of cars that
     * passed through for that month. If there were 8 cars that passed through in April,
     * the element at index 3 would be 8
     *
     * Gives the traffic for each month in an array for the specified year passed in
     */
    public function getTrafficByYear($year, $locationChoice);

    /**
     * @param $year
     * @param $month
     * @return array
     *
     * Gets the traffic for each day during the specified month of the specified year
     *
     * Usage: getTrafficByMonth(2018, 2); // for February 2018
     */
    public function getTrafficByMonth($year, $month, $locationChoice);

    /**
     * @param $year
     * @param $month
     * @param $day
     * @return array
     *
     * Gets the traffic for each hour and returns it in a 24 element array.
     *
     * Usage: <var = getTrafficByDay(2018, 2, 15);> for Febraury 15, 2018
     */
    public function getTrafficByDay($year, $month, $day, $locationChoice);

    /**
     * @param $year1
     * @param $month1
     * @param $day1
     * @param $year2
     * @param $month2
     * @param $day2
     * @return int - the number of cars in the time range
     *
     * Takes in a date range (start and end date) and counts the number of cars in the given range
     */
    public function getTrafficTimeRange($year1, $month1, $day1, $year2, $month2, $day2, $locationChoice);




}