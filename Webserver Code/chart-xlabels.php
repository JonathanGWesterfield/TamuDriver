<?php
include_once "CommonInterface.php";
include_once "PHPtoSQLInterface.php";

$thisCommon = new Common(true);
$db = new PHPtoSQL($thisCommon);

// Return the labels for the chart's x-axis. Results vary by input date and mode.
if(isset($_GET['mode']) && isset($_GET['day']) && isset($_GET['month']) && isset($_GET['year'])) {
    
    $day   = intval($_GET['day']);
    $month = intval($_GET['month']);
    $year  = intval($_GET['year']);

    if(strtolower($_GET['mode']) == 'day') {
        
        // If mode is day, then output all hours in the day.
        $xlabels = ["12am", "1am", "2am", "3am", "4am", "5am", "6am", "7am", "8am", "9am",
            "10am", "11am", "12pm", "1pm", "2pm", "3pm", "4pm", "5pm", "6pm", "7pm",
            "8pm", "9pm", "10pm", "11pm"];

            $printedFirst = false;
            foreach($xlabels as $item) {
                if($printedFirst)
                    echo "\n";
                echo $item;
                $printedFirst = true;
            }

    } else if(strtolower($_GET['mode']) == 'month') {
        
        // If mode is month, then output all dates in the month.
        $xlabels = range(1,cal_days_in_month(CAL_GREGORIAN, $month, $year));

        $printedFirst = false;
        foreach($xlabels as $item) {
            if($printedFirst)
                echo "\n";
            echo $item;
            $printedFirst = true;
        }

    } else if(strtolower($_GET['mode']) == 'year') {
        
        // If mode is year, then output all months in the year.
        $xlabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        $printedFirst = false;
        foreach($xlabels as $item) {
            if($printedFirst)
                echo "\n";
            echo $item;
            $printedFirst = true;
        }

    }
}