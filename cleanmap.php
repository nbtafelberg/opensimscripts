<?php

// default to seave me updating my config
$maptilefolder = "/home/wolfgrid/yeti-211211-robust/bin/maptiles/00000000-0000-0000-0000-000000000000/";
ob_start();
// ** Find theget next available port on theserver
ini_set("display_errors", 1);
ini_set("log_errors", 1);
dolog("Only Run on Robust Server");
error_reporting(E_ALL);

include("config.php");
include("database.php");
#

function dolog($message) {
    $message = date("H:i:s") . " - $message" . PHP_EOL;
    print($message);
    flush();
    ob_flush();
}

function fakeget($param) {
    global $arguments;
    for ($i = 0; $i < count($arguments); ++$i) {
        $key = explode("=", $arguments[$i])[0];
        $value = explode("=", $arguments[$i])[1];
        if ($key === $param) {
            return $value;
        }
    }
    return "";
}

function getUUID() {
    $name_space = "5f6384bfec4ca0b2d4114a13aa2a5435";
    $string = date("hisa");
    $n_hex = str_replace(array('-', '{', '}'), '', $name_space); // Getting hexadecimal components of namespace
    $binary_str = ''; // Binary Value
    //Namespace UUID to bits conversion
    for ($i = 0; $i < strlen($n_hex); $i += 2) {
        $binary_str .= chr(hexdec($n_hex[$i] . $n_hex[$i + 1]));
    }
    //hash value
    $hashing = md5($binary_str . $string);

    return sprintf('%08s-%04s-%04x-%04x-%12s',
            // 32 bits for the time low
            substr($hashing, 0, 8),
            // 16 bits for the time mid
            substr($hashing, 8, 4),
            // 16 bits for the time hi,
            (hexdec(substr($hashing, 12, 4)) & 0x0fff) | 0x3000,
            // 8 bits and 16 bits for the clk_seq_hi_res,
            // 8 bits for the clk_seq_low,
            (hexdec(substr($hashing, 16, 4)) & 0x3fff) | 0x8000,
            // 48 bits for the node
            substr($hashing, 20, 12)
    );
}

function custom_copy($src, $dst) {

    // open the source directory
    $dir = opendir($src);

    // Make the destination directory if not exist
    @mkdir($dst);

    // Loop through the files in source directory
    foreach (scandir($src) as $file) {

        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {

                // Recursively calling custom copy function
                // for sub directory 
                custom_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }

    closedir($dir);
}

function getter($param) {
    $test = fakeget($param) . "";
    if ($test !== "") {
        dolog($param . "=" . $test);
        return $test;
    }
    return readline($param . ":");
    // return filter_input(INPUT_POST, $param);
}

// read through the files inthe maptiles folder
$dir = new DirectoryIterator($maptilefolder);
shell_exec("mkdir " . $maptilefolder . "removed_maptiles");
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $filename = $fileinfo->getFilename();
        dolog("Checking " . $filename);
        // break the filename down into parts
        //  map-1-5007-5028-objects.jpg
        if (strpos($filename, "-") !== false) {
            $mapbits = explode("-", $filename);
            $xpos = $mapbits[2];
            $ypos = $mapbits[3];
            // now look it up in the database
            $sql = "select * from robust.regions where locX/256=" . $xpos . " and locY/256=" . $ypos;
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                while ($row = $result->fetch_assoc()) {
                    dolog("Maptile found at " . $xpos . " " . $ypos . " " . $row["regionName"]);
                }
            } else {
                dolog("Removing " . $filename);
                shell_exec("mv " . $maptilefolder . $filename . " " . $maptilefolder . "removed_maptiles");
            }
        }
    }

}




    $con->close();
    