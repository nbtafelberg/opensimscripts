<?php
ob_start();
include ("config.php");
include ("database.php");
// build multple regions

function getter($param) { 
    $test=filter_input(INPUT_GET,$param) . "";
    if ($test!=="") {
        return $test;
    }
    return readline($param . ":");
   // return filter_input(INPUT_POST, $param);
}


function dolog($message) {
    $message = date("H:i:s") . " - $message" . PHP_EOL;
    print($message);
    flush();
    ob_flush();
}

    $xpos = getter("start-xpos");
    $ypos = getter("start-ypos");
    $regionname = getter("Region-Name");
    $estatename = getter("Estate-Name");
    $shortname = getter("Short-Name");
    $numberofregionsacross=getter("Number-Of-Regions-Across (east to west)");
    $numberofregionsdown=getter("Number-Of-Regions-(south to north)");
    $estateowner=getter("Estate-Owner");
    dolog("Please use westocean as databasename");
    $dbname=getter("dbname");
    if ($dbname==="") {
        $dbname="westocean";
    }
// create region script

$myfile = fopen("create" . $regionname . ".sh", "w");

$numberofregionsacross=$numberofregionsacross*4;
$numberofregionsdown=$numberofregionsdown*4;


$regioncounter=1;
for ($yp=$ypos;$yp<$ypos+$numberofregionsdown;$yp=$yp+4) {
for ($xp=$xpos;$xp<$xpos+$numberofregionsacross;$xp=$xp+4) {

    $sql="insert into grid.regions ";
    $sql.="set servername='" . $thisservername . "',";
    $sql.="xpos='" . $xp . "',";
    $sql.="ypos='" . $yp . "',";
    $sql.="regionname='" . $regionname . " " . $regioncounter . "',";
    $sql.="estatename='" . $estatename . "',";
    $sql.="owner='" . $estateowner . "',";
    $sql.="databasename='" . $dbname . "',";
    $sql.="shortname='" . $shortname . $regioncounter . "'";
    dolog ($sql);
    $con->query($sql);
    $con->query("create database if not exists " . $dbname);
    fwrite($myfile,'php runregion.php "regionname=' . $regionname . ' ' . $regioncounter . PHP_EOL . '"');
    
    ++$regioncounter;





} //x
} //y
    
fclose($myfile);

