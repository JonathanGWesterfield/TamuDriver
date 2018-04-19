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

include('CommonMethods.php');

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
	while($row = $results->fetch(PDO::FETCH_ASSOC)) {
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
  	
  	while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
  		// Format first date by "YYYY-MM-DD HH"
  		$formattedRowTime = substr($row[entryTime], 0, 13); 
  		
  		// Increment the element in the count array corresponding to the matching date
  		if ($formattedRowTime == $formattedTimeMarker) {
  			$countByHour[$index] = $countByHour[$index] + 1;
  		}
  		else {
  			$formattedTimeMarker = $formattedRowTime;
  			array_push($dateByHour,$formattedRowTime);
  			array_push($countByHour,1);
  			$index = $index + 1;
  		}
  	}
  	
  	return array($countByHour, $dateByHour);
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
	
	// find middle index of orignal array 
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
  	foreach ($countByHour as $value) {
  		$runningSum = $runningSum + $value;
  	}
  	
  	// Divide by the number of elements in array to compute average
  	$average = $runningSum / sizeof($countByHour);
  	
  	return $average;
	  	
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

?>

<html>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<body>
<div class="container">
<div class="card">
<div class="card-body">
<!-- Create a table in which the results will be displayed -->

	<?php
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
			?>

			<!-- Create a table in which the results will be displayed -->
			<div class="row">
			<div class="col-s-12 col-md-12 table-responsive">
			<table style="width:100%" class="table tableSection table-bordered">
		  	<tr>
					<th>Entry #</th>
					<th>Time</th>
		  	</tr>

			<?php
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
</style>
