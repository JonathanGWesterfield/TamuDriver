<?php
/**
 * Created by PhpStorm.
 * User: Jabroni
 * Date: 4/20/18 blaze it
 * Time: 6:14 PM
 */

include_once "./CommonMethods.php";

interface CommonInterface
{
    public function Common($debug);
    public function connect($db);
    public function executeQuery($sql, $filename);
}