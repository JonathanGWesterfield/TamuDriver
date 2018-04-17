<?php
/*****************************************
** File:    index.html
** Project: CSCE 315 Project 1, Spring 2018
** Author:  XXXXXXXXXXX
** Date:    2/2/18
** Section: 505
** E-mail:  XXXXXXXXXXX
**
**   This file contains the main code for the website.
**   This file displays all results in the database. It has the ability to take user input
**   for a selected time range and sends the input to the next page of the web
**   site, PHPtoSQL.php.
**   This file also displays a bar graph showing the traffic counted over the past week.
**
***********************************************/

include('CommonMethods.php');
	
// Create instance to get access to common methods
$COMMON = new Common($debug);
$debug = true;

// Execute query to select all results in the database
$sql = "SELECT * FROM `ArduinoProject1`";
$results = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
  
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
  
// ExecuteQueryWithTimeRange
// Returns the results of a query within a given time range
// to find people counts
function ExecuteQueryWithTimeRange($Time1, $Time2, $COMMON)
{
	$sql = "SELECT * FROM `ArduinoProject1` WHERE `entryTime` BETWEEN '" . $Time1 . "' AND '" . $Time2 . "';"; 
	$results = $COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
	return $results;
}
  
// GetTrafficCountForDates
// Returns an array that contains the traffic count for each day 
// in the input array of dates 
function GetTrafficCountForDates($results, $dates)
{
	$countByDay = array(0,0,0,0,0,0,0);
	
	// Fetch results row by row until there are no rows left
	while($row = $results->fetch(PDO::FETCH_ASSOC)) {
		$index = 6; // last index of array (to get the most recent day)
		
		// Trim the date to the string "YYYY-MM-DD"
		$startTime = substr($dates[$index], 0, 10);

		// Get the date of the current row and trim to format "YYYY-MM-DD"
		$currentRowDate = substr($row[entryTime], 0, 10);
		
		// If row date is equal to the last element of the date array,
		// increment the count for the corresponding element
		if($currentRowDate == $startTime){
			$countByDay[$index] = $countByDay[$index] + 1;
		} 
		else{
		    // Find the corresponding position in the count array for the row date 
			while($currentRowDate != $startTime && $index > 0){
				$index = $index - 1;
				$startTime = substr($dates[$index], 0, 10);
			}
			
			// Increment the count for the element
			if($index >= 0){
				$countByDay[$index] = $countByDay[$index] + 1;
			}
		}
	}
	
	return $countByDay;
}
?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!-- Chart.js CDN for graphing data -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>

<html>
<body>
<div class="container">
<br>
	<div class="jumbotron">
		<h1>CSCE 315 Project 1</h1>
	</div>
<div class="card">
<div class="card-body">

	<!-- Create a table in which the results will be displayed -->
  	<div class="row">
  	<h3>View All Results</h3><br>
	<div class="col-s-12 col-md-12 table-responsive">
		<table class="table tableSection table-bordered">
			<thead>
			  <tr>
				<th>Entry #</th>
				<th>Time</th> 
			  </tr>
			</thead>
			<tbody>
			
			<?php
				$count = 0; // variable to count the number of results
			
				// Fetch a single row at a time from all of the results
				while($row = $results->fetch(PDO::FETCH_ASSOC)) {
			?>
			
              <!-- Display each row as a new entry in the table -->
			  <tr>
				<td><?php echo $row[entryNumber] ?></td>
				<td><?php echo $row[entryTime] ?></td>

				<!-- Update count -->
				<?php $count = $count + 1; ?>
			  </tr>

			<?php  
			  }
			?>
		
			</tbody>
		</table>
		
		<?php 
			// Print out the total traffic counted
			echo '<p>Total traffic count: ' . $count . ' </p>' ;
			
			// Find the dates of the past week
			$timeInfo = GetDatesOfPastWeek();
			$dateOneWeekAgo = $timeInfo[0];
			$dateToday = $timeInfo[1];
			$datesOfPastWeek = $timeInfo[2];
			
			// Get results in time range of past week
			$queryResults = ExecuteQueryWithTimeRange($dateOneWeekAgo, $dateToday, $COMMON);
			
			// Generate array containing traffic counted per day in past week for use in bar graph
			$trafficCountInWeek = GetTrafficCountForDates($queryResults, $datesOfPastWeek);
		 ?>
		
	</div>
  </div>
  
	<!-- Allow user to select a time range -->
	<br><h3>Select a Time Range to View Results</h3>
	<div class="row">
		<div class="col-s-6 col-s-offset-3 col-md-6 col-md-offset-3">
			<br>
			  <b>Please select a time range:</b> <br>
			<form action="PHPtoSQL.php" method="post">
			  <b>FR:</b>
			  <input type="datetime-local" name="Time1">
			  <br>
			  <b>TO:</b>
			  <input type="datetime-local" name="Time2">
			  <input type="submit" value="Submit">
			</form>
			<br>
		</div>
	</div>

</div>
</div>
</div>
<br>
<canvas id="myChart" width="400" height="400"></canvas>
</body>
</html>

<!-- Generate a bar graph showing the traffic counted by day in the past week -->
<script>
var ctx = document.getElementById("myChart");
var dates = <?php echo json_encode($datesOfPastWeek); ?>;
var countByDay = <?php echo json_encode($trafficCountInWeek); ?>;
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: dates, // last 7 days
        datasets: [{
            label: '# of People',
            data: countByDay, // traffic count per day
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(32, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(32, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
		title: {
			display: true,
			text: "Traffic Counted In The Past Week",
			fontSize: 24
		},
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>

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

