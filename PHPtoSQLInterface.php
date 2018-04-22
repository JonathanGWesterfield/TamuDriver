<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 4/22/18
 * Time: 2:58 PM
 */

interface PHPtoSQLInterface
{
    public function GetMinimumCountInHour($countByHour, $dateByHour);
    public function GetMedianCountInHour($countByHour, $dateByHour);
    public function GetMaximumCountInHour($countByHour, $dateByHour);
    public function GetAverageCountInHour($countByHour);
    public function getNumWalkersThisWeek();
    public function getTrafficByYear($year);
    public function getTrafficByMonth($year, $month);
    public function getTrafficByDay($year, $month, $day);
    public function getTrafficTimeRange($year1, $month1, $day1, $year2, $month2, $day2);




}