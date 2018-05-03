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

    /**
     * Class destructor
     */
    public function __destruct();

    /**
     * @brief Gets the number of cars in the last week starting from today
     * @return int
     *
     * Usage: <br>
     * $numCars = getNumCarsThisWeek("lot35"); for Lot 35
     */
    public function getNumCarsThisWeek($locationChoice);

    /**
     * @brief Gives the traffic for each month in an array for the specified year passed in.
     * @param $year
     * @param $locationChoice - string for the lot you want to put in
     * @return int array - a length 12 array where each element is the number of cars that
     * passed through for that month. If there were 8 cars that passed through in April,
     * the element at index 3 would be 8
     *
     * Usage: <br>
     * $yearArray = getTrafficByYear(2018, "lot35"); for 2018 for Lot 35
     */
    public function getTrafficByYear($year, $locationChoice);

    /**
     * @brief Gets the traffic for each day during the specified month of the specified year
     * @param $year
     * @param $month
     * @param $locationChoice
     * @return int array - array with the traffic number for each day of the specified month
     *
     * Usage: <br>
     * $monthArray = getTrafficByMonth(2018, 2, "lot35"); // for February 2018 for Lot 35
     */
    public function getTrafficByMonth($year, $month, $locationChoice);

    /**
     * @brief Gets the traffic for each hour and returns it in a 24 element array.
     * @param $year
     * @param $month
     * @param $day
     * @param $locationChoice
     * @return int array - length 24 array with the traffic number for each our of the day
     *
     * Usage: <br>
     * < $dayArray = getTrafficByDay(2018, 2, 15, "lot35");> for Febraury 15, 2018 for Lot 35
     */
    public function getTrafficByDay($year, $month, $day, $locationChoice);

    /**
     * @brief Takes in a date range (start and end date) and counts the number of cars in the given range
     * @param $year1
     * @param $month1
     * @param $day1
     * @param $year2
     * @param $month2
     * @param $day2
     * @param $locationChoice
     * @return integer - the number of cars in the time range
     *
     * Usage: <br>
     * $trafficInRange = getTrafficTimeRange(2018, 2, 15, 2018, 2, 28, "lot35"); <br>
     * for number of cars between 2/15/2018 and 2/28/2018 for Lot 35
     */
    public function getTrafficTimeRange($year1, $month1, $day1, $year2, $month2, $day2, $locationChoice);

    /**
     * @brief Will return the names of all different locations (parking lots)
     * that are being recorded in the database
     * @return array of strings
     *
     * Usage: <br>
     * $listLocations[] = getListOfLocations();
     */
    public function getListOfLocations();

    /**
     * @brief Finds the minimum count for an hour in a specific time range.
     * @param $countByHour - the corresponding array of dates sorted by hour returned from GroupResultsByHour.
     * @param $dateByHour - the array of counts by hour returned from GroupResultsByHour.
     * @return  Returns an array containing (1) the minimum count within an hour and (2)
     * the corresponding time for which the count was recorded.
     */
    public function GetMinimumCountInHour($countByHour, $dateByHour);

    /**
     * @brief Finds the median count for an hour in a specific time range.
     * @param $countByHour - the array of counts by hour returned from GroupResultsByHour.
     * @param $dateByHour - the corresponding array of dates sorted by hour returned from GroupResultsByHour.
     * @return array - Returns an array containing (1) the median count within an hour and
     * (2) the corresponding time for which the count was recorded.
     */
    public function GetMedianCountInHour($countByHour, $dateByHour);

    /**
     * @brief Finds the maximum count for an hour in a specific time range.
     * @param $countByHour - the array of counts by hour returned from GroupResultsByHour.
     * @param $dateByHour - the corresponding array of dates sorted by hour returned from GroupResultsByHour.
     * @return 2 arrays - Returns an array containing (1) the maximum count within an hour and
     * (2) the corresponding time for which the count was recorded.
     */
    public function GetMaximumCountInHour($countByHour, $dateByHour);

    /**
     * @brief Finds average number of people counted during an hour in a specific time range.
     * @param $countByHour - the array of counts by hour returned from GroupResultsByHour.
     * @return integer - Returns the average number of people recorded in an hour of the specific time range.
     */
    public function GetAverageCountInHour($countByHour);

    /**
     * @brief A function to execute a query of correct MySQL syntax for the specified instance of the
     * database within a certain time range.
     * @param $Time1 - First timestamp of the format ‘YYYY-MM-DD HH:MM:00’
     * @param $Time2 - Second timestamp of the format ‘YYYY-MM-DD HH:MM:00’.
     * @param $COMMON - common instance that connects to the database.
     * @return array - all of the resulting data (in rows) returned from the query.
     *
     *
     * Usage: <br>
     * $Time1 = $_POST["Time1"]; <br>
     * $Time2 = $_POST["Time2"]; <br>
     * // Convert input times into the correct Timestamp format <br>
     * $time1Format = ConvertTime($Time1); <br>
     * $time2Format = ConvertTime($Time2); <br>
     * // Create a new Common instance to connect to the database <br>
     * $debug = false; <br>
     * $COMMON = new Common($debug); <br>
     * // Execute query and fetch the results <br>
     * $results = ExecuteQuery($time1Format, $time2Format, $COMMON); <br>
     */
    public function ExecuteQuery($Time1, $Time2, $COMMON);

    /**
     * @brief Converts a time of a certain format to that of another.
     * @param $Time - timestamp of the format ‘YYYY-MM-DDTHH:MM’.
     * @return string - which is of the form ‘YYYY-MM-DD HH:MM:00’.
     */
    function ConvertTime($Time);

    /**
     * @brief PrintResults is a function used to print the results of a query.
     * @param $results - the results returned from a query, specifically from the function, ExecuteQuery()
     * @return void - returns echo’s html code to return rows with an entry number and the timestamp for it
     */
    function PrintResults($results);

    /**
     * @brief Groups the results of a query by hour and counts the number of results within each hour of the time range.
     * @param $results - the results returned from a query, specifically from the function, ExecuteQuery().
     * @return array - Returns an array containing (1) an array of the dates within the results,
     * grouped by hour, and (2) an array containing the counts for each corresponding hour of the dates array.
     */
    function GroupResultsByHour($results);

    /**
     * @brief Reformats a date into a more readable format.
     * @param $date - A date in format "YYYY-MM-DD HH" where HH ranges from 00 to 23.
     * @return string - The date in format "YYYY-MM-DD HH:00" where HH ranges from 00 to 12 AM/PM.
     */
    function ReformatDate($date);
}