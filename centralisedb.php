<?php

function get_column_names($con, $table,$database) {
    $sql = 'DESCRIBE ' . $database . "." . $table;
    $result = mysqli_query($con, $sql);

    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row['Field'];
    }

    return $rows;
}

function copyTable($local, $remote, $table,$database) {
    $columns = get_column_names($local, $table,$database);
    $result = $local->query("select * from " . $database . "." . $table);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $insertsql = "insert into " . $table . " ";
            $data = "";
            foreach ($columns as $column) {
                $data = $data . "'" . mysqli_real_escape_string($local, $row[$column]) . "',";
            }
            $data = substr($data, 0, strlen($data) - 1);
            $insertsql = $insertsql . implode(",", $columns) . " set " . $data;
            log($insertsql);
            mysqli_query($remote, $insertsql);
        }
        dolog("complete");
    } else {
        echo "0 results";
    }
}

function dolog($message) {
    $message = date("H:i:s") . " - $message" . PHP_EOL;
    print($message);
    flush();
    ob_flush();
}

$arguments = $argv;

function fakeget($param) {
    global $arguments;
    foreach ($arguments as $arg) {
        if (strpos($arg, "=") > -1) {
            $arg = str_replace('"', '', $arg);
            $key = explode("=", $arg)[0];
            $value = explode("=", $arg)[1];
            if ($key === $param) {
                return $value;
            }
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

ob_start();
// ** Find theget next available port on theserver
ini_set("display_errors", 1);
ini_set("log_errors", 1);

error_reporting(E_ALL);
include ("config.php");
include ("database.php");

function transferTables($rn) {
    global $con;
    dolog("Moving database to Grid");

//	Get instance of GridCommon.ini in regions folder
    dolog("Edit GridCommon.ini");
    $ldatabase = "";
    $luserid = "";
    $lpassword = "";
    $handle = fopen($rn, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // process the line read.
            $checkfirst = trim($line);
            if (substr($checkfirst, 0, 1) !== ";") {
                if (strpos($line, "ConnectionString = ") !== false) {
                    $stringparts = explode(";", $line);
                    foreach ($stringparts as $item) {
                        if (strpos($item, "Database") !== false) {
                            $bits = explode("=", $item);
                            dolog("Database " . $item);
                            $ldatabase = $bits[1];
                        }
                        if (strpos($item, "User ID") !== false) {
                            dolog("User ID " . $item);
                            $bits = explode("=", $item);
                            $luserid = $bits[1];
                        }
                        if (strpos($item, "Password") !== false) {
                            dolog("Password " . $item);
                            $bits = explode("=", $item);
                            $lpassword = $bits[1];
                        }
                    }
                }
            }
        }

        fclose($handle);
    }
    if ($ldatabase === "" || $lpassword === "" || $luserid === "") {
        dolog("Errror, Insufficient DB info " . $ldatabase . " " . $luserid . " " . $lpassword);
        die();
    }
    if ($luserid==="DBUSER") {
        return; //its a template
    }
    dolog("Database " . $ldatabase . " " . $luserid . " " . $lpassword);
//	Connect to dat$linkabase
    $local = mysqli_connect("localhost", $luserid, $lpassword) or die("Could not Connect");
    mysqli_select_db($con, "robust");
    mysqli_set_charset($con, 'utf8mb4');

    $query = mysqli_query($local, "SHOW TABLES IN " . $ldatabase);
    $numrows = mysqli_num_rows($query);
    dolog("Amount of tables: " . $numrows . " and their names:");
    while ($row = mysqli_fetch_array($query)) {
        dolog($row[0]);
        copyTable($local, $con, $row[0],$ldatabase);
    }
}

$it = new RecursiveDirectoryIterator($regionsfolder);
$display = Array('GridCommon.ini');
foreach (new RecursiveIteratorIterator($it) as $file) {
    if (strpos($file, "GridCommon.ini") !== false && strpos($file, "GridCommon.ini.example") ===false ) {
        dolog($file . "\n");
        transferTables($file);
    }
}