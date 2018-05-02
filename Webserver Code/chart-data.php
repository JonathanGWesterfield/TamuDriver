<?php
include_once "CommonInterface.php";
include_once "PHPtoSQLInterface.php";

$thisCommon = new Common(true);
$db = new PHPtoSQL($thisCommon);

// Ensure all variables are set before continuing.
if(isset($_GET['mode']) && isset($_GET['day']) && isset($_GET['month']) && isset($_GET['year']) && isset($_GET['loc'])) {
    
    // Store $_GET[] variables in local variables.
    $day   = intval($_GET['day']);
    $month = intval($_GET['month']);
    $year  = intval($_GET['year']);
    $locationChoice = $_GET['loc'];

    $today = getdate();

    // Retrieve data according to mode.
    if(strtolower($_GET['mode']) == 'day') {
        
        $printedFirst = false;
        $data = $db->getTrafficByDay($year, $month, $day, $locationChoice);

        // Trim data if needed.
        if($day == $today['mday'] && $month == $today['mon'] && $year == $today['year']) {
            $data = array_slice($data, 0, $today['hours']+1);
        } else if(($day > $today['mday'] && $month == $today['mon'] && $year == $today['year']) ||
                  ($month > $today['mon'] && $year == $today['year']) ||
                  ($year > $today['year'])) {
            $data = [];
        }

        // Display data.
        foreach($data as $item) {
            if($printedFirst)
                echo "\n";
            echo $item;
            $printedFirst = true;
        }

    } else if(strtolower($_GET['mode']) == 'month') {
        
        $printedFirst = false;
        $data = $db->getTrafficByMonth($year, $month, $locationChoice);

        // Trim data if needed.
        if($month == $today['mon'] && $year == $today['year']) {
            $data = array_slice($data, 0, $today['mday']);
        } else if(($month > $today['mon'] && $year == $today['year']) ||
                  ($year > $today['year'])) {
            $data = [];
        }

        // Display data.
        foreach($data as $item) {
            if($printedFirst)
                echo "\n";
            echo $item;
            $printedFirst = true;
        }

    } else if(strtolower($_GET['mode']) == 'year') {
        
        $printedFirst = false;
        $data = $db->getTrafficByYear($year, $locationChoice);

        // Trim data if needed.
        if($year == $today['year']) {
            $data = array_slice($data, 0, $today['mon']);
        } else if($year > $today['year']) {
            $data = [];
        }

        // Display data.
        foreach($data as $item) {
            if($printedFirst)
                echo "\n";
            echo $item;
            $printedFirst = true;
        }

    }

}