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

dolog("This routine moves data from a seperate database into the westocean database for land");    
$regionname = getter("Region-Name");
     
// get the data forthe region
$result=$con->query("select * from grid.regions where regionname='" . $regionname . "' or shortname='" . $shortname . "' and databasename<>'westocean'");

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    dolog("MOving from " . $databasename ." to westocean");
    $con->query("insert into westocean.bakedterrain select * from " . $row["databasename"] . ".bakedterrain");
    $con->query("insert into westocean.land select * from " . $row["databasename"] . ".land");
    $con->query("insert into westocean.landaccesslist select * from " . $row["databasename"] . ".landaccesslist");
    $con->query("insert into westocean.primitems select * from " . $row["databasename"] . ".primitems");
    $con->query("insert into westocean.prims select * from " . $row["databasename"] . ".prims");
    $con->query("insert into westocean.primshapes select * from " . $row["databasename"] . ".primshapes");
    $con->query("insert into westocean.regionban select * from " . $row["databasename"] . ".regionban");
    $con->query("insert into westocean.regionenvironment select * from " . $row["databasename"] . ".regionenvironment");
    $con->query("insert into westocean.regionextra select * from " . $row["databasename"] . ".regionextra");
    $con->query("insert into westocean.regionwindlight select * from " . $row["databasename"] . ".regionwindlight");
    $con->query("insert into westocean.spawn_points select * from " . $row["databasename"] . ".spawn_points");
    $con->query("insert into westocean.terrain select * from " . $row["databasename"] . ".terrain");
    $con->query("update grid.regions set databasename='westocean' where id=" . $row["id"]);
    $con->query("drop database ". $databasename);
    }
} 
 
    
    