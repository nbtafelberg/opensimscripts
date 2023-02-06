<?php

$con = mysqli_connect($databasehost, $databaseusername, $databasepassword);
mysqli_select_db($con,"robust");
mysqli_set_charset($con, 'utf8mb4');
/**
$localcon= mysqli_connect("127.0.0.1",$localdbusername,$localdbpassword);
mysqli_set_charset($localcon, 'utf8mb4');
**/



function pcGetRecordFrom($db, $query) {

    $r = mysqli_query($db, $query);
    if (!$r) {
        echo "Error in " . $query;
    }
    $data = "";
    $found = False;
    $resultx = "";
    $numrows = mysqli_num_rows($r);
    
    if ($numrows > 0) {
        while ($row = mysqli_fetch_assoc($r)) {
            return $row;
           
        }
    } 
    return $resultx;
}

function pcGetDataFrom($db, $query, $field) {

    $r = mysqli_query($db, $query);
    $data = "";
    $found = False;
    $resultx = "";
    $numrows = mysqli_num_rows($r);
    if ($numrows > 0) {
        while ($row = mysqli_fetch_assoc($r)) {
            $found = True;
            $resultx = $row[$field] . "";

            break;
        }
    } 
    return $resultx;
}
