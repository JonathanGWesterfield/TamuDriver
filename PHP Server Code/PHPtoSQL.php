<?php

/*****************************************
** File:    PHPtoSQL.php
** Project: CSCE 315 Project 1, Spring 2018
** Author:  XXXXXXXXXXX
** Date:    2/2/18
** Section: 505
** E-mail:  XXXXXXXXXXX
**
**   This file contains code for Project 1 to connect the PHP for the web site
**   to the database.
**   This file takes a range of timestamps from user input in index.html,
**   performs input validation, and queries the results of the database
**   table accordingly. It also displays statistics regarding the selected data.
**
***********************************************/

include_once "CommonInterface.php";
include_once "PHPtoSQLInterface.php";

class PHPtoSQL implements PHPtoSQLInterface
{
    private $COMMON;
    private $currentDate;
    private $currententryTime;
    private $currentWeekDay;
    private $tomorrowDate;
    private $currentYear;
    private $nextYear;
    private $currentMonth;
    private $lot1 = "lot35";
    private $lot2 = "lot54";

    /**
     * DBAPI constructor.
     * @param $db
     *
     * Mostly sets up the dates in this object. Also sets the timezone to our timezone.
     */
    public function __construct($db)
    {
        // echo("<br>Constructor has been called<br>");

        $this->COMMON = $db;

        date_default_timezone_set('America/Chicago');

        // Check to make sure the timezone the server is in is set correctly (to College Station)
        if (date_default_timezone_get() != 'America/Chicago')
        {
            // echo("ERROR! TIMEZONE SET INCORRECTLY!!! NOT SET TO COLLEGE STATION'S TIME (CHICAGO)!!<br>");
            // echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br/>';
            exit(1);
        }
        // echo("Default Timezone set to: " . date_default_timezone_get() . "<br>");

        $this->currentDate = date("Y-m-d");
        $this->currententryTime = date("Y-m-d H:i:s");
        $this->currentWeekDay = date('w');
        $this->tomorrowDate = date('Y-m-d', strtotime('+1 day'));
        $this->currentYear = date("Y");
        $this->currentMonth = date("m");
        $this->nextYear = date('Y', strtotime('+1 year'));
        $this->nextYear = $this->nextYear . "-01-01";

        // echo("Current Date: " . $this->currentDate . "<br>");
        // echo("Current entryTime: " . $this->currententryTime . "<br>");
        // echo("Current weekday numerically: " . $this->currentWeekDay . "<br>");
        // echo("Tomorrow's Date: " . $this->tomorrowDate . "<br>");
        // echo("The current year is: " . $this->currentYear . "<br>");
        // echo("The current month is: " . $this->currentMonth . "<br>");
        // echo("Next year is: " . $this->nextYear . "<br>");
        // echo("<br><br>");
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        // echo("<br>Destructor has been called <br>");
        // echo("DESTROYING EVERTYING<br>");
    }

    /**
     * @return int
     *
     * Gets the number of walkers in the last week starting from today
     */
    public function getNumCarsThisWeek($locationChoice)
    {
        // echo("<br><br>getNumWalkersThisWeek<br>");
        $lastWeek = date('Y-m-d', strtotime('-1 week'));

        // echo("Last weeks date: " . $lastWeek . "<br>");

        $sql = "SELECT COUNT(entryNumber) FROM DriverData WHERE entryTime BETWEEN \"" . $lastWeek
            . "%\" AND \"" . $this->tomorrowDate . "%\" AND InOrOut=1 AND location = \"" . $locationChoice . "\"";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        // echo("Total Num Walkers is: " . $row['COUNT(entryNumber)'] . "<br>");

        return (int)$row['COUNT(entryNumber)'];
    }

    /**
     * @brief Replaces the boilerplate code that determines if the location is lot 54 or lot 35 based
     * on the boolean value that is passed in to the funciton
     * @param $locationChoice
     * @return string
     */
    private function determineLocation($locationChoice)
    {
        switch($locationChoice)
        {
            case 0:
                return $this->lot1; // lot 35
                break;
            case 1:
                return $this->lot2; // lot 54
                break;
            default:
                return $this->lot1; // lot 35
        }
    }

    /**
     * @param $year
     * @return array - a length 12 array where each element is the number of cars that
     * passed through for that month. If there were 8 cars that passed through in April,
     * the element at index 3 would be 8
     *
     * Gives the traffic for each month in an array for the specified year passed in
     */
    public function getTrafficByYear($year, $locationChoice)
    {
        // get the date for first day of next year to get the full range of time
        $thisYear = new DateTime((string)$year . "-01-01");
        $nextYear = clone $thisYear;
        $nextYear->modify('+1 year');

        $prevMonth = clone $nextYear;

        $monthArray = []; // array for the numbers for the months

        for ($i = 0; $i < 12; $i++) // get numbers for each month
        {
            $lookMonth = clone $prevMonth;
            $prevMonth->modify('-1 month'); // decrement month
            // echo $prevMonth->format('Y-m-d');

            $sql = "SELECT COUNT(entryNumber) FROM DriverData WHERE entryTime BETWEEN \"" .
                $prevMonth->format('Y-m-d') . "%\" AND \"" .
                $lookMonth->format('Y-m-d') . "%\" AND InOrOut=1 AND location = \"" . $locationChoice . "\"";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            array_push($monthArray, $row['COUNT(entryNumber)']); // add the result to the month array
        }

        /* echo("The numbers for this year starting from the end of the year: ");
        foreach (array_reverse($monthArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>"); */

        return array_reverse($monthArray); // reverse the array to start in January instead of December
    }

    /**
     * @param $year
     * @param $month
     * @return array
     *
     * Gets the traffic for each day during the specified month of the specified year
     *
     * Usage: getTrafficByMonth(2018, 2); // for February 2018
     */
    public function getTrafficByMonth($year, $month, $locationChoice)
    {
        // get the very first day of the month
        $thisMonth = new DateTime((string)$year . "-" . (string)$month . "-01");
        // echo("The beginning of this month: " . $thisMonth->format('Y-m-d') . "<br>");

        // copy to make the next Month's object
        $nextMonth = clone $thisMonth;

        // get the next month's date by incrementing a month
        $nextMonth->modify('+1 month');
        // echo("Next Month is: " . $nextMonth->format('Y-m-d') . "<br>");

        // calculate the # of days between the start and end of the month - # of days in the month
        $diff = $nextMonth->diff($thisMonth)->format("%a");

        // echo("Difference between the 2 months in days: " . $diff . "<br>");

        // create 2 days to look at the numbers between each day
        $lookDay = clone $nextMonth;
        $dayBefore = clone $nextMonth;
        $dayBefore->modify('-1 day');

        // echo("Look day: " . $lookDay->format('Y-m-d') . "<br>");
        // echo("Day Before: " . $dayBefore->format('Y-m-d') . "<br>");

        $dayArray = [];

        for($i = 0; $i < $diff; $i++)
        {
            $sql = "SELECT COUNT(entryNumber) FROM DriverData WHERE entryTime BETWEEN \"" .
                $dayBefore->format('Y-m-d') . "%\" AND \"" . $lookDay->format('Y-m-d') .
                "%\" AND InOrOut=1 AND location = \"" . $locationChoice . "\"";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            // add the result to the day array
            array_push($dayArray, $row['COUNT(entryNumber)']);

            // decrement the target days for the next iteration
            $lookDay->modify("-1 day");
            $dayBefore->modify("-1 day");
        }

        /* echo("Numbers for the days of this current month: <br>");
        foreach (array_reverse($dayArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>"); */

        // return the days reversed since the original array starts from the end of the month
        return array_reverse($dayArray);
    }

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
    public function getTrafficByDay($year, $month, $day, $locationChoice)
    {
        // get the next day to get the correct date
        // get the time of the absolute end of the day and the hour before that
        $endOfToday = new DateTime((string)$year . "-" . (string)$month . "-" . (string)$day . " 00:00:00");
        $endOfToday->modify('+1 day');
        $prevHour = clone $endOfToday;
        $prevHour->modify('-1 hour');

        // echo("End of Today: " . $endOfToday->format('Y-m-d H:i:s') . "<br>");
        // echo("End of Today - 1 hour: " . $prevHour->format('Y-m-d H:i:s') . "<br>");

        $hourArray = [];

        for($i = 0; $i < 24; $i++)
        {
            /** output the times to see if I overshot how many times to iterate */
            // echo("End of Today: " . $endOfToday->format('Y-m-d H:i:s') . "<br>");
            // echo("End of Today - 1 hour: " . $prevHour->format('Y-m-d H:i:s') . "<br>");

            // get the number of walkers in between the 2 times
            $sql = "SELECT COUNT(entryNumber) FROM DriverData WHERE entryTime BETWEEN \"" .
                $prevHour->format('Y-m-d H:i:s') . "\" AND \"" . $endOfToday->format('Y-m-d H:i:s') .
                "\" AND InOrOut=1 AND location = \"" . $locationChoice . "\"";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            // push the result onto the array
            array_push($hourArray, $row['COUNT(entryNumber)']);

            // decrement the hour for the next iteration
            $endOfToday->modify('-1 hour');
            $prevHour->modify('-1 hour');
        }

        /* echo("Numbers by hour: ");
        foreach (array_reverse($hourArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>"); */

        // reverse the array since it starts from the end of the day
        return array_reverse($hourArray);
    }

    /**
     * @param $year1
     * @param $month1
     * @param $day1
     * @param $year2
     * @param $month2
     * @param $day2
     * @return int
     *
     * Takes in a date range (start and end date) and counts the number of cars in the given range
     */
    public function getTrafficTimeRange($year1, $month1, $day1, $year2, $month2, $day2, $locationChoice)
    {
        $startDay = new DateTime((string)$year1 . "-" . (string)$month1 . "-" . (string)$day1 . " 00:00:00");
        // get the next day to get the correct date

        // get the time of the absolute end of the end day
        $endDay = new DateTime((string)$year2 . "-" . (string)$month2 . "-" . (string)$day2 . " 00:00:00");
        $endDay->modify('+1 day');


        // echo("Start Day: " . $startDay->format('Y-m-d H:i:s') . "<br>");
        // echo("End Day: " . $endDay->format('Y-m-d H:i:s') . "<br>");

        $sql = "SELECT COUNT(entryNumber) FROM DriverData WHERE entryTime BETWEEN \"" .
            $startDay->format('Y-m-d H:i:s') . "\" AND \"" . $endDay->format('Y-m-d H:i:s') .
            "\" AND InOrOut=1 AND location = \"" . $locationChoice . "\"";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
        $row = $rs->fetch(PDO::FETCH_ASSOC);

        // echo("Number of People in given time range: " . $row['COUNT(entryNumber)'] . "<br><br>");

        return (int)$row['COUNT(entryNumber)'];
    }

    /**
     * @return array of strings -  Will return the names of all different locations (parking lots)
     * that are being recorded in the database
     */
    public function getListOfLocations()
    {
        // create an array of locations to return
        $locationArray = [];

        $sql = "SELECT DISTINCT location FROM DriverData";
        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
        while($row = $rs->fetch(PDO::FETCH_ASSOC))
        {
            foreach($row as $element)
            {
                array_push($locationArray, $element);
            }
        }

        echo("Locations in Database: ");
        foreach ($locationArray as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>");

    }

    // GetMinimumCountInHour
    // Given an array of dates grouped by hour and corresponding array of counts in each hour,
    // return the minimum count and corresponding hour
    function GetMinimumCountInHour($countByHour, $dateByHour)
    {
        // Find minimum element in array
        $minCountByHour = min($countByHour);

        // Get element in date array corresponding to the minimum element in count array
        $index = array_search(min($countByHour), $countByHour);
        $dateOfMinHour = $dateByHour[$index];

        return array($minCountByHour, $dateOfMinHour);

    }

    // GetMedianCountInHour
    // Given an array of dates grouped by hour and corresponding array of counts in each hour,
    // return the median count and corresponding hour
    function GetMedianCountInHour($countByHour, $dateByHour)
    {
        // Sort array of counts
        $sortedCountByHour = $countByHour;
        rsort($sortedCountByHour);

        // find middle index of original array
        $midIndex = round(count($sortedCountByHour)/2);
        $median = $sortedCountByHour[$midIndex - 1];

        // Get element in date array corresponding to the median element in count array
        $index = array_search($median, $countByHour);
        $dateOfMedianHour = $dateByHour[$index];
        return array($median, $dateOfMedianHour);

    }

    // GetMaximumCountInHour
    // Given an array of dates grouped by hour and corresponding array of counts in each hour,
    // return the maximum count and corresponding hour
    function GetMaximumCountInHour($countByHour, $dateByHour)
    {
        // Find maximum element in array
        $maxCountByHour = max($countByHour);

        // Get element in date array corresponding to the maximum element in count array
        $index = array_search(max($countByHour), $countByHour);
        $dateOfMaxHour = $dateByHour[$index];

        return array($maxCountByHour, $dateOfMaxHour);
    }

    // GetAverageCountInHour
    // Given an array of counts per hour, return the average number of people per hour
    function GetAverageCountInHour($countByHour)
    {
        // Sum up the values in count array
        $runningSum = 0;
        foreach ($countByHour as $value)
        {
            $runningSum = $runningSum + $value;
        }

        // Divide by the number of elements in array to compute average
        $average = $runningSum / sizeof($countByHour);

        return $average;

    }



    // ExecuteQuery
    // Given two input times for the time range, return the query results
    function ExecuteQuery($Time1, $Time2, $COMMON)
    {
        $sql = "SELECT * FROM `ArduinoProject1` WHERE `entryTime` BETWEEN '" . $Time1 . "' AND '" . $Time2 . "';";
        $rs = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
        return $rs;
    }

    // ConvertTime
    // Given timestamp from form, convert into the correct Timestamp format
    function ConvertTime($Time)
    {
        $timeFormat = substr($Time, 0,10) . " " . substr($Time, 11, 5) . ":00";
        return $timeFormat;
    }

    // PrintResults
    // Given query results, print table of results to website
    function PrintResults($results)
    {
        $count = 0;
        while($row = $results->fetch(PDO::FETCH_ASSOC))
        {
            echo("<tr>");
            echo("<td>" . $row['entryNumber'] . "</td>");
            $count = $count + 1;
            echo("<td>" . $row['entryTime'] . "</td>");
            echo("</tr>");
        }
        echo ("<p>Traffic count in selected range: " . $count . "</p>");

    }

    // GroupResultsByHour
    // Given query results, count number of results in each hour and return array
    // containing an array of the traffic counted in each hour and a corresponding array with
    // dates within time range grouped by hour
    function GroupResultsByHour($results)
    {
        // Get first date in results
        $row = $results->fetch(PDO::FETCH_ASSOC);
        $firstTime = $row[entryTime];

        // Format first date by "YYYY-MM-DD HH"
        $formattedTimeMarker = substr($firstTime, 0, 13);

        $countByHour = array(0);
        $dateByHour = array($formattedTimeMarker);
        $index = 0; // use to access first element of array

        while ($row = $results->fetch(PDO::FETCH_ASSOC))
        {
            // Format first date by "YYYY-MM-DD HH"
            $formattedRowTime = substr($row[entryTime], 0, 13);

            // Increment the element in the count array corresponding to the matching date
            if ($formattedRowTime == $formattedTimeMarker)
            {
                $countByHour[$index] = $countByHour[$index] + 1;
            }
            else
            {
                $formattedTimeMarker = $formattedRowTime;
                array_push($dateByHour,$formattedRowTime);
                array_push($countByHour,1);
                $index = $index + 1;
            }
        }

        return array($countByHour, $dateByHour);
    }

    // ReformatDate
    // Given a date in format "YYYY-MM-DD HH" where HH ranges from 00 to 23,
    // reformat to "YYYY-MM-DD HH:00" where HH ranges from 00 to 12 AM/PM
    function ReformatDate($date)
    {
        // Extract the date from the input string
        $dateString = substr($date, 0, 10);

        // Extract the time from the input string
        $timeString = substr($date, 11, 13);

        // Determine if the time should be AM or PM
        $amOrPm = "";
        if ($timeString > 12) {
            $amOrPm = "PM";
        }
        else {
            $amOrPm = "AM";
        }

        // Format the date in a more readable format
        $hour = $timeString % 12;
        $formattedDate = $dateString . " " . $hour . ":00 " . $amOrPm;

        return $formattedDate;

    }



    // Functions for Generating Bar Graph

    // GetDatesOfPastWeek
    // Returns an array with the date from a week ago, the current date,
    // and an array containing all dates within this week
    function GetDatesOfPastWeek()
    {
        $dates = array("","","","","","","");

        // Get Start and End Dates
        $today = strtotime("today");
        $previousWeek = strtotime("-1 week", $today);

        // Format dates to match SQL syntax
        $startDate = date("Y-m-d", $previousWeek) . " " . "00:00:00";
        $endDate = date("Y-m-d") . " " . "00:00:00";

        //fill the array in ascending order (most recent date is last)
        for($i = 0; $i < 7; $i++)
        {
            $dayName = "-1 week +" .$i. " day";
            $dates[$i] = date("Y-m-d", strtotime($dayName, $today));
        }

        return array($startDate, $endDate, $dates);

    }

    // GetTrafficCountForDates
    // Returns an array that contains the traffic count for each day
    // in the input array of dates
    function GetTrafficCountForDates($results, $dates)
    {
        $countByDay = array(0, 0, 0, 0, 0, 0, 0);

        // Fetch results row by row until there are no rows left
        while($row = $results->fetch(PDO::FETCH_ASSOC))
        {
            $index = 6; // last index of array (to get the most recent day)

            // Trim the date to the string "YYYY-MM-DD"
            $startTime = substr($dates[$index], 0, 10);

            // Get the date of the current row and trim to format "YYYY-MM-DD"
            $currentRowDate = substr($row[entryTime], 0, 10);


            // If row date is equal to the last element of the date array,
            // increment the count for the corresponding element
            if($currentRowDate == $startTime)
            {
                $countByDay[$index] = $countByDay[$index] + 1;
            }
            else
            {
                // Find the corresponding position in the count array for the row date
                while($currentRowDate != $startTime && $index > 0)
                {
                    $index = $index - 1;
                    $startTime = substr($dates[$index], 0, 10);
                }

                // Increment the count for the element
                if($index >= 0)
                {
                    $countByDay[$index] = $countByDay[$index] + 1;
                }
            }
        }
        return $countByDay;
    }
}


?>
<!--
<html>
-->
<!-- Latest compiled and minified CSS, jQuery library, and Latest compiled JavaScrip -->
<!--
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<body>
<div class="container">
<div class="card">
<div class="card-body">
-->
<!-- Create a table in which the results will be displayed -->

	<?php /*
		// Get the post values from index.html
		$Time1 = $_POST["Time1"];
		$Time2 = $_POST["Time2"];
		
		// Convert input times into the correct Timestamp format
		$time1Format = ConvertTime($Time1);
		$time2Format = ConvertTime($Time2);

		// Create a new Common instance to connect to the database
		$debug = false;
		$COMMON = new Common($debug);

		// Execute query and fetch the results
		$results = ExecuteQuery($time1Format, $time2Format, $COMMON);
		$row = $results->fetch(PDO::FETCH_ASSOC);

		// Check for empty input from user and display error message if needed
		if($time2Format == " :00" || $time2Format == " :00")
		{
			?>
			<h2 style="text-align: center;">Unfortunately, it seems like you didn't input any values for either Time 1 or Time 2. <a href="./index.html"> Please try again</a>.</h2>
			<?php
		}
		// Check for no results returned and display message if needed
		elseif($row[entryNumber] == "")
		{
			?>
			<h2 style="text-align: center;">Unfortunately, no entries were recorded within that range. <a href="./index.html"> Please try again</a>.</h2>
			<?php
		}
		// Display results within time range
		else
		{
			*/?>

			<!-- Create a table in which the results will be displayed -->
            <!--
			<div class="row">
			<div class="col-s-12 col-md-12 table-responsive">
			<table style="width:100%" class="table tableSection table-bordered">
		  	<tr>
					<th>Entry #</th>
					<th>Time</th>
		  	</tr>  -->

			<?php
                /*
				// Fetch a single row at a time from all of the results
				PrintResults($results);
				
				// Re-execute query to use to generate statistics
				$queryResults = ExecuteQuery($time1Format, $time2Format, $COMMON);
				
				// Group results by hour and generate array with traffic counted in each hour in time range
				$resultsByHour = GroupResultsByHour($queryResults);
				$countByHour = $resultsByHour[0]; // an array containing traffic counted in each hour
				$dateByHour = $resultsByHour[1]; // a corresponding array containing each hour within time range
				
				// Display the minimum number of people per hour
				$minimumInfo = GetMinimumCountInHour($countByHour, $dateByHour);
				$formattedMinTime = ReformatDate($minimumInfo[1]);
				echo("<p>Minimum traffic count in an hour: " . $minimumInfo[0] . " during hour " . $formattedMinTime . "</p>");
				
				// Display the maximum number of people per hour
				$maximumInfo = GetMaximumCountInHour($countByHour, $dateByHour);
				$formattedMaxTime = ReformatDate($maximumInfo[1]);
				echo("<p>Maximum traffic count in an hour: " . $maximumInfo[0] . " during hour " . $formattedMaxTime . "</p>");
				
				// Display the median number of people per hour
				$medianNumberByHour = GetMedianCountInHour($countByHour, $dateByHour);
				$formattedMedianTime = ReformatDate($medianNumberByHour[1]);
				echo("<p>Median traffic count in an hour: " . $medianNumberByHour[0] . " during hour " . $formattedMedianTime . "</p>");
				
				// Display the average number of people per hour
				$averageNumByHour = GetAverageCountInHour($countByHour);
				echo("<p>Average traffic count per hour: " . $averageNumByHour . "</p>");
				?>

			</table>
			</div>
			</div>
			<div class="row">
			<a href="index.html">Go Back</a>
			</div>

			<?php
	  }
	?>

</div>
</div>
</div>
</body>
</html>

<!-- Apply styling to the main webpage -->
<style>
	  body{
		font-family: arial;
	  }
	  table, td{
		text-align: left;
	    <!--background: #ADD8E6;-->
		font-family: arial;
	  }
	table.tableSection {
		display: table;
		width: 100%;
	}
	table.tableSection thead, table.tableSection tbody {
		float: left;
		width: 100%;
	}
	table.tableSection tbody {
		overflow: auto;
		height: 500px;
		width: 100%;
	}
	table.tableSection tr {
		width: 100%;
		display: table;
		text-align: left;
	}
	table.tableSection th, table.tableSection td {
		width: 35%;
	}
</style> */
